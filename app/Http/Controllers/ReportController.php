<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\Title;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        $this->middleware('permission:view reports')->only(['index']);
        $this->middleware('permission:export reports pdf')->only(['exportPdf']);
        $this->middleware('permission:export reports excel')->only(['exportExcel']);
    }

    public function index(Request $request)
    {
        $user = auth()->user();

        $start = $request->input('start_date');
        $end = $request->input('end_date');

        $projects = Project::with(['teamLeader', 'tasks'])
            ->when($start && $end, fn($q) => $q->whereBetween('created_at', [$start, $end]))
            ->get();

        $tasks = Task::with(['assignee', 'project'])
            ->when($start && $end, fn($q) => $q->whereBetween('created_at', [$start, $end]))
            ->get();

        $totalProjects = $projects->count();
        $completedProjects = $projects->where('status', 'Completed')->count();
        $ongoingProjects = $projects->where('status', '!=', 'Completed')->count();

        $totalTasks = $tasks->count();
        $completedTasks = $tasks->where('status', 'Completed')->count();
        $pendingTasks = $tasks->where('status', 'Pending')->count();
        $inProgressTasks = $tasks->where('status', 'In Progress')->count();
        $overdueTasks = $tasks->where('due_date', '<', now())->where('status', '!=', 'Completed')->count();

        $avgProgress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 1) : 0;

        $priorityStats = [
            'High' => $tasks->where('priority', 'high')->count(),
            'Medium' => $tasks->where('priority', 'medium')->count(),
            'Low' => $tasks->where('priority', 'low')->count(),
        ];

        $teamLeaders = User::role('ketua_tim')->count();
        $teamMembers = User::role('anggota_tim')->count();

        $userStats = User::withCount([
            'tasks as total_tasks' => fn($query) => $query->when($start && $end, fn($q) => $q->whereBetween('created_at', [$start, $end])),
            'tasks as completed_tasks' => fn($query) => $query->where('status', 'Completed')->when($start && $end, fn($q) => $q->whereBetween('created_at', [$start, $end])),
        ])->get();

        $activityData = Task::selectRaw('DATE(created_at) as tanggal, COUNT(*) as total')
            ->when($start && $end, fn($q) => $q->whereBetween('created_at', [$start, $end]))
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'asc')
            ->get();

        $tasksByDate = $activityData;

        return view('reports.index', compact(
            'totalProjects',
            'completedProjects',
            'ongoingProjects',
            'totalTasks',
            'completedTasks',
            'pendingTasks',
            'inProgressTasks',
            'overdueTasks',
            'userStats',
            'activityData',
            'tasksByDate',
            'avgProgress',
            'priorityStats',
            'teamLeaders',
            'teamMembers',
            'projects',
            'start',
            'end'
        ));
    }

    public function exportPdf(Request $request)
    {
        $user = auth()->user();

        $start = $request->input('start_date');
        $end = $request->input('end_date');

        $startDate = $start ? Carbon::parse($start)->startOfDay() : null;
        $endDate = $end ? Carbon::parse($end)->endOfDay() : null;

        $projects = Project::with(['teamLeader', 'tasks'])
            ->when($startDate && $endDate, fn($q) => $q->whereBetween('created_at', [$startDate, $endDate]))
            ->get();

        $tasks = Task::with(['assignee', 'project'])
            ->when($startDate && $endDate, fn($q) => $q->whereBetween('created_at', [$startDate, $endDate]))
            ->get();

        $users = User::all();

        $data = [
            'projects' => $projects,
            'tasks' => $tasks,
            'users' => $users,
            'generated_at' => now(),
            'start' => $start,
            'end' => $end,
        ];

        $pdf = Pdf::loadView('reports.pdf', $data);
        return $pdf->download('laporan-' . now()->format('Y-m-d') . '.pdf');
    }

    public function exportExcel(Request $request)
    {
        $user = auth()->user();

        $start = $request->input('start_date');
        $end = $request->input('end_date');

        $startDate = $start ? Carbon::parse($start)->startOfDay() : null;
        $endDate = $end ? Carbon::parse($end)->endOfDay() : null;

        $projects = Project::with(['tasks', 'teamLeader', 'creator', 'owner'])
            ->when($startDate && $endDate, fn($q) => $q->whereBetween('created_at', [$startDate, $endDate]))
            ->get();

        $tasks = Task::with(['project', 'assignee'])
            ->when($startDate && $endDate, fn($q) => $q->whereBetween('created_at', [$startDate, $endDate]))
            ->get();

        $spreadsheet = new Spreadsheet();

        $overview = $spreadsheet->getActiveSheet();
        $overview->setTitle('Overview');

        $overview->setCellValue('A1', 'OVERVIEW PROYEK DAN TUGAS');
        $overview->mergeCells('A1:K1');
        $overview->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $overview->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $totalProjects = $projects->count();
        $totalTasks = $tasks->count();
        $completedTasks = $tasks->where('status', 'Completed')->count();
        $pendingTasks = $tasks->where('status', 'Pending')->count();
        $inProgressTasks = $tasks->where('status', 'In Progress')->count();
        $notCompletedTasks = $totalTasks - $completedTasks;


        $overview->setCellValue('A3', 'Jumlah Proyek');
        $overview->setCellValue('B3', $totalProjects);
        $overview->setCellValue('A4', 'Jumlah Tugas');
        $overview->setCellValue('B4', $totalTasks);
        $overview->setCellValue('A5', 'Tugas Selesai');
        $overview->setCellValue('B5', $completedTasks);
        $overview->setCellValue('A6', 'Tugas Dalam Progress');
        $overview->setCellValue('B6', $inProgressTasks);
        $overview->setCellValue('A7', 'Tugas Tertunda');
        $overview->setCellValue('B7', $pendingTasks);
        $overview->setCellValue('A8', 'Periode');
        $periode = ($start && $end)
            ? $start . ' s/d ' . $end
            : 'Seluruh Periode';
        $overview->setCellValue('B8', $periode);
        $overview->setCellValue('A9', 'Dibuat Pada');
        $overview->setCellValue('B9', now()->format('d-m-Y H:i'));
        $overview->getStyle('A11:B11')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $overview->getStyle('D11:E11')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $overview->getStyle('B3:B9')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $overview->getStyle('B12:B14')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $overview->getStyle('E11:E14')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $overview->setCellValue('A11', 'Status');
        $overview->setCellValue('B11', 'Jumlah');
        $overview->setCellValue('A12', 'Selesai');
        $overview->setCellValue('B12', $completedTasks);
        $overview->setCellValue('A13', 'Belum Selesai');
        $overview->setCellValue('B13', $notCompletedTasks);

        $progressByDate = $projects
            ->groupBy(fn($p) => $p->created_at ? $p->created_at->format('Y-m-d') : 'N/A')
            ->map(function ($group) {
                $avg = $group->avg(fn($p) => is_numeric($p->progress) ? $p->progress : 0);
                return round($avg, 2);
            })
            ->filter(fn($v, $k) => $k !== 'N/A')
            ->sortKeys(); 

        $chartStartRow = 11;
        $overview->setCellValue("D{$chartStartRow}", 'Tanggal');
        $overview->setCellValue("E{$chartStartRow}", 'Rata-Rata Progress (%)');


        $r = $chartStartRow + 1;
        foreach ($progressByDate as $date => $avgProgress) {
            $overview->setCellValue("D{$r}", Carbon::parse($date)->format('d-m-Y'));
            $overview->setCellValue("E{$r}", (float) $avgProgress);
            $r++;
        }
        $chartDataStartRow = $chartStartRow + 1;
        $chartDataEndRow = max($chartStartRow + 1, $r - 1);

        foreach (range('A', 'F') as $c) {
            $overview->getColumnDimension($c)->setAutoSize(true);
        }
        foreach (['D', 'E'] as $c) {
            $overview->getColumnDimension($c)->setAutoSize(true);
        }

        $pieLabels = [new DataSeriesValues('String', "'Overview'!\$A\$12"), new DataSeriesValues('String', "'Overview'!\$A\$13")];
        $pieValues = [new DataSeriesValues('Number', "'Overview'!\$B\$12:\$B\$13")];


        $pieSeries = new DataSeries(
            DataSeries::TYPE_PIECHART,
            null,
            range(0, count($pieValues) - 1),
            $pieLabels,
            [],
            $pieValues
        );
        $piePlotArea = new PlotArea(null, [$pieSeries]);
        $pieLegend = new Legend(Legend::POSITION_RIGHT, null, false);
        $pieTitle = new Title('Status Tugas');


        $pieChart = new Chart('pie_chart', $pieTitle, $pieLegend, $piePlotArea, true);
        $pieChart->setTopLeftPosition('A16');
        $pieChart->setBottomRightPosition('C28');
        $overview->addChart($pieChart);

        if ($chartDataEndRow >= $chartDataStartRow) {
            $labelRange = "'Overview'!\$D\${$chartDataStartRow}:\$D\${$chartDataEndRow}";
            $valueRange = "'Overview'!\$E\${$chartDataStartRow}:\$E\${$chartDataEndRow}";


            $dataSeriesLabels = [new DataSeriesValues('String', "'Overview'!\$E\${$chartStartRow}")];
            $xAxisTickValues = [new DataSeriesValues('String', $labelRange)];
            $dataSeriesValues = [new DataSeriesValues('Number', $valueRange)];


            $lineSeries = new DataSeries(
                DataSeries::TYPE_LINECHART,
                null,
                range(0, count($dataSeriesValues) - 1),
                $dataSeriesLabels,
                $xAxisTickValues,
                $dataSeriesValues
            );
            $linePlotArea = new PlotArea(null, [$lineSeries]);
            $lineLegend = new Legend(Legend::POSITION_RIGHT, null, false);
            $lineTitle = new Title('Rata-Rata Progress Proyek');


            $lineChart = new Chart('line_chart', $lineTitle, $lineLegend, $linePlotArea, true);
            $lineChart->setTopLeftPosition('D16');
            $lineChart->setBottomRightPosition('K28');
            $overview->addChart($lineChart);
        }

        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Projects');

        $sheet2->setCellValue('A1', 'DETAIL PROYEK');
        $sheet2->mergeCells('A1:L1');
        $sheet2->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet2->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $projectHeaders = [
            'A3' => 'ID',
            'B3' => 'Name',
            'C3' => 'Description',
            'D3' => 'Deadline',
            'E3' => 'Priority',
            'F3' => 'Status',
            'G3' => 'Progress (%)',
            'H3' => 'Team Leader',
            'I3' => 'Total Tasks',
            'J3' => 'Created By',
            'K3' => 'Created At',
            'L3' => 'Updated At',
        ];
        foreach ($projectHeaders as $cell => $text) {
            $sheet2->setCellValue($cell, $text);
        }

        $sheet2->getStyle('A3:L3')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFDCE6F1']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        $row = 4;
        foreach ($projects as $p) {
            $sheet2->setCellValue("A{$row}", $p->id);
            $sheet2->setCellValue("B{$row}", $p->name);
            $sheet2->setCellValue("C{$row}", $p->description ?? '-');

            if ($p->deadline) {
                $dt = Carbon::parse($p->deadline);
                $sheet2->setCellValue("D{$row}", $dt->format('d-m-Y'));
            } else {
                $sheet2->setCellValue("D{$row}", '-');
            }


            $sheet2->setCellValue("E{$row}", strtoupper($p->priority) ?? '-');
            $sheet2->setCellValue("F{$row}", $p->status ?? '-');
            $sheet2->setCellValue("G{$row}", is_numeric($p->progress) ? (float)$p->progress : 0);
            $sheet2->setCellValue("H{$row}", optional($p->teamLeader)->name ?? ($p->team_leader_id ?? '-'));
            $sheet2->setCellValue("I{$row}", $p->tasks->count());
            $sheet2->setCellValue("J{$row}", optional($p->creator)->name ?? ($p->created_by ?? '-'));


            if ($p->created_at) {
                $sheet2->setCellValue("K{$row}", $p->created_at->format('d-m-Y H:i'));
            }
            if ($p->updated_at) {
                $sheet2->setCellValue("L{$row}", $p->updated_at->format('d-m-Y H:i'));
            }

            $fillColor = ($row % 2 == 0) ? 'FFFFFFFF' : 'FFF7FBFF';
            $sheet2->getStyle("A{$row}:L{$row}")->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $fillColor]],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);


            $row++;
        }

        foreach (range('A', 'L') as $col) {
            $sheet2->getColumnDimension($col)->setAutoSize(true);
        }

        $sheet3 = $spreadsheet->createSheet();
        $sheet3->setTitle('Tasks');

        $sheet3->setCellValue('A1', 'DETAIL TUGAS');
        $sheet3->mergeCells('A1:L1');
        $sheet3->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet3->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);


        $taskHeaders = [
            'A3' => 'ID',
            'B3' => 'Task Name',
            'C3' => 'Description',
            'D3' => 'Assignee',
            'E3' => 'Due Date',
            'F3' => 'Priority',
            'G3' => 'Status',
            'H3' => 'Project',
            'I3' => 'Project Deadline',
            'J3' => 'Completed At',
            'K3' => 'Created At',
            'L3' => 'Updated At',
        ];
        foreach ($taskHeaders as $cell => $text) {
            $sheet3->setCellValue($cell, $text);
        }


        $sheet3->getStyle('A3:L3')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFDCE6F1']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        $row = 4;
        foreach ($tasks as $t) {
            $sheet3->setCellValue("A{$row}", $t->id);
            $sheet3->setCellValue("B{$row}", $t->name);
            $sheet3->setCellValue("C{$row}", $t->description ?? '-');
            $sheet3->setCellValue("D{$row}", optional($t->assignee)->name ?? ($t->assigned_to ?? '-'));
            if ($t->due_date) {
                $sheet3->setCellValue("E{$row}", Carbon::parse($t->due_date)->format('d-m-Y'));
            } else {
                $sheet3->setCellValue("E{$row}", '-');
            }
            $sheet3->setCellValue("F{$row}", strtoupper($t->priority) ?? '-');
            $sheet3->setCellValue("G{$row}", $t->status ?? '-');
            $sheet3->setCellValue("H{$row}", optional($t->project)->name ?? ($t->project_id ?? '-'));

            if ($t->project && $t->project->deadline) {
                $sheet3->setCellValue("I{$row}", Carbon::parse($t->project->deadline)->format('d-m-Y'));
            } else {
                $sheet3->setCellValue("I{$row}", '-');
            }


            if ($t->completed_at) {
                $sheet3->setCellValue("J{$row}", Carbon::parse($t->completed_at)->format('d-m-Y H:i'));
            } else {
                $sheet3->setCellValue("J{$row}", '-');
            }
            if ($t->created_at) {
                $sheet3->setCellValue("K{$row}", $t->created_at->format('d-m-Y H:i'));
            }
            if ($t->updated_at) {
                $sheet3->setCellValue("L{$row}", $t->updated_at->format('d-m-Y H:i'));
            }

            $fillColor = ($row % 2 == 0) ? 'FFFFFFFF' : 'FFFDF7F0';
            $sheet3->getStyle("A{$row}:L{$row}")->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $fillColor]],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);


            $row++;
        }

        foreach (range('A', 'L') as $col) {
            $sheet3->getColumnDimension($col)->setAutoSize(true);
        }

        $fileName = 'laporan-sistem-' . now()->format('Ymd_His') . '.xlsx';
        $spreadsheet->setActiveSheetIndex(0);

        $writer = new Xlsx($spreadsheet);
        $writer->setIncludeCharts(true);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            "Content-Type" => "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
        ]);
    }
}
