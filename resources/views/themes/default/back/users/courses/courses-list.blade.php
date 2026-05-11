@extends('themes.default.layouts.back.student-master')

@section('title')
    @lang('l.my_courses')
@endsection

@section('css')
    <style>
        .course-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            height: 100%;
            cursor: pointer;
            overflow: hidden;
        }

        .course-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .course-image {
            height: 200px;
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            position: relative;
            overflow: hidden;
        }

        .course-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .course-card:hover .course-image img {
            transform: scale(1.05);
        }

        .course-image .overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(30, 64, 175, 0.8), rgba(59, 130, 246, 0.8));
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .course-card:hover .course-image .overlay {
            opacity: 1;
        }

        .course-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .course-meta {
            color: #7f8c8d;
            font-size: 0.9rem;
        }

        .course-stats {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #ecf0f1;
        }

        .stat-item {
            text-align: center;
            flex: 1;
        }

        .stat-number {
            display: block;
            font-size: 1.1rem;
            font-weight: 600;
            color: #1e40af;
        }

        .stat-label {
            font-size: 0.8rem;
            color: #95a5a6;
            margin-top: 5px;
        }

        .no-courses {
            text-align: center;
            padding: 50px 20px;
            color: #7f8c8d;
        }

        .no-courses i {
            font-size: 4rem;
            margin-bottom: 20px;
            color: #bdc3c7;
        }

        .page-headers {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            color: white;
            padding: 30px 0;
            margin-bottom: 30px;
            border-radius: 15px;
        }

        .page-header h1,
        .page-headers h1 {
            margin: 0;
            font-size: 2rem;
            font-weight: 700;
            color: white !important;
        }

        .page-header p,
        .page-headers p {
            margin: 10px 0 0 0;
            opacity: 0.95;
            color: white !important;
        }

        .track-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.16);
            color: #fff;
            padding: 8px 16px;
            border-radius: 999px;
            font-weight: 700;
            margin-bottom: 14px;
        }

        .track-tabs-wrap {
            background: #fff;
            border-radius: 16px;
            padding: 12px;
            margin-bottom: 28px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08);
        }

        .track-tabs {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .track-tabs .nav-link {
            border: 0;
            border-radius: 12px;
            color: #334155;
            font-weight: 700;
            padding: 12px 18px;
            background: #f1f5f9;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .track-tabs .nav-link.active {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            color: #fff;
        }

        .track-tab-panel {
            margin-top: 24px;
        }

        .placeholder-panel {
            background: #fff;
            border-radius: 16px;
            padding: 40px 24px;
            text-align: center;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08);
            color: #64748b;
        }

        .placeholder-panel i {
            font-size: 3rem;
            color: #3b82f6;
            margin-bottom: 18px;
        }

        .placeholder-panel h3 {
            color: #1e293b;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .course-count-label {
            white-space: nowrap;
        }
        .progress-overview {
            background: #fff;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08);
        }

        .progress-summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .progress-summary-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 18px;
            text-align: center;
        }

        .progress-summary-number {
            display: block;
            font-size: 1.8rem;
            font-weight: 800;
            color: #1e40af;
            margin-bottom: 4px;
        }

        .progress-summary-label {
            color: #64748b;
            font-weight: 700;
            font-size: 0.92rem;
        }

        .track-progress-bar {
            height: 12px;
            background: #e5e7eb;
            border-radius: 999px;
            overflow: hidden;
            margin: 12px 0 10px;
        }

        .track-progress-fill {
            height: 100%;
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            border-radius: 999px;
        }

        .progress-course-list {
            display: grid;
            gap: 14px;
        }

        .progress-course-item {
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 16px;
            background: #fff;
        }

        .progress-course-title {
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 8px;
        }

        .progress-course-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            color: #64748b;
            font-weight: 700;
            font-size: 0.92rem;
        }

        .progress-course-meta span {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #f1f5f9;
            border-radius: 999px;
            padding: 7px 12px;
        }

        .progress-practice-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            color: #fff !important;
            text-decoration: none;
            border-radius: 999px;
            padding: 9px 14px;
            font-weight: 800;
            transition: all 0.25s ease;
        }

        .progress-practice-link:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 18px rgba(30, 64, 175, 0.22);
            color: #fff !important;
        }


        @media (max-width: 768px) {
            .page-headers {
                padding: 24px 0;
            }

            .page-headers h1 {
                font-size: 1.6rem;
            }

            .track-tabs .nav-link {
                width: 100%;
                justify-content: center;
            }

            .stats-summary {
                margin-top: 15px;
                text-align: start;
            }
        }
    </style>
@endsection

@section('content')
    @php
        $track = request()->query('track');

        $trackTitles = [
            'digital-sat' => 'Digital SAT',
            'est-i' => 'EST I',
            'est-ii' => 'EST II',
            'act-i' => 'ACT I',
            'act-ii' => 'ACT II',
            'ap-math' => 'AP Math',
            'calculus' => 'Calculus',
            'linear-algebra' => 'Linear Algebra',
            'statistics' => 'Statistics',
            'university-math-support' => 'University Math Support',
        ];

        $trackTitle = $trackTitles[$track] ?? null;

        $displayCourses = $courses;

        if ($track === 'digital-sat') {
            $displayCourses = $courses->filter(function ($course) {
                return ($course->lectures_count ?? 0) > 0;
            });

            if ($displayCourses->count() === 0) {
                $displayCourses = $courses;
            }
        }

        $displayedCoursesCount = $displayCourses->count();
        $courseCountLabel = $track === 'digital-sat' ? __('l.course_count') : ($trackTitle ? __('l.course_parts') : __('l.courses_available'));
    @endphp

    <div class="main-content">
        <!-- Page Header -->
        <div class="page-headers">
            <div class="container-fluid">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        @if($trackTitle)
                            <div class="track-badge">
                                <i class="fas fa-route"></i>
                                <span>{{ $trackTitle }} @lang('l.track')</span>
                            </div>
                            <h1>{{ $trackTitle }}</h1>
                            <p>
                                @if($track === 'digital-sat')
                                    @lang('l.digital_sat_learning_path')
                                @else
                                    {{ $trackTitle }}
                                @endif
                            </p>
                        @else
                            <h1>@lang('l.my_courses')</h1>
                            <p>@lang('l.explore_your_courses_description')</p>
                        @endif
                    </div>

                    <div class="col-md-4 text-end">
                        <div class="stats-summary">
                            <span class="badge bg-light text-dark fs-6 px-3 py-2 course-count-label">
                                {{ $displayedCoursesCount }} {{ $courseCountLabel }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($trackTitle)
            <div class="track-tabs-wrap">
                <ul class="nav track-tabs" id="trackTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button
                            class="nav-link active"
                            id="course-tab"
                            data-bs-toggle="tab"
                            data-bs-target="#course-panel"
                            type="button"
                            role="tab"
                            aria-controls="course-panel"
                            aria-selected="true"
                        >
                            <i class="fas fa-book-open"></i>
                            Course
                        </button>
                    </li>

                    <li class="nav-item" role="presentation">
                        <a
                            class="nav-link"
                            href="{{ route('dashboard.users.tests.index', ['track' => request('track')]) }}"
                        >
                            <i class="fas fa-pen"></i>
                            Practice Tests
                        </a>
                    </li>

                    <li class="nav-item" role="presentation">
                        <button
                            class="nav-link"
                            id="progress-tab"
                            data-bs-toggle="tab"
                            data-bs-target="#progress-panel"
                            type="button"
                            role="tab"
                            aria-controls="progress-panel"
                            aria-selected="false"
                        >
                            <i class="fas fa-chart-line"></i>
                            Progress
                        </button>
                    </li>
                </ul>
            </div>

            <div class="tab-content" id="trackTabsContent">
                <div
                    class="tab-pane fade show active track-tab-panel"
                    id="course-panel"
                    role="tabpanel"
                    aria-labelledby="course-tab"
                >
        @endif

        @if($displayCourses->count() > 0)
            <div class="row">
                @foreach($displayCourses as $course)
                    <div class="col-xl-4 col-lg-6 col-md-6 mb-4">
                        <div class="card course-card" onclick="viewCourse('{{ encrypt($course->id) }}')">
                            <div class="course-image">
                                @if($course->image)
                                    <img src="{{ asset($course->image) }}" alt="{{ $course->name }}">
                                @else
                                    <div class="d-flex align-items-center justify-content-center h-100">
                                        <i class="fas fa-graduation-cap fa-4x text-white opacity-50"></i>
                                    </div>
                                @endif
                                <div class="overlay">
                                    <i class="fas fa-eye fa-2x text-white"></i>
                                </div>
                            </div>
                            <div class="card-body">
                                @php
                                    $displayCardCourseName = $course->name;

                                    if (request('track') === 'digital-sat') {
                                        $displayCardCourseName = __('l.digital_sat_course');
                                    }
                                @endphp
                                <h5 class="course-title">{{ $displayCardCourseName }}</h5>
                                <div class="course-meta">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-layer-group me-2 text-primary"></i>
                                        <span>{{ $course->level->name ?? '-' }}</span>
                                    </div>
                                    @if($course->price)
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-tag me-2 text-success"></i>
                                            <span>{{ $course->price }} @lang('l.currency')</span>
                                        </div>
                                    @else
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-gift me-2 text-success"></i>
                                            <span class="text-success">@lang('l.Free')</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="course-stats">
                                    <div class="stat-item">
                                        <span class="stat-number">{{ $course->lectures_count }}</span>
                                        <div class="stat-label">@lang('l.lessons')</div>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-number">{{ $course->lectures->sum(function($lecture) { return $lecture->assignments->count(); }) }}</span>
                                        <div class="stat-label">@lang('l.assignments')</div>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-number">
                                            <i class="fas fa-calendar-alt text-muted"></i>
                                        </span>
                                        <div class="stat-label">{{ $course->updated_at->diffForHumans() }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="no-courses">
                <i class="fas fa-graduation-cap"></i>
                <h3>@lang('l.no_courses_available')</h3>
                <p>@lang('l.no_courses_description')</p>
            </div>
        @endif

        @if($trackTitle)
                </div>


                <div
                    class="tab-pane fade track-tab-panel"
                    id="progress-panel"
                    role="tabpanel"
                    aria-labelledby="progress-tab"
                >
                    @php
                        $totalLessons = 0;
                        $totalAssignments = 0;
                        $completedAssignments = 0;
                        $practiceTestsCount = 0;

                        foreach ($displayCourses as $course) {
                            $totalLessons += (int) ($course->lectures_count ?? $course->lectures->count());

                            if (method_exists($course, 'activeTests')) {
                                $practiceTestsCount += (int) $course->activeTests()->count();
                            }

                            foreach ($course->lectures as $lecture) {
                                foreach ($lecture->assignments as $assignment) {
                                    $totalAssignments++;

                                    $studentAssignment = $assignment->studentAssignments
                                        ->where('student_id', auth()->id())
                                        ->first();

                                    if ($studentAssignment && $studentAssignment->submitted_at) {
                                        $completedAssignments++;
                                    }
                                }
                            }
                        }

                        $assignmentProgress = $totalAssignments > 0
                            ? round(($completedAssignments / $totalAssignments) * 100)
                            : 0;
                    @endphp

                    <div class="progress-overview">
                        <div class="progress-summary-grid">
                            <div class="progress-summary-card">
                                <span class="progress-summary-number">{{ $totalLessons }}</span>
                                <div class="progress-summary-label">@lang('l.lessons')</div>
                            </div>

                            <div class="progress-summary-card">
                                <span class="progress-summary-number">{{ $completedAssignments }}/{{ $totalAssignments }}</span>
                                <div class="progress-summary-label">@lang('l.assignments_completed')</div>
                            </div>

                            <div class="progress-summary-card">
                                <span class="progress-summary-number">{{ $assignmentProgress }}%</span>
                                <div class="progress-summary-label">@lang('l.assignment_progress')</div>
                            </div>

                            <div class="progress-summary-card">
                                <span class="progress-summary-number">{{ $practiceTestsCount }}</span>
                                <div class="progress-summary-label">Practice Tests</div>
                            </div>
                        </div>

                        <div>
                            <div class="d-flex justify-content-between align-items-center">
                                <strong>Overall @lang('l.assignment_progress')</strong>
                                <strong>{{ $assignmentProgress }}%</strong>
                            </div>

                            <div class="track-progress-bar">
                                <div class="track-progress-fill" style="width: {{ $assignmentProgress }}%;"></div>
                            </div>
                        </div>

                        <div class="progress-course-list mt-4">
                            @foreach($displayCourses as $course)
                                @php
                                    $displayProgressCourseName = $course->name;

                                    if (request('track') === 'digital-sat') {
                                        $displayProgressCourseName = __('l.digital_sat_course');
                                    }

                                    $courseAssignmentsCount = $course->lectures->sum(function($lecture) {
                                        return $lecture->assignments->count();
                                    });
                                @endphp

                                <div class="progress-course-item">
                                    <div class="progress-course-title">{{ $displayProgressCourseName }}</div>

                                    <div class="progress-course-meta">
                                        <span>
                                            <i class="fas fa-book-open"></i>
                                            {{ $course->lectures_count }} Lessons
                                        </span>

                                        <span>
                                            <i class="fas fa-tasks"></i>
                                            {{ $courseAssignmentsCount }} Assignments
                                        </span>

                                        @if(request('track'))
                                            <a
                                                href="{{ route('dashboard.users.tests.index', ['track' => request('track')]) }}"
                                                class="progress-practice-link"
                                            >
                                                <i class="fas fa-pen"></i>
                                                @lang('l.go_to_practice_tests')
                                            </a>
                                        @else
                                            <span>
                                                <i class="fas fa-pen"></i>
                                                @lang('l.practice_tests_available')
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@section('js')
    <script>
        function viewCourse(courseId) {
            window.location.href = "{{ route('dashboard.users.courses-lectures') }}?id=" + courseId;
        }

        // Add some animation on page load
        $(document).ready(function() {
            $('.course-card').each(function(index) {
                $(this).css('opacity', '0').delay(index * 100).animate({
                    opacity: 1
                }, 500);
            });
        });
    </script>
@endsection
