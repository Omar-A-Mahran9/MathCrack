@extends('themes.default.layouts.back.student-master')

@section('title')
    {{ $test->name }}
@endsection

@section('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        :root{
            --mc-primary:#1e40af;
            --mc-primary-2:#3b82f6;
            --mc-success:#10b981;
            --mc-success-2:#059669;
            --mc-warn:#f59e0b;
            --mc-warn-2:#d97706;
            --mc-gray:#6b7280;
            --mc-gray-2:#4b5563;
            --mc-border:#e5e7eb;
            --mc-bg:#f8fafc;
            --mc-text:#1f2937;
        }

        .mc-wrap{ padding-bottom: 30px; }

        .test-hero{
            background: linear-gradient(135deg, var(--mc-primary) 0%, var(--mc-primary-2) 100%);
            color:#fff;
            padding:40px 0;
            margin-bottom:30px;
            border-radius:15px;
            position:relative;
            overflow:hidden;
        }
        .test-hero::before{
            content:'';
            position:absolute;
            top:0;
            right:-100px;
            width:200px;
            height:100%;
            background:rgba(255,255,255,.1);
            transform:skewX(-15deg);
        }
        .test-hero h1{
            font-size:2.2rem;
            font-weight:700;
            margin:0 0 8px 0;
            color:#fff !important;
        }
        .test-hero p{
            font-size:1.05rem;
            opacity:.95;
            margin:0;
            color:#fff !important;
        }

        .test-info-card,
        .status-section,
        .previous-attempts{
            background:#fff;
            border-radius:15px;
            box-shadow:0 4px 15px rgba(0,0,0,.08);
            overflow:hidden;
        }

        .card-header-custom{
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            padding:18px 22px;
            border-bottom:1px solid var(--mc-border);
        }
        .card-header-custom h3{
            margin:0;
            color:var(--mc-text);
            font-size:1.2rem;
            font-weight:700;
        }
        .card-body-custom{ padding:22px; }

        .info-grid{
            display:grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap:16px;
            margin-bottom:22px;
        }
        .info-item{
            background:var(--mc-bg);
            padding:14px;
            border-radius:12px;
            border-left:4px solid var(--mc-primary-2);
        }
        [dir="rtl"] .info-item{
            border-left:0;
            border-right:4px solid var(--mc-primary-2);
        }
        .info-label{
            font-size:.82rem;
            color:var(--mc-gray);
            font-weight:700;
            letter-spacing:.4px;
            margin-bottom:6px;
        }
        .info-value{
            font-size:1.05rem;
            color:var(--mc-text);
            font-weight:700;
        }

        .test-description{
            background:var(--mc-bg);
            padding:16px;
            border-radius:12px;
            border-left:4px solid var(--mc-primary-2);
            margin-bottom:18px;
        }
        [dir="rtl"] .test-description{
            border-left:0;
            border-right:4px solid var(--mc-primary-2);
        }
        .test-description h5{ color:var(--mc-text); margin:0 0 8px 0; font-weight:800; }
        .test-description p{ color:var(--mc-gray); margin:0; line-height:1.7; }

        .course-info{
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border:1px solid #0ea5e9;
            border-radius:12px;
            padding:12px 14px;
            margin-bottom:16px;
        }
        .course-info h6{ color:#0c4a6e; margin:0 0 4px 0; font-weight:800; }
        .course-info p{ color:#0369a1; margin:0; font-weight:700; }

        .part-section{
            background:#fff;
            border:2px solid var(--mc-border);
            border-radius:12px;
            margin-bottom:16px;
            transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
        }
        .part-section:hover{
            border-color:var(--mc-primary-2);
            box-shadow:0 4px 15px rgba(59,130,246,.12);
            transform: translateY(-2px);
        }
        .part-header{
            background: linear-gradient(135deg, var(--mc-primary) 0%, var(--mc-primary-2) 100%);
            color:#fff;
            padding:12px 16px;
            font-weight:800;
        }
        .part-content{ padding:16px; }
        .part-stats{
            display:grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap:12px;
        }
        .part-stat-item{
            text-align:center;
            background:var(--mc-bg);
            padding:12px;
            border-radius:10px;
        }
        .part-stat-number{
            display:block;
            font-size:1.15rem;
            font-weight:900;
            color:var(--mc-primary);
        }
        .part-stat-label{
            font-size:.82rem;
            color:var(--mc-gray);
            margin-top:4px;
            font-weight:700;
        }

        .status-section{ padding:20px; }

        .current-status{
            display:flex;
            align-items:center;
            gap:12px;
            margin-bottom:16px;
        }
        .status-icon{
            width:50px;
            height:50px;
            border-radius:999px;
            display:flex;
            align-items:center;
            justify-content:center;
            font-size:1.1rem;
            color:#fff;
        }
        .status-not-started .status-icon{
            background: linear-gradient(135deg, var(--mc-gray) 0%, var(--mc-gray-2) 100%);
        }
        .status-in-progress .status-icon{
            background: linear-gradient(135deg, var(--mc-warn) 0%, var(--mc-warn-2) 100%);
        }
        .status-completed .status-icon{
            background: linear-gradient(135deg, var(--mc-success) 0%, var(--mc-success-2) 100%);
        }
        .status-info h4{
            margin:0 0 4px 0;
            color:var(--mc-text);
            font-size:1.1rem;
            font-weight:900;
        }
        .status-info p{ margin:0; color:var(--mc-gray); font-weight:700; }

        .warning-notice{
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border:1px solid var(--mc-warn);
            border-radius:12px;
            padding:14px;
            margin-bottom:16px;
        }
        .warning-notice .notice-header{
            display:flex;
            align-items:center;
            gap:10px;
            margin-bottom:8px;
        }
        .warning-notice .notice-icon{ color:var(--mc-warn-2); font-size:1.1rem; }
        .warning-notice .notice-title{ margin:0; color:#92400e; font-weight:900; }
        .warning-notice .notice-content{ margin:0; color:#92400e; font-weight:700; }

        .action-buttons{
            display:flex;
            gap:12px;
            flex-wrap:wrap;
        }

        .btn-action{
            padding:13px 18px;
            border-radius:12px;
            font-weight:900;
            text-decoration:none;
            display:inline-flex;
            align-items:center;
            gap:10px;
            transition: transform .2s ease, box-shadow .2s ease, background .2s ease, border-color .2s ease;
            border:none;
            cursor:pointer;
            font-size:1rem;
            white-space:nowrap;
        }

        .btn-primary-action{
            background: linear-gradient(135deg, var(--mc-primary) 0%, var(--mc-primary-2) 100%);
            color:#fff;
        }
        .btn-primary-action:hover{
            transform:translateY(-2px);
            box-shadow:0 10px 24px rgba(30,64,175,.25);
            color:#fff;
        }

        .btn-success-action{
            background: linear-gradient(135deg, var(--mc-success) 0%, var(--mc-success-2) 100%);
            color:#fff;
        }
        .btn-success-action:hover{
            transform:translateY(-2px);
            box-shadow:0 10px 24px rgba(16,185,129,.22);
            color:#fff;
        }

        .btn-warning-action{
            background: linear-gradient(135deg, var(--mc-warn) 0%, var(--mc-warn-2) 100%);
            color:#fff;
        }
        .btn-warning-action:hover{
            transform:translateY(-2px);
            box-shadow:0 10px 24px rgba(245,158,11,.22);
            color:#fff;
        }

        .btn-secondary-action{
            background:#fff;
            color:var(--mc-gray);
            border:2px solid var(--mc-border);
        }
        .btn-secondary-action:hover{
            border-color:var(--mc-primary-2);
            color:var(--mc-primary-2);
            transform:translateY(-1px);
        }

        .previous-attempts{ padding:18px; margin-top:18px; }
        .attempts-header h5{
            color:var(--mc-text);
            font-weight:900;
            margin:0 0 14px 0;
            display:flex;
            align-items:center;
            gap:10px;
        }
        .attempts-table-container{ overflow-x:auto; }
        .attempts-table{
            margin:0;
            border-radius:12px;
            overflow:hidden;
            box-shadow:0 2px 10px rgba(0,0,0,.05);
        }
        .attempts-table thead th{
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border:none;
            color:#374151;
            font-weight:900;
            padding:14px 10px;
            text-align:center;
            font-size:.9rem;
        }
        .attempts-table tbody td{
            border:none;
            padding:14px 10px;
            text-align:center;
            vertical-align:middle;
            border-bottom:1px solid #f3f4f6;
        }
        .attempt-row:hover{ background:var(--mc-bg); }

        .attempt-number-badge{
            background: linear-gradient(135deg, var(--mc-primary-2) 0%, var(--mc-primary) 100%);
            color:#fff;
            padding:7px 12px;
            border-radius:999px;
            font-weight:900;
            font-size:.9rem;
            display:inline-block;
            min-width:42px;
        }
        .attempt-date{ text-align:left; }
        [dir="rtl"] .attempt-date{ text-align:right; }
        .attempt-date .date{ font-weight:900; color:var(--mc-text); font-size:.95rem; }
        .attempt-date .time{ font-size:.82rem; color:var(--mc-gray); font-weight:700; }

        .score-display{ font-weight:900; }
        .score-value{ color:var(--mc-primary); font-size:1.05rem; }
        .score-total{ color:var(--mc-gray); font-size:.9rem; font-weight:800; }

        .percentage-badge{
            padding:6px 12px;
            border-radius:999px;
            font-weight:900;
            font-size:.9rem;
            display:inline-block;
            min-width:70px;
        }
        .percentage-badge.excellent{
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            color:#065f46;
            border:1px solid var(--mc-success);
        }
        .percentage-badge.good{
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            color:#92400e;
            border:1px solid var(--mc-warn);
        }
        .percentage-badge.needs-improvement{
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color:#991b1b;
            border:1px solid #ef4444;
        }

        .view-attempt-btn{
            border-radius:10px;
            padding:7px 12px;
            font-size:.86rem;
            font-weight:900;
        }

        [dir="rtl"] .ms-auto{ margin-left:0 !important; margin-right:auto !important; }

        @media (max-width:768px){
            .test-hero h1{ font-size:1.8rem; }
            .info-grid{ grid-template-columns:1fr; }
            .part-stats{ grid-template-columns:repeat(2,1fr); }
            .action-buttons{ flex-direction:column; }
            .btn-action{ justify-content:center; }
        }
    </style>
@endsection

@section('content')
@php
    $baseScore = 200;
    $maxScore  = 800;

    $allowedLevels = ['Digital SAT','EST I','EST II','ACT I','ACT II'];
    $levelName = $test->course->level->name ?? '';
    $useRoundUpTo10 = in_array($levelName, $allowedLevels, true);

    $allQuestions = $test->questions()->get();
    $rawQuestionsTotal = (float) $allQuestions->sum('score');

    $scale = 0;
    if ($rawQuestionsTotal > 0) {
        $scale = ($maxScore - $baseScore) / $rawQuestionsTotal;
    }

    $roundUpToNext10 = function ($n) {
        $n = (int) $n;
        if ($n <= 0) return 0;
        $mod = $n % 10;
        if ($mod !== 0) $n += (10 - $mod);
        return $n;
    };

    $clamp = function ($n, $min, $max) {
        if ($n < $min) return $min;
        if ($n > $max) return $max;
        return $n;
    };

    $calcRawEarnedForAttempt = function ($attemptId) use ($test) {
        $raw = 0.0;

        $qs = $test->questions()->with(['answers' => function($q) use ($attemptId) {
            $q->where('student_test_id', $attemptId);
        }])->get();

        foreach ($qs as $q) {
            $ans = $q->answers->first();
            if ($ans) $raw += (float) ($ans->score_earned ?? 0);
        }

        return $raw;
    };

    $calcScaled800 = function ($rawEarned) use ($baseScore, $maxScore, $scale, $useRoundUpTo10, $roundUpToNext10, $clamp) {
        $scaled = (int) round($baseScore + ((float)$rawEarned * (float)$scale));
        $scaled = $clamp($scaled, $baseScore, $maxScore);

        if ($useRoundUpTo10) {
            $scaled = $roundUpToNext10($scaled);
            $scaled = $clamp($scaled, $baseScore, $maxScore);
        }

        return $scaled;
    };

    $activeScore800 = null;
    if (!empty($activeAttempt)) {
        $rawEarnedActive = $calcRawEarnedForAttempt($activeAttempt->id);
        $activeScore800  = $calcScaled800($rawEarnedActive);
    }

    $part1Count = (int) ($test->part1_questions_count ?? 0);
    $part2Count = (int) ($test->part2_questions_count ?? 0);

    $module1Questions = $allQuestions->slice(0, $part1Count);
    $module2Questions = $allQuestions->slice($part1Count, $part2Count);

    $module1MaxPoints = (float) $module1Questions->sum('score');
    $module2MaxPoints = (float) $module2Questions->sum('score');
@endphp

<div class="main-content mc-wrap">
    <div class="test-hero">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1>{{ $test->name }}</h1>
                    <p>{{ $test->course->name ?? '' }}</p>
                </div>
                <div class="col-md-4 text-end">
                    <i class="fas fa-clipboard-list fa-3x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="test-info-card">
                <div class="card-header-custom">
                    <h3>@lang('l.test_information')</h3>
                </div>

                <div class="card-body-custom">
                    <div class="course-info">
                        <h6>@lang('l.course')</h6>
                        <p>{{ $test->course->name ?? '' }}</p>
                    </div>

                    @if($test->description)
                        <div class="test-description">
                            <h5>@lang('l.description')</h5>
                            <p>{{ $test->description }}</p>
                        </div>
                    @endif

                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">@lang('l.total_questions')</div>
                            <div class="info-value">{{ $test->total_questions_count }} @lang('l.questions')</div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">@lang('l.total_time')</div>
                            <div class="info-value">{{ $test->total_time_minutes }} @lang('l.minutes')</div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">@lang('l.total_score')</div>
                            <div class="info-value">{{ $maxScore }} @lang('l.points')</div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">@lang('l.initial_score')</div>
                            <div class="info-value">{{ $baseScore }} @lang('l.points')</div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">@lang('l.max_attempts')</div>
                            <div class="info-value">{{ $test->max_attempts ?? 1 }} @lang('l.attempts')</div>
                        </div>
                    </div>

                    <h5 class="mb-3" style="font-weight:900; color:var(--mc-text);">@lang('l.test_parts')</h5>

                    <div class="part-section">
                        <div class="part-header">@lang('l.first_part')</div>
                        <div class="part-content">
                            <div class="part-stats">
                                <div class="part-stat-item">
                                    <span class="part-stat-number">{{ $part1Count }}</span>
                                    <div class="part-stat-label">@lang('l.questions')</div>
                                </div>
                                <div class="part-stat-item">
                                    <span class="part-stat-number">{{ $test->part1_time_minutes }}</span>
                                    <div class="part-stat-label">@lang('l.minutes')</div>
                                </div>
                                <div class="part-stat-item">
                                    <span class="part-stat-number">{{ (int) round($module1MaxPoints) }}</span>
                                    <div class="part-stat-label">@lang('l.max_points')</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if(($test->break_time_minutes ?? 0) > 0)
                        <div class="part-section">
                            <div class="part-header" style="background: linear-gradient(135deg, var(--mc-success) 0%, var(--mc-success-2) 100%);">
                                @lang('l.break_time')
                            </div>
                            <div class="part-content">
                                <div class="part-stats">
                                    <div class="part-stat-item">
                                        <span class="part-stat-number">{{ $test->break_time_minutes }}</span>
                                        <div class="part-stat-label">@lang('l.minutes')</div>
                                    </div>
                                    <div class="part-stat-item">
                                        <span class="part-stat-number"><i class="fas fa-coffee" style="color:var(--mc-success-2);"></i></span>
                                        <div class="part-stat-label">@lang('l.optional')</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="part-section">
                        <div class="part-header">@lang('l.second_part')</div>
                        <div class="part-content">
                            <div class="part-stats">
                                <div class="part-stat-item">
                                    <span class="part-stat-number">{{ $part2Count }}</span>
                                    <div class="part-stat-label">@lang('l.questions')</div>
                                </div>
                                <div class="part-stat-item">
                                    <span class="part-stat-number">{{ $test->part2_time_minutes }}</span>
                                    <div class="part-stat-label">@lang('l.minutes')</div>
                                </div>
                                <div class="part-stat-item">
                                    <span class="part-stat-number">{{ (int) round($module2MaxPoints) }}</span>
                                    <div class="part-stat-label">@lang('l.max_points')</div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="status-section">

                @if($activeAttempt)
                    @switch($activeAttempt->status)
                        @case('not_started')
                            <div class="current-status status-not-started">
                                <div class="status-icon"><i class="fas fa-play"></i></div>
                                <div class="status-info">
                                    <h4>@lang('l.ready_to_start')</h4>
                                    <p>@lang('l.test_ready_to_start_desc')</p>
                                </div>
                            </div>
                            @break

                        @case('part1_in_progress')
                            <div class="current-status status-in-progress">
                                <div class="status-icon"><i class="fas fa-clock"></i></div>
                                <div class="status-info">
                                    <h4>@lang('l.first_part_in_progress')</h4>
                                    <p>@lang('l.continue_where_you_left')</p>
                                </div>
                            </div>
                            @break

                        @case('in_break')
                            <div class="current-status status-in-progress">
                                <div class="status-icon"><i class="fas fa-coffee"></i></div>
                                <div class="status-info">
                                    <h4>@lang('l.break_time')</h4>
                                    <p>@lang('l.ready_for_second_part')</p>
                                </div>
                            </div>
                            @break

                        @case('part2_in_progress')
                            <div class="current-status status-in-progress">
                                <div class="status-icon"><i class="fas fa-clock"></i></div>
                                <div class="status-info">
                                    <h4>@lang('l.second_part_in_progress')</h4>
                                    <p>@lang('l.continue_where_you_left')</p>
                                </div>
                            </div>
                            @break

                        @case('completed')
                            <div class="current-status status-completed">
                                <div class="status-icon"><i class="fas fa-check"></i></div>
                                <div class="status-info">
                                    <h4>@lang('l.test_completed')</h4>
                                    <p>@lang('l.final_score') {{ (int) ($activeScore800 ?? $baseScore) }}/{{ $maxScore }}</p>
                                </div>
                            </div>
                            @break
                    @endswitch
                @else
                    <div class="current-status status-not-started">
                        <div class="status-icon"><i class="fas fa-play"></i></div>
                        <div class="status-info">
                            <h4>@lang('l.not_started')</h4>
                            <p>@lang('l.click_start_to_begin')</p>
                        </div>
                    </div>
                @endif

                @if(!$activeAttempt || $activeAttempt->status === 'not_started')
                    <div class="warning-notice">
                        <div class="notice-header">
                            <i class="fas fa-exclamation-triangle notice-icon"></i>
                            <h6 class="notice-title">@lang('l.important_notice')</h6>
                        </div>
                        <p class="notice-content">@lang('l.test_start_warning')</p>
                    </div>
                @endif

                <div class="action-buttons">
                    @if($activeAttempt)
                        @switch($activeAttempt->status)
                            @case('not_started')
                                <button type="button" class="btn-action btn-primary-action" onclick="startTest()">
                                    <i class="fas fa-play"></i>
                                    @lang('l.start_test')
                                </button>
                                @break

                            @case('part1_in_progress')
                            @case('in_break')
                            @case('part2_in_progress')
                                <a href="{{ route('dashboard.users.tests.take', $test->id) }}" class="btn-action btn-warning-action">
                                    <i class="fas fa-play"></i>
                                    @lang('l.continue_test')
                                </a>
                                @break

                            @case('completed')
                                @if($remainingAttempts > 0)
                                    <button type="button" class="btn-action btn-primary-action" onclick="startTest()">
                                        <i class="fas fa-redo"></i>
                                        @lang('l.start_new_attempt')
                                    </button>
                                @endif
                                <a href="{{ route('dashboard.users.tests.results', $test->id) }}" class="btn-action btn-success-action">
                                    <i class="fas fa-chart-line"></i>
                                    @lang('l.view_results')
                                </a>
                                @break
                        @endswitch
                    @else
                        @if($remainingAttempts > 0)
                            <button type="button" class="btn-action btn-primary-action" onclick="startTest()">
                                <i class="fas fa-play"></i>
                                @lang('l.start_test')
                            </button>
                        @else
                            <div class="alert alert-warning mb-0" style="width:100%;">
                                <i class="fas fa-exclamation-triangle"></i>
                                @lang('l.no_more_attempts_available')
                            </div>
                        @endif
                    @endif

                    <a href="{{ route('dashboard.users.tests.index') }}" class="btn-action btn-secondary-action">
                        @if(app()->getLocale() == 'ar')
                            <i class="fas fa-arrow-right"></i>
                        @else
                            <i class="fas fa-arrow-left"></i>
                        @endif
                        @lang('l.back_to_tests')
                    </a>
                </div>

                @if(($allAttempts->where('status', 'completed')->count() ?? 0) > 0)
                    <div class="previous-attempts mt-4">
                        <div class="attempts-header">
                            <h5>
                                <i class="fas fa-history"></i>
                                @lang('l.previous_attempts')
                            </h5>
                        </div>

                        <div class="attempts-table-container">
                            <table class="table table-hover attempts-table">
                                <thead>
                                    <tr>
                                        <th>@lang('l.attempt_number')</th>
                                        <th>@lang('l.attempt_date')</th>
                                        <th>@lang('l.attempt_score')</th>
                                        <th>@lang('l.percentage')</th>
                                        <th>@lang('l.actions')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($allAttempts->where('status', 'completed')->sortByDesc('created_at') as $attempt)
                                        @php
                                            $rawEarned = $calcRawEarnedForAttempt($attempt->id);
                                            $score800  = $calcScaled800($rawEarned);
                                            $percentage = round(($score800 / $maxScore) * 100, 1);

                                            if ($percentage >= 80) $badgeClass = 'excellent';
                                            elseif ($percentage >= 60) $badgeClass = 'good';
                                            else $badgeClass = 'needs-improvement';
                                        @endphp
                                        <tr class="attempt-row">
                                            <td><span class="attempt-number-badge">{{ $attempt->attempt_number }}</span></td>
                                            <td>
                                                <div class="attempt-date">
                                                    <div class="date">{{ $attempt->created_at->format('Y-m-d') }}</div>
                                                    <small class="time">{{ $attempt->created_at->format('H:i') }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="score-display">
                                                    <span class="score-value">{{ $score800 }}</span>
                                                    <span class="score-total">/ {{ $maxScore }}</span>
                                                </div>
                                            </td>
                                            <td><span class="percentage-badge {{ $badgeClass }}">{{ $percentage }}%</span></td>
                                            <td>
                                                <a class="btn btn-sm btn-outline-primary view-attempt-btn"
                                                   href="{{ route('dashboard.users.tests.results', $test->id) }}?attempt_id={{ $attempt->id }}"
                                                   title="@lang('l.view_details')">
                                                    <i class="fas fa-eye me-1"></i>
                                                    @lang('l.view_details')
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    function startTest() {
        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
        const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : null;

        if (!csrfToken) {
            if (window.Swal) {
                Swal.fire({ title: 'Error', text: 'CSRF token missing', icon: 'error' });
            }
            return;
        }

        const ask = () => {
            if (!window.Swal) return Promise.resolve({ isConfirmed: true });

            return Swal.fire({
                title: '@lang("l.start_test")',
                text: '@lang("l.are_you_ready_to_start")',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#1e40af',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '@lang("l.yes_start")',
                cancelButtonText: '@lang("l.cancel")',
                reverseButtons: true
            });
        };

        const loading = () => {
            if (!window.Swal) return;
            Swal.fire({
                title: '@lang("l.starting_test")',
                text: '@lang("l.please_wait")',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => Swal.showLoading()
            });
        };

        ask().then((result) => {
            if (!result.isConfirmed) return;

            loading();

            fetch('{{ route("dashboard.users.tests.start", $test->id) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({})
            })
            .then(r => {
                if (!r.ok) throw new Error('HTTP ' + r.status);
                return r.json();
            })
            .then(data => {
                if (!data || !data.success) {
                    if (window.Swal) {
                        Swal.fire({
                            title: '@lang("l.error")',
                            text: (data && data.error) ? data.error : '@lang("l.unknown_error")',
                            icon: 'error',
                            confirmButtonColor: '#1e40af'
                        });
                    }
                    return;
                }

                const go = () => { window.location.href = data.redirect; };

                if (window.Swal && data.attempt_number) {
                    Swal.fire({
                        title: '@lang("l.test_started")',
                        text: `@lang("l.attempt_number") ${data.attempt_number}`,
                        icon: 'success',
                        confirmButtonColor: '#1e40af',
                        timer: 1800,
                        timerProgressBar: true
                    }).then(go);
                } else {
                    go();
                }
            })
            .catch(err => {
                if (window.Swal) {
                    Swal.fire({
                        title: '@lang("l.error")',
                        text: err.message || '@lang("l.connection_error")',
                        icon: 'error',
                        confirmButtonColor: '#1e40af'
                    });
                }
            });
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        if (window.jQuery) {
            $('.test-info-card, .status-section').css('opacity', '0').animate({ opacity: 1 }, 500);
            $('.previous-attempts').css('opacity', '0').delay(200).animate({ opacity: 1 }, 500);
        }
    });
</script>
@endsection