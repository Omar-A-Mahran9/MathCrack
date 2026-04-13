@extends('themes.default.layouts.back.student-master')

@section('title')
    @lang('l.tests')
@endsection

@section('css')
<style>
    /* === spacing tweaks === */
    .course-section { margin-bottom: 30px !important; }
    .course-header { padding: 25px 30px !important; }
    .tests-grid { padding: 25px 30px !important; }
    .test-card { margin-bottom: 20px !important; }
    .card-body { padding: 20px !important; }
    .test-stats { margin-bottom: 15px !important; gap: 12px !important; }
    .test-status { margin-bottom: 15px !important; }
    .test-actions { gap: 10px !important; }
    .course-purchase-section { padding: 20px 25px !important; margin-top: 20px !important; }
    .course-purchase-info { margin-bottom: 15px !important; }
    .course-meta { margin-top: 12px !important; gap: 20px !important; }

    /* page header spacing */
    .page-headers { margin-bottom: 30px !important; padding: 40px 0 !important; }

    /* fix header two lines */
    .page-header h1 {
        margin: 0 !important;
        font-size: 2.5rem !important;
        font-weight: 300 !important;
        color: white !important;
        position: relative !important;
        z-index: 2 !important;
        display: block !important;
        width: 100% !important;
        margin-bottom: 10px !important;
    }

    .page-header p {
        margin: 0 !important;
        opacity: 0.9 !important;
        color: white !important;
        font-size: 1.1rem !important;
        position: relative !important;
        z-index: 2 !important;
        display: block !important;
        width: 100% !important;
    }

    /* main card styles */
    .course-section {
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
        background: rgba(255,255,255,0.1);
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
        color: rgba(255,255,255,0.95);
        font-size: 1rem;
        font-weight: 500;
    }

    .course-meta-item i { font-size: 1.1rem; }

    .test-card {
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

    .card-header::after {
        content: '';
        position: absolute;
        bottom: -10px;
        left: 0;
        width: 100%;
        height: 20px;
        clip-path: polygon(0 0, 100% 0, 100% 100%, 0 0);
        opacity: 0.8;
    }

    .test-title {
        font-size: 1.3rem;
        font-weight: 700;
        color: white;
        margin-bottom: 5px;
        margin-top: 0;
    }

    .test-description {
        color: rgba(255,255,255,0.9);
        font-size: 0.9rem;
        margin-bottom: 0;
        line-height: 1.5;
    }

    .test-stats { display: grid; grid-template-columns: repeat(4, 1fr); }

    .stat-item {
        background: #f8fafc;
        padding: 15px;
        border-radius: 12px;
        text-align: center;
        transition: all 0.3s ease;
        border: 1px solid #f1f5f9;
    }

    .stat-item:hover { background: #f1f5f9; transform: translateY(-2px); }

    .stat-number {
        font-size: 1.3rem;
        font-weight: 700;
        color: #1e40af;
        display: block;
        margin-bottom: 5px;
    }

    .stat-label { font-size: 0.85rem; color: #64748b; font-weight: 500; }

    .price-section {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        padding: 15px;
        border-radius: 12px;
        text-align: center;
        margin-bottom: 20px;
    }

    .price-amount { font-size: 1.4rem; font-weight: 700; margin-bottom: 5px; }
    .price-label { font-size: 0.9rem; opacity: 0.9; }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        border-radius: 25px;
        font-size: 0.9rem;
        font-weight: 600;
    }

    .status-not-started { background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); color: #475569; }
    .status-in-progress { background: linear-gradient(135deg, #fef3c7 0%, #f59e0b 100%); color: #92400e; }
    .status-completed { background: linear-gradient(135deg, #d1fae5 0%, #10b981 100%); color: #065f46; }
    .status-locked { background: linear-gradient(135deg, #fee2e2 0%, #ef4444 100%); color: #991b1b; }

    .test-actions { display: flex; }

    .btn-test {
        flex: 1;
        padding: 12px 20px;
        border-radius: 10px;
        font-weight: 600;
        text-decoration: none;
        text-align: center;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        font-size: 0.9rem;
    }

    .btn-primary-test { background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); color: white; border: none; }
    .btn-primary-test:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(30, 64, 175, 0.4); color: white; }

    .btn-secondary-test { background: white; color: #64748b; border: 2px solid #e2e8f0; }
    .btn-secondary-test:hover { border-color: #3b82f6; color: #3b82f6; transform: translateY(-2px); }

    .btn-success-test { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border: none; }
    .btn-success-test:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(16, 185, 129, 0.4); color: white; }

    .btn-warning-test { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; border: none; }
    .btn-warning-test:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(245, 158, 11, 0.4); color: white; }

    .course-purchase-section {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        border-top: 1px solid #e2e8f0;
        text-align: center;
        border-radius: 0 0 20px 20px;
    }

    .course-purchase-info { margin-bottom: 20px; }
    .course-purchase-price { font-size: 1.5rem; font-weight: 700; color: #1e40af; margin-bottom: 8px; }
    .course-purchase-desc { color: #64748b; font-size: 1rem; }

    .no-tests { text-align: center; padding: 80px 20px; color: #64748b; }
    .no-tests i { font-size: 5rem; margin-bottom: 25px; color: #cbd5e1; }
    .no-tests h3 { font-size: 1.8rem; margin-bottom: 15px; color: #475569; }

    .page-headers {
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
        background: rgba(255,255,255,0.1) !important;
        transform: skewX(-15deg);
    }

    .stats-summary { position: relative; z-index: 2; }

    .badge {
        background: rgba(255,255,255,0.2) !important;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.3) !important;
        color: white !important;
    }

    /* colored headers */
    .test-card:nth-child(3n+1) .card-header { background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); }
    .test-card:nth-child(3n+2) .card-header { background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%); }
    .test-card:nth-child(3n+3) .card-header { background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%); }

    .test-card:nth-child(3n+1) .btn-primary-test { background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); }
    .test-card:nth-child(3n+2) .btn-primary-test { background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%); }
    .test-card:nth-child(3n+3) .btn-primary-test { background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%); }

    /* remove white background layering */
    .page-headers .container-fluid,
    .page-headers .row,
    .page-headers .col-md-8,
    .page-headers .col-md-4,
    .page-headers .page-header,
    .page-headers .stats-summary { background: transparent !important; }

    .page-headers h1,
    .page-headers p,
    .page-headers .badge,
    .course-header .course-title,
    .course-meta-item,
    .course-meta-item span,
    .course-meta-item i {
        color: white !important;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
    }

    .badge.bg-light {
        background: rgba(255, 255, 255, 0.2) !important;
        border: 1px solid rgba(255, 255, 255, 0.3) !important;
        color: white !important;
        backdrop-filter: blur(10px);
    }

    @media (max-width: 768px) {
        .course-header { padding: 20px !important; }
        .course-title { font-size: 1.5rem; }
        .course-meta { gap: 15px; }
        .test-actions { flex-direction: column; }
        .page-header h1 { font-size: 2rem !important; }
        .page-headers { padding: 30px 0 !important; }
    }

    /* vertical spacing unify */
    .page-headers { margin-bottom: 5px !important; }
    .course-section { margin-bottom: 5px !important; }
    .course-header { margin-bottom: 25px !important; }

    .tests-grid .col-xl-4,
    .tests-grid .col-lg-6,
    .tests-grid .col-md-6 { margin-bottom: 10px !important; }

    .course-purchase-section { margin-top: 20px !important; }

    @media (max-width: 768px) {
        .page-headers { margin-bottom: 10px !important; }
        .course-section { margin-bottom: 10px !important; }
    }

        /* تكبير خطوط الفلترة بشكل بسيط */
    #filtersForm label.form-label {
        font-size: 1.5rem !important;
        font-weight: 700 !important;
        color: #1e40af !important;
    }

    #filtersForm .form-select,
    #filtersForm .form-select option {
        font-size: 1.2em !important;
        font-weight: 600 !important;
    }


    /* خلفية الفلترة */
        
       
    #filtersForm {
        background: linear-gradient(135deg, #239BA7 0%, #48B3AF 100%) !important;
        padding: 28px 30px !important;
        border-radius: 22px !important;
        box-shadow: 
            0 10px 30px rgba(30, 64, 175, 0.08),
            0 4px 12px rgba(0, 0, 0, 0.04),
            inset 0 1px 0 rgba(255, 255, 255, 0.8) !important;
        margin-bottom: 40px !important;
        border: 1px solid #e2e8f0 !important;
        position: relative !important;
        overflow: hidden !important;
    }

    /* تأثيرات الزوايا */
    #filtersForm::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #3b82f6, #8b5cf6, #10b981) !important;
        border-radius: 22px 22px 0 0 !important;
    }

    /* ظل داخلي */
    #filtersForm {
        box-shadow: 
            inset 0 2px 4px rgba(255, 255, 255, 0.8),
            0 8px 25px rgba(0, 0, 0, 0.06) !important;
    }

    /* تعديل الخطوط للتناسب مع الخلفية */
    #filtersForm label.form-label {
        color: #0c0c0cff !important;
        font-weight: 700 !important;
        text-shadow: 0 1px 1px rgba(255, 255, 255, 0.9);
    }

    #filtersForm .form-select {
        background-color: rgba(255, 255, 255, 0.9) !important;
        backdrop-filter: blur(5px) !important;
        border: 2px solid #cbd5e0 !important;
    }


        /* === إصلاح التداخل مع العناوين === */
    .page-headers {
        margin-bottom: 20px !important;
        padding-bottom: 30px !important;
    }

    #filtersForm {
        margin-top: -10px !important; /* رفع الفلترة للأعلى قليلاً */
        position: relative;
        z-index: 10; /* تأكد من ظهورها فوق العناصر الأخرى */
    }


    #filtersForm {
        /* الأبعاد */
        width: 100% !important;
        height: auto !important;
        min-height: 130px !important;
    }

        /* === حل شامل مع تقليل المسافات === */
    
    /* 1. تقليل Padding العلوي للفورم */
    #filtersForm {
        padding: 10px 25px 20px 25px !important; /* top reduced */
        margin-top: 0 !important;
    }

    /* 2. تقليل المسافة العلوية للـ Labels */
    #filtersForm .form-label {
        margin-top: -2px !important;
        margin-bottom: 5px !important;
        padding: 0 !important;
        display: block !important;
        position: relative !important;
        top: -2px !important;
    }

    /* 3. تقليل المسافة بين العناصر في الصف */
    #filtersForm .row.g-3 {
        --bs-gutter-y: 0.5rem !important; /* تقليل المسافة الرأسية */
        margin-top: -5px !important;
    }

    /* 4. تقليل ارتفاع الحاوية */
    #filtersForm {
        min-height: 100px !important; /* تقليل الارتفاع الأدنى */
    }

    /* 5. تحريك كل المحتوى لأعلى */
    #filtersForm .col-md-4 {
        padding-top: 0 !important;
        margin-top: -5px !important;
    }


        /* === الحل الأبسط === */
    
    /* ببساطة اضبط margin-top و margin-bottom */
    form#filtersForm {
        margin-top: 12px !important;     /* المسافة من الأعلى */
        margin-bottom: 18px !important;  /* المسافة من الأسفل */
    }
    
    /* تأكد من عدم وجود margins أخرى */
    .main-content > form {
        margin-left: auto !important;
        margin-right: auto !important;
    }
</style>

@endsection

@section('content')
<div class="main-content">
    <!-- Page Header -->
    <div class="page-headers">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="page-header-content">
                        <h1 class="page-main-title">Practice Tests</h1>
                        <p class="page-subtitle">Explore available practice tests and improve your skills</p>
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <div class="stats-summary">
                        @php
                            $totalTests = $coursesWithTests->sum(function($course) {
                                return $course['tests']->count();
                            });
                        @endphp
                        <span class="stats-badge">
                            <i class="fas fa-clipboard-list me-2"></i>
                            {{ $totalTests }} Tests Available
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif -->

    <!-- Filters (no Filter button) -->
    <!-- في قسم الفلاتر -->
<form id="filtersForm" method="GET" action="{{ route('dashboard.users.tests.index') }}" class="row g-3 mb-3">
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
    @if($coursesWithTests->count() > 0)
        @foreach($coursesWithTests as $course)
            <div class="course-section">
                <!-- Course Header -->
                <div class="course-header">
                    <h2 class="course-title">{{ $course['name'] }}</h2>
                    <div class="course-meta">
                        <div class="course-meta-item">
                            <i class="fas fa-clipboard-list"></i>
                            <span>{{ $course['tests']->count() }} @lang('l.tests')</span>
                        </div>
                        @if($course['tests_price'] > 0)
                            <div class="course-meta-item">
                                <i class="fas fa-tag"></i>
                                <span>@lang('l.course_tests_price'): {{ number_format($course['tests_price'], 2) }} @lang('l.currency')</span>
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

                <!-- Tests Grid -->
                <div class="tests-grid">
                    <div class="row">
                        @foreach($course['tests'] as $test)
                            <div class="col-xl-4 col-lg-6 col-md-6 mb-4">
                                <div class="test-card">
                                    <!-- Card Header -->
                                    <div class="card-header">
                                        <h3 class="test-title">{{ $test['name'] }}</h3>
                                        @if($test['description'])
                                            <p class="test-description">{{ Str::limit($test['description'], 80) }}</p>
                                        @endif
                                    </div>

                                    <!-- Card Body -->
                                    <div class="card-body">
                                        @php
                                            $testModel = \App\Models\Test::find($test['id']);

                                            $modulesCount   = 0;
                                            $questionsTotal = 0;
                                            $timeTotal      = 0;

                                            if ($testModel) {
                                                foreach (range(1, 5) as $i) {
                                                    $qCol = "part{$i}_questions_count";
                                                    $tCol = "part{$i}_time_minutes";

                                                    $q = (int)($testModel->{$qCol} ?? 0);
                                                    $t = (int)($testModel->{$tCol} ?? 0);

                                                    if ($q > 0 || $t > 0) {
                                                        $modulesCount++;
                                                        $questionsTotal += $q;
                                                        $timeTotal      += $t;
                                                    }
                                                }

                                                if ($modulesCount === 0) {
                                                    $modulesCount   = 1;
                                                    $questionsTotal = (int)($testModel->total_questions ?? $test['total_questions'] ?? 0);
                                                    $timeTotal      = (int)($testModel->total_time      ?? $test['total_time']      ?? 0);
                                                }
                                            } else {
                                                $modulesCount   = 1;
                                                $questionsTotal = (int)($test['total_questions'] ?? 0);
                                                $timeTotal      = (int)($test['total_time']      ?? 0);
                                            }
                                        @endphp

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

                                        <!-- Price Section -->
                                        @if($test['price'] > 0 && !$test['has_paid'])
                                            <div class="price-section">
                                                <div class="price-amount">{{ number_format($test['price'], 2) }} @lang('l.currency')</div>
                                                <div class="price-label">@lang('l.test_price')</div>
                                            </div>
                                        @endif

                                        <!-- Test Status -->
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
                                                    @case('in_break')
                                                    @case('part2_in_progress')
                                                        <span class="status-badge status-in-progress">
                                                            <i class="fas fa-clock"></i>
                                                            @lang('l.in_progress')
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

                                        <!-- Test Actions -->
                                        <div class="test-actions">
                                            @if($test['has_paid'])
                                                @switch($test['status'])
                                                    @case('not_started')
                                                        <a href="{{ route('dashboard.users.tests.show', $test['id']) }}" class="btn-test btn-primary-test">
                                                            <i class="fas fa-play"></i>
                                                            @lang('l.start_test')
                                                        </a>
                                                        @break
                                                    @case('part1_in_progress')
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
                                                        @lang('l.access_free_test')
                                                    </a>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Course Tests Purchase Option -->
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
                                @lang('l.purchase_all_tests')
                            </a>
                        </div>
                    @endif
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

    .final-elegant-confirm:focus {
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.18) !important;
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
    });
</script>
@endsection