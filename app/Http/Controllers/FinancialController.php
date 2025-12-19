<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\Project;
use App\Models\User;

class FinancialController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        $this->middleware('permission:view financial')->only('index');

        $this->middleware('permission:submit expense')->only('store');

        $this->middleware('permission:approve expense|reject expense')
            ->only(['approve', 'reject']);
    }

    public function index(Request $request)
    {
        /** @var User $user */
        $user  = auth()->user();
        $start = $request->input('start_date');
        $end   = $request->input('end_date');

        $expensesQuery = Expense::with(['project', 'user'])
            ->when($start && $end, function ($q) use ($start, $end) {
                $q->whereBetween('expense_date', [$start, $end]);
            });

        if ($user->hasRole('admin')) {
        } elseif ($user->hasRole('ketua_tim')) {
            $expensesQuery->whereHas('project', function ($q) use ($user) {
                $q->where('team_leader_id', $user->id);
            });
        } elseif ($user->hasRole('anggota_tim')) {
            $expensesQuery->whereHas('project.tasks', function ($q) use ($user) {
                $q->where('assigned_to', $user->id);
            });
        } else {
            $expensesQuery->whereRaw('1=0');
        }

        $expenses = $expensesQuery->latest()->get();

        $approvedExpenses = $expenses->where('status', 'approved');
        $totalExpense     = $approvedExpenses->sum('amount');

        $projects = Project::when($user->hasRole('ketua_tim'), function ($q) use ($user) {
            $q->where('team_leader_id', $user->id);
        })->when($user->hasRole('anggota_tim'), function ($q) use ($user) {
            $q->whereHas('tasks', fn($q2) => $q2->where('assigned_to', $user->id));
        })->get();

        $totalBudget     = $projects->sum('budget');
        $remainingBudget = $totalBudget - $totalExpense;

        $expenseStatus = [
            'approved' => $expenses->where('status', 'approved')->count(),
            'pending'  => $expenses->where('status', 'pending')->count(),
            'rejected' => $expenses->where('status', 'rejected')->count(),
        ];

        return view('financial.index', compact(
            'expenses',
            'projects',
            'totalExpense',
            'totalBudget',
            'remainingBudget',
            'expenseStatus',
            'start',
            'end'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'project_id'   => 'required|exists:projects,id',
            'category'     => 'required|string',
            'amount'       => 'required|numeric|min:1',
            'expense_date' => 'required|date',
        ]);

        /** @var User $user */
        $user    = auth()->user();
        $project = Project::findOrFail($request->project_id);

        if ($user->hasRole('ketua_tim') && $project->team_leader_id !== $user->id) {
            abort(403, 'Anda bukan ketua proyek ini.');
        }

        if ($user->hasRole('anggota_tim') && !$project->tasks()->where('assigned_to', $user->id)->exists()) {
            abort(403, 'Anda bukan anggota proyek ini.');
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

        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasRole('ketua_tim') && $expense->project->team_leader_id === $user->id) {
            return true;
        }

        abort(403, 'Anda tidak memiliki izin untuk aksi ini.');
    }
}
