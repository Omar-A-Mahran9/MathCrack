<!doctype html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Students Test Results</title>

    <link rel="stylesheet" href="{{ asset('back-assets/css/bootstrap.min.css') }}">

    <style>
        @page {
            size: A4 landscape;
            margin: 10mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            padding: 18px;
            font-family: Arial, sans-serif;
            color: #000;
            background: #fff;
        }

        .no-print {
            margin-bottom: 14px;
            display: flex;
            gap: 8px;
            justify-content: flex-start;
        }

        .btn {
            border-radius: 8px;
            font-weight: 700;
            padding: 8px 16px;
        }

        .print-header {
            text-align: center;
            margin-bottom: 18px;
        }

        .print-logo {
            width: 210px;
            height: auto;
            margin-bottom: 10px;
        }

        .divider {
            width: 100%;
            height: 4px;
            background-color: #d90404;
            margin: 8px auto 18px;
        }

        h2 {
            text-align: center;
            margin: 0 0 12px;
            font-size: 24px;
            font-weight: 800;
        }

        .info-box {
            display: flex;
            justify-content: center;
            gap: 28px;
            flex-wrap: wrap;
            margin-bottom: 18px;
            font-size: 15px;
            line-height: 1.5;
        }

        .info-box .line {
            display: inline-flex;
            gap: 6px;
            font-weight: 600;
        }

        .summary-row {
            display: flex;
            justify-content: center;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 16px;
        }

        .summary-item {
            border: 1px solid #000;
            padding: 6px 12px;
            font-size: 13px;
            font-weight: 700;
            min-width: 120px;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse !important;
            font-size: 12.5px;
            table-layout: fixed;
        }

        th,
        td {
            border: 1px solid #000 !important;
            padding: 7px 6px !important;
            text-align: center !important;
            vertical-align: middle !important;
            word-wrap: break-word;
            white-space: normal;
        }

        thead th {
            background-color: #f2f2f2 !important;
            font-weight: 800;
        }

        tbody tr {
            page-break-inside: avoid;
        }

        .student-name {
            text-align: left !important;
            font-weight: 700;
        }

        .status {
            font-weight: 700;
        }

        .score {
            font-weight: 800;
        }

        .print-footer {
            margin-top: 14px;
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            color: #333;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            body {
                padding: 0 !important;
                margin: 0 !important;
            }

            .print-header {
                margin-bottom: 12px;
            }

            .print-logo {
                width: 190px;
            }

            h2 {
                font-size: 22px;
            }

            table {
                font-size: 11.5px;
            }

            th,
            td {
                padding: 6px 5px !important;
            }

            .summary-item {
                font-size: 12px;
                padding: 5px 10px;
            }
        }
    </style>
</head>

<body>
@php
    $firstRow = $rows->first();

    $totalStudents = $rows->count();
    $completedCount = $rows->filter(function ($row) {
        return strtolower((string) $row->status) === 'completed';
    })->count();

    $totalCorrect = $rows->sum(function ($row) {
        return (int) ($row->correct_answers ?? 0);
    });

    $totalWrong = $rows->sum(function ($row) {
        return (int) ($row->wrong_answers ?? 0);
    });
@endphp

<div class="no-print">
    <button class="btn btn-primary" onclick="window.print()">Print</button>
    <button class="btn btn-secondary" onclick="window.close()">Close</button>
</div>

<div class="print-header">
    <img src="{{ asset('assets/themes/default/front/images/logo.png') }}" class="print-logo" alt="Logo">
    <div class="divider"></div>
</div>

<h2>Students Test Results</h2>

<div class="info-box">
    <span class="line">
        <strong>Course:</strong>
        <span>{{ $firstRow->course_name ?? '-' }}</span>
    </span>

    <span class="line">
        <strong>Test:</strong>
        <span>{{ $firstRow->test_name ?? '-' }}</span>
    </span>
</div>

<div class="summary-row">
    <div class="summary-item">Students: {{ $totalStudents }}</div>
    <div class="summary-item">Completed: {{ $completedCount }}</div>
    <div class="summary-item">Correct: {{ $totalCorrect }}</div>
    <div class="summary-item">Wrong: {{ $totalWrong }}</div>
</div>

<table>
    <thead>
        <tr>
            <th style="width: 5%;">No</th>
            <th style="width: 18%;">Student</th>
            <th style="width: 10%;">Last Attempt</th>
            <th style="width: 11%;">Status</th>
            <th style="width: 9%;">Score</th>
            <th style="width: 10%;">Total Score</th>
            <th style="width: 24%;">Start Time</th>
            <th style="width: 6%;">Correct</th>
            <th style="width: 7%;">Wrong</th>
        </tr>
    </thead>

    <tbody>
        @forelse($rows as $r)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td class="student-name">{{ $r->student_name }}</td>
                <td>{{ $r->last_attempt }}</td>
                <td class="status">{{ ucfirst($r->status) }}</td>
                <td class="score">{{ $r->final_score }}</td>
                <td>{{ $r->test_total_score }}</td>
                <td>{{ $r->started_at }}</td>
                <td>{{ $r->correct_answers ?? 0 }}</td>
                <td>{{ $r->wrong_answers ?? 0 }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="9">No results available.</td>
            </tr>
        @endforelse
    </tbody>
</table>

<div class="print-footer">
    <span>Generated at: {{ now()->format('Y-m-d H:i') }}</span>
    <span>MathCrack</span>
</div>

</body>
</html>
