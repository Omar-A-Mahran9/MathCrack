@extends('themes.default.layouts.back.student-master')

@section('title')
    @lang('l.tests')
@endsection

@section('css')
<style>
    .course-section {
        margin-bottom: 30px !important;
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        transition: all 0.3s ease;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .course-section:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }

    .course-header {
        padding: 25px 30px !important;
        margin-bottom: 25px !important;
        background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
        color: white;
        position: relative;
        overflow: hidden;
    }

    .course-header::before {
        content: '';
        position: absolute;
        top: 0;
        right: -60px;
        width: 120px;
        height: 100%;
        background: rgba(255, 255, 255, 0.1);
        transform: skewX(-15deg);
    }

    .course-title {
        font-size: 1.8rem;
        font-weight: 700;
        margin: 0;
        color: white !important;
        position: relative;
        z-index: 2;
    }

    .course-meta {
        margin-top: 12px !important;
        gap: 20px !important;
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        position: relative;
        z-index: 2;
    }

    .course-meta-item {
        display: flex;
        align-items: center;
        gap: 10px;
        color: rgba(255, 255, 255, 0.95);
        font-size: 1rem;
        font-weight: 500;
    }

    .course-meta-item i,
    .course-meta-item span {
        color: white !important;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
    }

    .tests-grid {
        padding: 25px 30px !important;
    }

    .test-card {
        margin-bottom: 20px !important;
        background: white;
        border-radius: 16px;
        padding: 0;
        transition: all 0.4s ease;
        height: 100%;
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        border: 1px solid #f1f5f9;
    }

    .test-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
    }

    .card-header {
        color: white;
        padding: 20px;
        position: relative;
    }

    .test-card:nth-child(3n+1) .card-header,
    .test-card:nth-child(3n+1) .btn-primary-test {
        background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
    }

    .test-card:nth-child(3n+2) .card-header,
    .test-card:nth-child(3n+2) .btn-primary-test {
        background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%);
    }

    .test-card:nth-child(3n+3) .card-header,
    .test-card:nth-child(3n+3) .btn-primary-test {
        background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
    }

    .test-title {
        font-size: 1.3rem;
        font-weight: 700;
        color: white;
        margin: 0 0 5px 0;
    }

    .test-description {
        color: rgba(255, 255, 255, 0.9);
        font-size: 0.9rem;
        margin-bottom: 0;
        line-height: 1.5;
    }

    .card-body {
        padding: 20px !important;
    }

    .test-stats {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        margin-bottom: 15px !important;
        gap: 12px !important;
    }

    .stat-item {
        background: #f8fafc;
        padding: 15px;
        border-radius: 12px;
        text-align: center;
        transition: all 0.3s ease;
        border: 1px solid #f1f5f9;
    }

    .stat-item:hover {
        background: #f1f5f9;
        transform: translateY(-2px);
    }

    .stat-number {
        font-size: 1.3rem;
        font-weight: 700;
        color: #1e40af;
        display: block;
        margin-bottom: 5px;
    }

    .stat-label {
        font-size: 0.85rem;
        color: #64748b;
        font-weight: 500;
    }

    .price-section {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        padding: 15px;
        border-radius: 12px;
        text-align: center;
        margin-bottom: 20px;
    }

    .price-amount {
        font-size: 1.4rem;
        font-weight: 700;
        margin-bottom: 5px;
    }

    .price-label {
        font-size: 0.9rem;
        opacity: 0.9;
    }

    .test-status {
        margin-bottom: 15px !important;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        border-radius: 25px;
        font-size: 0.9rem;
        font-weight: 600;
    }

    .status-not-started {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        color: #475569;
    }

    .status-in-progress {
        background: linear-gradient(135deg, #fef3c7 0%, #f59e0b 100%);
        color: #92400e;
    }

    .status-completed {
        background: linear-gradient(135deg, #d1fae5 0%, #10b981 100%);
        color: #065f46;
    }

    .status-locked {
        background: linear-gradient(135deg, #fee2e2 0%, #ef4444 100%);
        color: #991b1b;
    }

    .test-actions {
        display: flex;
        gap: 10px !important;
    }

    .btn-test {
        flex: 1;
        min-height: 42px;
        padding: 12px 20px;
        border-radius: 10px;
        font-weight: 800;
        text-decoration: none !important;
        text-align: center;
        transition: all 0.3s ease;
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        gap: 8px;
        font-size: 0.92rem;
        line-height: 1.2;
        opacity: 1 !important;
        visibility: visible !important;
        white-space: nowrap;
    }

    .btn-test i,
    .btn-test span {
        display: inline-flex !important;
        align-items: center;
        color: inherit !important;
        opacity: 1 !important;
        visibility: visible !important;
        text-indent: 0 !important;
        line-height: 1.2;
    }

    .btn-test span {
        font-size: 0.92rem;
        font-weight: 800;
    }

    .btn-primary-test {
        background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
        color: #ffffff !important;
        border: none;
    }

    .btn-primary-test:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(30, 64, 175, 0.4);
        color: white;
    }

    .btn-success-test {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: #ffffff !important;
        border: none;
    }

    .btn-success-test:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(16, 185, 129, 0.4);
        color: white;
    }

    .btn-warning-test {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: #ffffff !important;
        border: none;
    }

    .btn-warning-test:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(245, 158, 11, 0.4);
        color: white;
    }

    .course-purchase-section {
        padding: 20px 25px !important;
        margin-top: 20px !important;
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        border-top: 1px solid #e2e8f0;
        text-align: center;
        border-radius: 0 0 20px 20px;
    }

    .course-purchase-price {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1e40af;
        margin-bottom: 8px;
    }

    .course-purchase-desc {
        color: #64748b;
        font-size: 1rem;
    }

    .page-headers {
        margin-bottom: 5px !important;
        padding: 40px 0 !important;
        background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
        color: white !important;
        border-radius: 20px !important;
        position: relative !important;
        overflow: hidden !important;
    }

    .page-headers::before {
        content: '';
        position: absolute;
        top: 0;
        right: -100px;
        width: 200px;
        height: 100%;
        background: rgba(255, 255, 255, 0.1) !important;
        transform: skewX(-15deg);
    }

    .page-header h1,
    .page-main-title {
        margin: 0 0 10px 0 !important;
        font-size: 2.5rem !important;
        font-weight: 700 !important;
        color: white !important;
        position: relative !important;
        z-index: 2 !important;
    }

    .page-header p,
    .page-subtitle {
        margin: 0 !important;
        opacity: 0.9 !important;
        color: white !important;
        font-size: 1.1rem !important;
        position: relative !important;
        z-index: 2 !important;
    }

    .stats-summary {
        position: relative;
        z-index: 2;
    }

    .stats-badge {
        display: inline-flex;
        align-items: center;
        padding: 12px 18px;
        border-radius: 16px;
        background: rgba(255, 255, 255, 0.18);
        border: 1px solid rgba(255, 255, 255, 0.3);
        color: white;
        font-weight: 700;
        backdrop-filter: blur(10px);
    }

    #filtersForm {
        background: linear-gradient(135deg, #239BA7 0%, #48B3AF 100%) !important;
        padding: 10px 25px 20px 25px !important;
        border-radius: 22px !important;
        box-shadow: inset 0 2px 4px rgba(255, 255, 255, 0.8), 0 8px 25px rgba(0, 0, 0, 0.06) !important;
        margin-top: 12px !important;
        margin-bottom: 18px !important;
        border: 1px solid #e2e8f0 !important;
        position: relative !important;
        overflow: hidden !important;
        width: 100% !important;
        min-height: 100px !important;
    }

    #filtersForm label.form-label {
        font-size: 1.5rem !important;
        color: #0c0c0c !important;
        font-weight: 700 !important;
        margin-bottom: 5px !important;
    }

    #filtersForm .form-select,
    #filtersForm .form-select option {
        font-size: 1.2em !important;
        font-weight: 600 !important;
    }

    #filtersForm .form-select {
        background-color: rgba(255, 255, 255, 0.9) !important;
        backdrop-filter: blur(5px) !important;
        border: 2px solid #cbd5e0 !important;
    }

    .view-toggle-wrapper {
        display: flex;
        justify-content: flex-end;
        margin: 10px 0 18px 0;
    }

    .view-toggle {
        display: inline-flex;
        align-items: center;
        background: #ffffff;
        border: 1px solid #dbeafe;
        border-radius: 16px;
        padding: 6px;
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.06);
        gap: 6px;
    }

    .view-toggle-btn {
        border: none;
        background: transparent;
        color: #475569;
        padding: 10px 16px;
        border-radius: 12px;
        font-weight: 700;
        font-size: 0.95rem;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.25s ease;
        cursor: pointer;
    }

    .view-toggle-btn.active {
        background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
        color: #fff;
        box-shadow: 0 6px 18px rgba(30, 64, 175, 0.25);
    }

    .tests-table-wrapper {
        padding: 25px 30px !important;
    }

    .tests-table-card {
        background: #fff;
        border-radius: 18px;
        border: 1px solid #e2e8f0;
        overflow: hidden;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
    }

    .tests-table {
        width: 100%;
        table-layout: fixed;
        border-collapse: separate;
        border-spacing: 0;
        margin: 0;
    }

    .tests-table thead th {
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        color: #1e3a8a;
        font-size: 0.95rem;
        font-weight: 800;
        padding: 16px 14px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        border-bottom: 1px solid #cbd5e1;
        white-space: nowrap;
        text-align: center !important;
        vertical-align: middle !important;
    }

    .tests-table tbody td {
        padding: 16px 14px;
        border-bottom: 1px solid #eef2f7;
        color: #334155;
        font-size: 0.95rem;
        text-align: center !important;
        vertical-align: middle !important;
    }

    .tests-table thead th:first-child,
    .tests-table tbody td:first-child {
        text-align: left !important;
    }

    .table-test-name {
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 4px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        text-align: left !important;
    }

    .table-test-desc {
        font-size: 0.88rem;
        color: #64748b;
        line-height: 1.5;
        text-align: left !important;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }

    .mini-stat-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 54px;
        padding: 8px 12px;
        border-radius: 12px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        font-weight: 800;
        color: #1e40af;
        margin: 0 auto !important;
    }

    .table-price-free,
    .table-price-paid {
        display: inline-block;
        margin: 0 auto !important;
        text-align: center;
        font-weight: 800;
    }

    .table-price-free {
        color: #059669;
    }

    .table-price-paid {
        color: #b45309;
    }

    .table-actions {
        display: flex;
        justify-content: center !important;
        align-items: center !important;
        gap: 8px;
        flex-wrap: nowrap;
        width: 100%;
    }

    .table-actions .btn-test {
        min-width: 150px;
        flex: 0 0 auto;
        padding: 10px 16px;
        font-size: 0.9rem;
    }

    .table-actions .btn-test span {
        font-size: 0.9rem;
    }

    .table-empty-note {
        padding: 22px;
        text-align: center !important;
        color: #64748b;
        font-weight: 600;
    }

    .no-tests {
        text-align: center;
        padding: 80px 20px;
        color: #64748b;
    }

    .no-tests i {
        font-size: 5rem;
        margin-bottom: 25px;
        color: #cbd5e1;
    }

    .no-tests h3 {
        font-size: 1.8rem;
        margin-bottom: 15px;
        color: #475569;
    }

    @media (max-width: 992px) {
        .tests-table-wrapper {
            padding: 20px !important;
        }

        .tests-table-card {
            overflow-x: auto;
        }

        .tests-table {
            min-width: 980px;
        }
    }

    @media (max-width: 768px) {
        .course-header {
            padding: 20px !important;
        }

        .course-title {
            font-size: 1.5rem;
        }

        .test-actions {
            flex-direction: column;
        }

        .page-main-title {
            font-size: 2rem !important;
        }

        .page-headers {
            padding: 30px 0 !important;
            margin-bottom: 10px !important;
        }
    }

</style>
@endsection

@section('content')
<div class="main-content">
    @php
        $track = request()->query('track');

        $trackTitles = [
            'digital-sat' => 'Digital SAT',
            'est-i' => 'EST I',
            'est-ii' => 'EST II',
            'act-i' => 'ACT I',
            'act-ii' => 'ACT II',
            'ap-math' => 'AP Math',
        ];

        $trackTitle = $trackTitles[$track] ?? null;
        $totalTests = $coursesWithTests->sum(function($course) {
            return $course['tests']->count();
        });
    @endphp

    <div class="page-headers">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="page-header-content">
                        <h1 class="page-main-title">
                            {{ $trackTitle ? $trackTitle . ' Practice Tests' : 'Practice Tests' }}
                        </h1>
                        <p class="page-subtitle">
                            {{ $trackTitle
                                ? 'Practice tests for ' . $trackTitle . '. The Free Mock Test is included as the first practice test.'
                                : 'Explore available practice tests and improve your skills'
                            }}
                        </p>
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <div class="stats-summary">
                        <span class="stats-badge">
                            <i class="fas fa-clipboard-list me-2"></i>
                            {{ $totalTests }} Tests Available
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>


    @if(!request('track'))
    <form id="filtersForm" method="GET" action="{{ route('dashboard.users.tests.index') }}" class="row g-3 mb-3">
        @if(request('track'))
            <input type="hidden" name="track" value="{{ request('track') }}">
        @endif

        <div class="col-md-4">
            <label class="form-label">Level</label>
            <select id="levelSelect" name="level_id" class="form-select">
                <option value="">All Levels</option>
                @foreach($levels as $lvl)
                    <option value="{{ $lvl->id }}" {{ request('level_id') == $lvl->id ? 'selected' : '' }}>
                        {{ $lvl->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4">
            <label class="form-label">Course</label>
            <select id="courseSelect" name="course_id" class="form-select">
                <option value="">All Courses</option>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                        {{ $course->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </form>
@endif


    @if($coursesWithTests->count() > 0)
        <div class="view-toggle-wrapper">
            <div class="view-toggle">
                <button type="button" class="view-toggle-btn active" id="cardsToggleBtn" onclick="setTestsView('cards')">
                    <i class="fas fa-grip-horizontal"></i>
                    Cards View
                </button>
                <button type="button" class="view-toggle-btn" id="tableToggleBtn" onclick="setTestsView('table')">
                    <i class="fas fa-table"></i>
                    Table View
                </button>
            </div>
        </div>

        @foreach($coursesWithTests as $course)
            <div class="course-section">
                <div class="course-header">
                    @php
    $displayCourseName = $course['name'];

    if (request('track') === 'digital-sat') {
        if (stripos($course['name'], 'part 1') !== false) {
            $displayCourseName = 'Practice Tests - Set 1';
        } elseif (stripos($course['name'], 'part 2') !== false) {
            $displayCourseName = 'Practice Tests - Set 2';
        }
    }
@endphp

<h2 class="course-title">{{ $displayCourseName }}</h2>
                    <div class="course-meta">
                        <div class="course-meta-item">
                            <i class="fas fa-clipboard-list"></i>
                            <span>{{ $course['tests']->count() }} @lang('l.tests')</span>
                        </div>
                        @if($course['tests_price'] > 0)
                            <div class="course-meta-item">
                                <i class="fas fa-tag"></i>
                                <span>Practice Tests Price: {{ number_format($course['tests_price'], 2) }} @lang('l.currency')</span>
                            </div>
                        @endif
                        @if($course['has_purchased_all'])
                            <div class="course-meta-item">
                                <i class="fas fa-check-circle"></i>
                                <span>@lang('l.all_tests_purchased')</span>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="tests-grid tests-view-block" data-view="cards">
                    <div class="row">
                        @foreach($course['tests'] as $test)
                            @php
                                $testModel = \App\Models\Test::find($test['id']);

                                $modulesCount = 0;
                                $questionsTotal = 0;
                                $timeTotal = 0;

                                if ($testModel) {
                                    foreach (range(1, 5) as $i) {
                                        $qCol = "part{$i}_questions_count";
                                        $tCol = "part{$i}_time_minutes";

                                        $q = (int)($testModel->{$qCol} ?? 0);
                                        $t = (int)($testModel->{$tCol} ?? 0);

                                        if ($q > 0 || $t > 0) {
                                            $modulesCount++;
                                            $questionsTotal += $q;
                                            $timeTotal += $t;
                                        }
                                    }

                                    if ($modulesCount === 0) {
                                        $modulesCount = 1;
                                        $questionsTotal = (int)($testModel->total_questions ?? $test['total_questions'] ?? 0);
                                        $timeTotal = (int)($testModel->total_time ?? $test['total_time'] ?? 0);
                                    }
                                } else {
                                    $modulesCount = 1;
                                    $questionsTotal = (int)($test['total_questions'] ?? 0);
                                    $timeTotal = (int)($test['total_time'] ?? 0);
                                }
                            @endphp

                            <div class="col-xl-4 col-lg-6 col-md-6 mb-4">
                                <div class="test-card">
                                    <div class="card-header">
                                        <h3 class="test-title">{{ $test['name'] }}</h3>
                                        @if($test['description'])
                                            <p class="test-description">{{ Str::limit($test['description'], 80) }}</p>
                                        @endif
                                    </div>

                                    <div class="card-body">
                                        <div class="test-stats">
                                            <div class="stat-item">
                                                <span class="stat-number">{{ $questionsTotal }}</span>
                                                <div class="stat-label">@lang('l.questions')</div>
                                            </div>
                                            <div class="stat-item">
                                                <span class="stat-number">{{ $timeTotal }}</span>
                                                <div class="stat-label">@lang('l.minutes')</div>
                                            </div>
                                            <div class="stat-item">
                                                <span class="stat-number">{{ $modulesCount }}</span>
                                                <div class="stat-label">@lang('l.modules')</div>
                                            </div>
                                            <div class="stat-item">
                                                <span class="stat-number">{{ $test['total_score'] }}</span>
                                                <div class="stat-label">@lang('l.points')</div>
                                            </div>
                                        </div>

                                        @if($test['price'] > 0 && !$test['has_paid'])
                                            <div class="price-section">
                                                <div class="price-amount">{{ number_format($test['price'], 2) }} @lang('l.currency')</div>
                                                <div class="price-label">@lang('l.test_price')</div>
                                            </div>
                                        @endif

                                        @if($test['has_paid'])
                                            <div class="test-status">
                                                @switch($test['status'])
                                                    @case('not_started')
                                                        <span class="status-badge status-not-started">
                                                            <i class="fas fa-play-circle"></i>
                                                            @lang('l.not_started')
                                                        </span>
                                                        @break
                                                    @case('part1_in_progress')
                                                    @case('break_time')
                                                    @case('in_break')
                                                    @case('part2_in_progress')
                                                        <span class="status-badge status-in-progress">
                                                            <i class="fas fa-clock"></i>
                                                            @lang('l.continue_test')
                                                        </span>
                                                        @break
                                                    @case('completed')
                                                        <span class="status-badge status-completed">
                                                            <i class="fas fa-check-circle"></i>
                                                            @lang('l.completed')
                                                        </span>
                                                        @break
                                                    @default
                                                        <span class="status-badge status-locked">
                                                            <i class="fas fa-lock"></i>
                                                            @lang('l.locked')
                                                        </span>
                                                @endswitch
                                            </div>
                                        @endif

                                        <div class="test-actions">
                                            @if($test['has_paid'])
                                                @switch($test['status'])
                                                    @case('not_started')
                                                        <a href="{{ route('dashboard.users.tests.show', $test['id']) }}" class="btn-test btn-primary-test">
                                                            <i class="fas fa-play"></i>
                                                            <span>Start Test</span>
                                                        </a>
                                                        @break
                                                    @case('part1_in_progress')
                                                    @case('break_time')
                                                    @case('in_break')
                                                    @case('part2_in_progress')
                                                        <a href="{{ route('dashboard.users.tests.take', $test['id']) }}" class="btn-test btn-warning-test">
                                                            <i class="fas fa-forward"></i>
                                                            <span>Continue Test</span>
                                                        </a>
                                                        @break
                                                    @case('completed')
                                                        <a href="{{ route('dashboard.users.tests.results', $test['id']) }}" class="btn-test btn-success-test">
                                                            <i class="fas fa-chart-line"></i>
                                                            <span>View Results</span>
                                                        </a>
                                                        @break
                                                @endswitch
                                            @else
                                                @if($test['price'] > 0)
                                                    <a href="{{ route('dashboard.users.tests.purchase.test', $test['id']) }}" class="btn-test btn-primary-test">
                                                        <i class="fas fa-shopping-cart"></i>
                                                        <span>Purchase Test</span>
                                                    </a>
                                                @else
                                                    <a href="{{ route('dashboard.users.tests.show', $test['id']) }}" class="btn-test btn-success-test">
                                                        <i class="fas fa-gift"></i>
                                                        <span>Start Free Test</span>
                                                    </a>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if($course['tests_price'] > 0 && !$course['has_purchased_all'])
                        <div class="course-purchase-section">
                            <div class="course-purchase-info">
                                <div class="course-purchase-price">
                                    {{ number_format($course['tests_price'], 2) }} @lang('l.currency')
                                </div>
                                <div class="course-purchase-desc">
                                    @lang('l.purchase_all_course_tests_desc')
                                </div>
                            </div>
                            <a href="{{ route('dashboard.users.tests.purchase.course-tests', $course['id']) }}" class="btn-test btn-primary-test" style="max-width: 300px; margin: 0 auto;">
                                <i class="fas fa-shopping-cart"></i>
                                <span>Purchase All Tests</span>
                            </a>
                        </div>
                    @endif
                </div>

                <div class="tests-table-wrapper tests-view-block" data-view="table" style="display:none;">
                    <div class="tests-table-card">
                        <table class="tests-table">
                            <colgroup>
                                <col style="width: 20%;">
                                <col style="width: 10%;">
                                <col style="width: 10%;">
                                <col style="width: 10%;">
                                <col style="width: 10%;">
                                <col style="width: 10%;">
                                <col style="width: 14%;">
                                <col style="width: 16%;">
                            </colgroup>
                            <thead>
                                <tr>
                                    <th>Practice Test</th>
                                    <th>@lang('l.questions')</th>
                                    <th>@lang('l.minutes')</th>
                                    <th>@lang('l.modules')</th>
                                    <th>@lang('l.points')</th>
                                    <th>@lang('l.price')</th>
                                    <th>@lang('l.status')</th>
                                    <th>@lang('l.actions')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($course['tests'] as $test)
                                    @php
                                        $testModel = \App\Models\Test::find($test['id']);

                                        $modulesCount = 0;
                                        $questionsTotal = 0;
                                        $timeTotal = 0;

                                        if ($testModel) {
                                            foreach (range(1, 5) as $i) {
                                                $qCol = "part{$i}_questions_count";
                                                $tCol = "part{$i}_time_minutes";

                                                $q = (int)($testModel->{$qCol} ?? 0);
                                                $t = (int)($testModel->{$tCol} ?? 0);

                                                if ($q > 0 || $t > 0) {
                                                    $modulesCount++;
                                                    $questionsTotal += $q;
                                                    $timeTotal += $t;
                                                }
                                            }

                                            if ($modulesCount === 0) {
                                                $modulesCount = 1;
                                                $questionsTotal = (int)($testModel->total_questions ?? $test['total_questions'] ?? 0);
                                                $timeTotal = (int)($testModel->total_time ?? $test['total_time'] ?? 0);
                                            }
                                        } else {
                                            $modulesCount = 1;
                                            $questionsTotal = (int)($test['total_questions'] ?? 0);
                                            $timeTotal = (int)($test['total_time'] ?? 0);
                                        }
                                    @endphp

                                    <tr>
                                        <td>
                                            <div class="table-test-name">{{ $test['name'] }}</div>
                                            @if($test['description'])
                                                <div class="table-test-desc">{{ Str::limit($test['description'], 100) }}</div>
                                            @endif
                                        </td>
                                        <td><span class="mini-stat-badge">{{ $questionsTotal }}</span></td>
                                        <td><span class="mini-stat-badge">{{ $timeTotal }}</span></td>
                                        <td><span class="mini-stat-badge">{{ $modulesCount }}</span></td>
                                        <td><span class="mini-stat-badge">{{ $test['total_score'] }}</span></td>
                                        <td>
                                            @if($test['price'] > 0)
                                                <span class="table-price-paid">{{ number_format($test['price'], 2) }} @lang('l.currency')</span>
                                            @else
                                                <span class="table-price-free">@lang('l.free')</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($test['has_paid'])
                                                @switch($test['status'])
                                                    @case('not_started')
                                                        <span class="status-badge status-not-started">
                                                            <i class="fas fa-play-circle"></i>
                                                            @lang('l.not_started')
                                                        </span>
                                                        @break
                                                    @case('part1_in_progress')
                                                    @case('break_time')
                                                    @case('in_break')
                                                    @case('part2_in_progress')
                                                        <span class="status-badge status-in-progress">
                                                            <i class="fas fa-clock"></i>
                                                            @lang('l.continue_test')
                                                        </span>
                                                        @break
                                                    @case('completed')
                                                        <span class="status-badge status-completed">
                                                            <i class="fas fa-check-circle"></i>
                                                            @lang('l.completed')
                                                        </span>
                                                        @break
                                                    @default
                                                        <span class="status-badge status-locked">
                                                            <i class="fas fa-lock"></i>
                                                            @lang('l.locked')
                                                        </span>
                                                @endswitch
                                            @else
                                                @if($test['price'] > 0)
                                                    <span class="status-badge status-locked">
                                                        <i class="fas fa-shopping-cart"></i>
                                                        @lang('l.purchase_required')
                                                    </span>
                                                @else
                                                    <span class="status-badge status-completed">
                                                        <i class="fas fa-gift"></i>
                                                        @lang('l.free')
                                                    </span>
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            <div class="table-actions">
                                                @if($test['has_paid'])
                                                    @switch($test['status'])
                                                        @case('not_started')
                                                            <a href="{{ route('dashboard.users.tests.show', $test['id']) }}" class="btn-test btn-primary-test">
                                                                <i class="fas fa-play"></i>
                                                                @lang('l.start_test')
                                                            </a>
                                                            @break
                                                        @case('part1_in_progress')
                                                        @case('break_time')
                                                    @case('in_break')
                                                        @case('part2_in_progress')
                                                            <a href="{{ route('dashboard.users.tests.take', $test['id']) }}" class="btn-test btn-warning-test">
                                                                <i class="fas fa-forward"></i>
                                                                @lang('l.continue_test')
                                                            </a>
                                                            @break
                                                        @case('completed')
                                                            <a href="{{ route('dashboard.users.tests.results', $test['id']) }}" class="btn-test btn-success-test">
                                                                <i class="fas fa-chart-line"></i>
                                                                @lang('l.view_results')
                                                            </a>
                                                            @break
                                                    @endswitch
                                                @else
                                                    @if($test['price'] > 0)
                                                        <a href="{{ route('dashboard.users.tests.purchase.test', $test['id']) }}" class="btn-test btn-primary-test">
                                                            <i class="fas fa-shopping-cart"></i>
                                                            @lang('l.purchase_test')
                                                        </a>
                                                    @else
                                                        <a href="{{ route('dashboard.users.tests.show', $test['id']) }}" class="btn-test btn-success-test">
                                                            <i class="fas fa-gift"></i>
                                                            Start Free Test
                                                        </a>
                                                    @endif
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach

                                @if($course['tests_price'] > 0 && !$course['has_purchased_all'])
                                    <tr>
                                        <td colspan="8" class="table-empty-note">
                                            <div style="display:flex; flex-direction:column; align-items:center; gap:14px;">
                                                <div class="course-purchase-price">
                                                    {{ number_format($course['tests_price'], 2) }} @lang('l.currency')
                                                </div>
                                                <div class="course-purchase-desc">
                                                    @lang('l.purchase_all_course_tests_desc')
                                                </div>
                                                <a href="{{ route('dashboard.users.tests.purchase.course-tests', $course['id']) }}" class="btn-test btn-primary-test" style="max-width: 320px;">
                                                    <i class="fas fa-shopping-cart"></i>
                                                    <span>Purchase All Tests</span>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <div class="no-tests">
            <i class="fas fa-clipboard-list"></i>
            <h3>@lang('l.no_tests_available')</h3>
            <p>@lang('l.no_tests_description')</p>
        </div>
    @endif
</div>
@endsection

@section('js')
<style>
    .swal2-container.swal2-backdrop-show,
    .swal2-container.swal2-noanimation {
        background: #ffffff !important;
    }

    .final-elegant-popup {
        border-radius: 22px !important;
        box-shadow: 0 25px 80px rgba(15, 23, 42, 0.18) !important;
        padding: 2.2rem 2rem 1.8rem !important;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif !important;
    }

    .final-elegant-title {
        font-size: 28px !important;
        font-weight: 800 !important;
        color: #0f172a !important;
        margin-bottom: 10px !important;
    }

    .final-elegant-html {
        font-size: 16px !important;
        line-height: 1.8 !important;
        color: #475569 !important;
        margin-top: 8px !important;
    }

    .final-elegant-confirm {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%) !important;
        color: #fff !important;
        border: none !important;
        border-radius: 12px !important;
        padding: 12px 28px !important;
        font-size: 15px !important;
        font-weight: 700 !important;
        box-shadow: 0 10px 25px rgba(37, 99, 235, 0.28) !important;
    }
</style>

<script>
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: @json(session('success')),
            confirmButtonText: 'OK',
            confirmButtonColor: '#2563eb'
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'warning',
            title: 'Unable to continue',
            text: @json(session('error')),
            confirmButtonText: 'Back to Home',
            confirmButtonColor: '#2563eb',
            backdrop: 'rgba(255,255,255,1)',
            background: '#ffffff',
            allowOutsideClick: false
        }).then(function(){
            window.location.href = "{{ url('/') }}";
        });
    @endif

    @if($errors->any())
        Swal.fire({
            icon: 'warning',
            title: 'Unable to continue',
            html: `{!! implode('<br>', $errors->all()) !!}`,
            confirmButtonText: 'Back to Home',
            allowOutsideClick: false,
            allowEscapeKey: false,
            backdrop: '#ffffff',
            background: '#ffffff',
            width: 560,
            customClass: {
                popup: 'final-elegant-popup',
                title: 'final-elegant-title',
                htmlContainer: 'final-elegant-html',
                confirmButton: 'final-elegant-confirm'
            }
        }).then(() => {
            window.location.href = "{{ url('/') }}";
        });
    @endif

    function setTestsView(viewType) {
        const cardsBlocks = document.querySelectorAll('.tests-view-block[data-view="cards"]');
        const tableBlocks = document.querySelectorAll('.tests-view-block[data-view="table"]');
        const cardsBtn = document.getElementById('cardsToggleBtn');
        const tableBtn = document.getElementById('tableToggleBtn');

        if (viewType === 'table') {
            cardsBlocks.forEach(el => el.style.display = 'none');
            tableBlocks.forEach(el => el.style.display = 'block');
            cardsBtn?.classList.remove('active');
            tableBtn?.classList.add('active');
        } else {
            cardsBlocks.forEach(el => el.style.display = 'block');
            tableBlocks.forEach(el => el.style.display = 'none');
            tableBtn?.classList.remove('active');
            cardsBtn?.classList.add('active');
        }

        localStorage.setItem('testsViewMode', viewType);
    }

    $(document).ready(function() {
        $('.course-section').each(function(index) {
            $(this).css('opacity', '0').css('transform', 'translateY(30px)')
                .delay(index * 200)
                .animate({ opacity: 1 }, 600)
                .css('transform', 'translateY(0)');
        });

        $('.test-card').hover(
            function() { $(this).find('.stat-item').css('transform', 'translateY(-3px)'); },
            function() { $(this).find('.stat-item').css('transform', 'translateY(0)'); }
        );

        $('.btn-test').on('click', function() {
            $(this).css('transform', 'scale(0.95)');
            setTimeout(() => { $(this).css('transform', ''); }, 150);
        });

        const form = document.getElementById('filtersForm');
        const levelSelect = document.getElementById('levelSelect');
        const courseSelect = document.getElementById('courseSelect');

        if (form && levelSelect && courseSelect) {
            levelSelect.addEventListener('change', function () {
                courseSelect.value = '';
                form.submit();
            });

            courseSelect.addEventListener('change', function () {
                form.submit();
            });
        }

        const savedView = localStorage.getItem('testsViewMode') || 'cards';
        setTestsView(savedView);
    });
</script>
@endsection
