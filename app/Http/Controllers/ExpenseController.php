<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        $this->middleware('permission:view financial')->only('index');

        $this->middleware('permission:submit expense')->only('store');

        $this->middleware('permission:approve expense|reject expense')
            ->only(['approve', 'reject']);
    }

    public function index()
    {
        /** @var User $user */
        $user = auth()->user();

        $expensesQuery = Expense::with(['project', 'user']);

        if ($user->can('view financial') && $user->hasRole('admin')) {
            $expensesQuery = Expense::with(['project', 'user']);
        } elseif ($user->can('view own projects') && $user->hasRole('ketua_tim')) {
            $expensesQuery = Expense::with(['project', 'user'])
                ->whereHas('project', fn($q) => $q->where('team_leader_id', $user->id));
        } elseif ($user->can('view assigned tasks') && $user->hasRole('anggota_tim')) {
            $expensesQuery = Expense::with(['project', 'user'])
                ->whereHas('project.tasks', fn($q) => $q->where('assigned_to', $user->id));
        } else {
            $expensesQuery = Expense::whereRaw('1=0');
        }

        $expenses = $expensesQuery->latest()->paginate(10);

        return view('financial.index', compact('expenses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'project_id'   => 'required|exists:projects,id',
            'category'     => 'required|string',
            'amount'       => 'required|numeric|min:1',
            'expense_date' => 'required|date',
            'description'  => 'nullable|string|max:500',
        ]);

        /** @var User $user */
        $user    = auth()->user();
        $project = Project::findOrFail($request->project_id);

        if ($user->can('view own projects') && $user->hasRole('ketua_tim') && $project->team_leader_id !== $user->id) {
            abort(403, 'Anda bukan ketua proyek ini.');
        }

        if ($user->can('view assigned tasks') && $user->hasRole('anggota_tim')) {
            $hasTask = $project->tasks()->where('assigned_to', $user->id)->exists();
            if (!$hasTask) {
                abort(403, 'Anda bukan anggota proyek ini.');
            }
        }

        Expense::create([
            'project_id'   => $project->id,
            'user_id'      => $user->id,
            'category'     => $request->category,
            'amount'       => $request->amount,
            'description'  => $request->description,
            'expense_date' => $request->expense_date,
            'status'       => 'pending',
        ]);

        return back()->with('success', 'Pengajuan pengeluaran berhasil dikirim.');
    }

    public function approve(Expense $expense)
    {
        $this->authorizeApproval($expense);

        $project = $expense->project;

        $totalApproved = Expense::where('project_id', $project->id)
            ->where('status', 'approved')
            ->sum('amount');

        $remainingBudget = $project->budget - $totalApproved;

        if ($expense->amount > $remainingBudget) {
            return back()->with(
                'error',
                'Gagal menyetujui: sisa budget proyek tidak mencukupi.'
            );
        }

        $expense->update([
            'status'      => 'approved',
            'approved_by' => auth()->id(),
        ]);

        return back()->with('success', 'Pengeluaran berhasil disetujui.');
    }

    public function reject(Expense $expense)
    {
        $this->authorizeApproval($expense);

        $expense->update([
            'status'      => 'rejected',
            'approved_by' => auth()->id(),
        ]);

        return back()->with('success', 'Pengeluaran berhasil ditolak.');
    }

    private function authorizeApproval(Expense $expense)
    {
        /** @var User $user */
        $user = auth()->user();

        if ($user->can('approve expense') && $user->hasRole('admin')) {
            return true;
        }

        if ($user->can('approve expense') && $user->hasRole('ketua_tim') && $expense->project->team_leader_id === $user->id) {
            return true;
        }

        abort(403, 'Anda tidak memiliki izin untuk aksi ini.');
    }
}
