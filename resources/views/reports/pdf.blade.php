<!DOCTYPE html>
<html>

<head>
    <title>Laporan Sistem</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        h1 {
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }

        th {
            background-color: #f0f0f0;
        }
    </style>
</head>

<body>
    @can('view reports')

        <h1>Laporan Aktivitas Sistem</h1>

        <p>
            @if (!empty($start) && !empty($end))
                Periode: {{ \Carbon\Carbon::parse($start)->format('d M Y') }} —
                {{ \Carbon\Carbon::parse($end)->format('d M Y') }}
            @else
                Periode: Semua data
            @endif
        </p>

        <h2>Daftar Proyek</h2>
        <table>
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Ketua Tim</th>
                    <th>Status</th>
                    <th>Deadline</th>
                    <th>Total Tugas</th>
                    <th>Dibuat Pada</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($projects as $project)
                    <tr>
                        <td>{{ $project->name }}</td>
                        <td>{{ $project->teamLeader->name ?? '-' }}</td>
                        <td>{{ $project->status }}</td>
                        <td>{{ $project->deadline ? \Carbon\Carbon::parse($project->deadline)->format('d M Y') : '-' }}</td>
                        <td>{{ $project->tasks->count() }}</td>
                        <td>{{ optional($project->created_at)->format('d M Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align:center;">Tidak ada proyek pada periode ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <h2 style="margin-top:18px;">Daftar Tugas</h2>
        <table>
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Ditugaskan Kepada</th>
                    <th>Proyek</th>
                    <th>Status</th>
                    <th>Deadline</th>
                    <th>Dibuat Pada</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tasks as $task)
                    <tr>
                        <td>{{ $task->name }}</td>
                        <td>{{ $task->assignee->name ?? '-' }}</td>
                        <td>{{ $task->project->name ?? '-' }}</td>
                        <td>{{ $task->status }}</td>
                        <td>{{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('d M Y') : ($task->deadline ? \Carbon\Carbon::parse($task->deadline)->format('d M Y') : '-') }}
                        </td>
                        <td>{{ optional($task->created_at)->format('d M Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align:center;">Tidak ada tugas pada periode ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <p style="margin-top:18px;">
            Dibuat pada: {{ $generated_at->format('d M Y') }}
        </p>

    @endcan
</body>

</html>
