@extends('themes.default.layouts.back.student-master')

@section('title')
    {{ $lecture->name }} - Lesson
@endsection

@section('css')
    <link rel="stylesheet" href="https://cdn.plyr.io/3.7.8/plyr.css" />

    <style>
        html {
            scroll-behavior: smooth;
        }

        .lesson-hero {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            border-radius: 16px;
            padding: 28px;
            margin-bottom: 24px;
            position: relative;
            overflow: hidden;
            color: #fff;
        }

        .lesson-hero::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at 20% 20%, rgba(255,255,255,0.16), transparent 26%),
                radial-gradient(circle at 85% 15%, rgba(255,255,255,0.12), transparent 22%);
            pointer-events: none;
        }

        .lesson-hero-content {
            position: relative;
            z-index: 2;
        }

        .lesson-hero h1 {
            color: #fff !important;
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 10px;
            line-height: 1.35;
        }

        .lesson-hero p {
            color: rgba(255,255,255,0.92) !important;
            margin-bottom: 0;
        }

        .hero-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 18px;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255,255,255,0.16);
            border: 1px solid rgba(255,255,255,0.24);
            border-radius: 999px;
            padding: 8px 14px;
            color: #fff;
            font-weight: 700;
            backdrop-filter: blur(10px);
            text-decoration: none;
            cursor: pointer;
        }

        .hero-badge:hover {
            color: #fff;
            background: rgba(255,255,255,0.26);
            transform: translateY(-2px);
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255,255,255,0.16);
            border: 1px solid rgba(255,255,255,0.28);
            border-radius: 10px;
            padding: 9px 15px;
            color: #fff;
            text-decoration: none;
            font-weight: 700;
            transition: all 0.25s ease;
            margin-bottom: 18px;
        }

        .back-btn:hover {
            color: #fff;
            background: rgba(255,255,255,0.26);
            transform: translateY(-2px);
        }

        .lesson-cover {
            max-height: 190px;
            border-radius: 14px;
            object-fit: cover;
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.24);
        }

        .lesson-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: #fff;
            border-radius: 14px;
            padding: 18px;
            text-align: center;
            border: 1px solid #e5e7eb;
            box-shadow: 0 8px 22px rgba(15, 23, 42, 0.06);
        }

        .stat-number {
            display: block;
            color: #1e40af;
            font-size: 1.7rem;
            font-weight: 900;
            margin-bottom: 4px;
        }

        .stat-label {
            color: #64748b;
            font-weight: 700;
            font-size: 0.9rem;
        }

        .lesson-card {
            background: #fff;
            border-radius: 16px;
            padding: 22px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
            margin-bottom: 22px;
        }

        .lesson-card-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.25rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 16px;
        }

        .lesson-card-title i {
            color: #2563eb;
        }

        .video-container {
            position: relative;
            width: 100%;
            height: 600px;
            background: #000;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 14px 35px rgba(0, 0, 0, 0.22);
        }

        .video-container .plyr,
        .video-container .plyr__video-wrapper,
        .video-container video,
        .video-container iframe,
        #plyr-video-player {
            width: 100% !important;
            height: 100% !important;
            min-height: 480px !important;
            max-height: none !important;
            background: #000;
        }

        .video-container .plyr__video-embed {
            height: 100% !important;
            padding-bottom: 0 !important;
        }

        .video-container .plyr__video-embed iframe {
            height: 100% !important;
        }

        @media (max-width: 768px) {
            .video-container {
                height: 260px;
            }

            .video-container .plyr,
            .video-container .plyr__video-wrapper,
            .video-container video,
            .video-container iframe,
            #plyr-video-player {
                min-height: 260px !important;
            }
        }

        .material-card {
            display: flex;
            align-items: center;
            gap: 16px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 16px;
            text-decoration: none;
            color: #0f172a;
            transition: all 0.25s ease;
        }

        .material-card:hover {
            color: #1e40af;
            border-color: #bfdbfe;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(30, 64, 175, 0.08);
        }

        .material-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #dbeafe;
            color: #1e40af;
            font-size: 1.5rem;
            flex: 0 0 auto;
        }

        .material-title {
            font-weight: 800;
            margin-bottom: 3px;
        }

        .material-meta {
            color: #64748b;
            font-size: 0.9rem;
        }

        .assignment-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-left: 5px solid #2563eb;
            border-radius: 16px;
            padding: 18px;
            margin-bottom: 16px;
            box-shadow: 0 6px 18px rgba(15, 23, 42, 0.06);
            transition: all 0.25s ease;
        }

        .assignment-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 26px rgba(15, 23, 42, 0.10);
        }

        .assignment-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 14px;
            margin-bottom: 12px;
        }

        .assignment-title {
            color: #0f172a;
            font-weight: 800;
            margin-bottom: 5px;
        }

        .assignment-description {
            color: #64748b;
            margin-bottom: 0;
        }

        .assignment-status {
            display: inline-flex;
            align-items: center;
            white-space: nowrap;
            padding: 7px 12px;
            border-radius: 999px;
            font-size: 0.8rem;
            font-weight: 800;
        }

        .status-not-started {
            background: #fee2e2;
            color: #991b1b;
        }

        .status-in-progress {
            background: #fef3c7;
            color: #92400e;
        }

        .status-completed {
            background: #dcfce7;
            color: #166534;
        }

        .assignment-meta {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 12px;
            margin: 14px 0;
        }

        .assignment-meta-item {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #f8fafc;
            border-radius: 12px;
            padding: 12px;
            color: #334155;
            font-weight: 700;
        }

        .assignment-meta-icon {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #e0f2fe;
            color: #0369a1;
            flex: 0 0 auto;
        }

        .progress-line {
            height: 8px;
            background: #e5e7eb;
            border-radius: 999px;
            overflow: hidden;
            margin: 12px 0 16px;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #22c55e, #16a34a);
            transition: width 0.3s ease;
        }

        .score-box {
            background: #f8fafc;
            border-radius: 12px;
            padding: 14px;
            margin-top: 14px;
        }

        .btn-custom {
            border-radius: 12px;
            padding: 11px 18px;
            font-weight: 800;
            text-decoration: none;
            transition: all 0.25s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-primary-custom {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            border: none;
            color: white;
        }

        .btn-primary-custom:hover {
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(30, 64, 175, 0.24);
        }

        .btn-success-custom {
            background: linear-gradient(135deg, #16a34a 0%, #22c55e 100%);
            border: none;
            color: white;
        }

        .btn-success-custom:hover {
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(22, 163, 74, 0.24);
        }

        .sidebar-card {
            background: #fff;
            border-radius: 16px;
            padding: 20px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
            margin-bottom: 22px;
        }

        .sidebar-title {
            display: flex;
            align-items: center;
            gap: 9px;
            color: #0f172a;
            font-weight: 800;
            margin-bottom: 16px;
        }

        .info-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 12px 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-icon {
            width: 38px;
            height: 38px;
            border-radius: 11px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #dbeafe;
            color: #1e40af;
            flex: 0 0 auto;
        }

        .empty-card {
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 14px;
            padding: 24px;
            text-align: center;
            color: #64748b;
        }

        .quick-action {
            width: 100%;
            margin-bottom: 10px;
        }

        .plyr {
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        .plyr__video-wrapper {
            pointer-events: none;
        }

        .plyr__controls {
            pointer-events: auto;
        }

        @media (max-width: 768px) {
            html {
            scroll-behavior: smooth;
        }

        .lesson-hero {
                padding: 20px;
            }

            .lesson-hero h1 {
                font-size: 1.55rem;
            }

            .assignment-header {
                flex-direction: column;
            }
        }
    </style>
@endsection

@section('content')
    @php
        $courseBackUrl = route('dashboard.users.courses-lectures', ['id' => encrypt($lecture->course->id)]);

        $displayCourseName = $lecture->course->name;

        if (($lecture->course->track_slug ?? null) === 'digital-sat') {
            $displayCourseName = __('l.digital_sat_course');
        }

        $youtubeId = null;

        if (!empty($lecture->video_url)) {
            $videoUrl = $lecture->video_url;

            if (str_contains($videoUrl, 'youtu.be/')) {
                $youtubeId = \Illuminate\Support\Str::after($videoUrl, 'youtu.be/');
                $youtubeId = \Illuminate\Support\Str::before($youtubeId, '?');
            } elseif (str_contains($videoUrl, 'v=')) {
                $youtubeId = \Illuminate\Support\Str::after($videoUrl, 'v=');
                $youtubeId = \Illuminate\Support\Str::before($youtubeId, '&');
            } elseif (str_contains($videoUrl, '/embed/')) {
                $youtubeId = \Illuminate\Support\Str::after($videoUrl, '/embed/');
                $youtubeId = \Illuminate\Support\Str::before($youtubeId, '?');
            }
        }
    @endphp

    <div class="main-content">
        <div class="container-fluid">
            <div class="lesson-hero">
                <div class="lesson-hero-content">
                    <div class="row align-items-center">
                        <div class="col-lg-8">
                            <a href="{{ $courseBackUrl }}" class="back-btn">
                                <i class="fas fa-arrow-left"></i>
                                @lang('l.back_to_course')
                            </a>

                            <h1>{{ $lecture->name }}</h1>

                            @if($lecture->description)
                                <p>{{ \Illuminate\Support\Str::limit(strip_tags($lecture->description), 180) }}</p>
                            @else
                                <p>@lang('l.video'), materials, and assignments for this lesson.</p>
                            @endif

                            <div class="hero-badges">
                                <a href="{{ $courseBackUrl }}" class="hero-badge">
                                    <i class="fas fa-book-open"></i>
                                    {{ $displayCourseName }}
                                </a>

                                @if($lecture->assignments->count() > 0)
                                    <a href="#Assignments" class="hero-badge">
                                        <i class="fas fa-tasks"></i>
                                        {{ $lecture->assignments->count() }} @lang('l.assignments')
                                    </a>
                                @endif

                                @if($lecture->files)
                                    <a href="#PDFMaterial" class="hero-badge">
                                        <i class="fas fa-file-alt"></i>
                                        @lang('l.pdf_material')
                                    </a>
                                @endif

                                @if($lecture->video_url)
                                    <a href="#@lang('l.video')Lesson" class="hero-badge">
                                        <i class="fas fa-play-circle"></i>
                                        @lang('l.video_lesson')
                                    </a>
                                @endif
                            </div>
                        </div>

                        <div class="col-lg-4 text-end mt-4 mt-lg-0">
                            @if ($lecture->image)
                                <img src="{{ asset($lecture->image) }}" alt="{{ $lecture->name }}" class="img-fluid lesson-cover">
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="lesson-stats">
                <div class="stat-card">
                    <span class="stat-number">{{ $lecture->assignments->count() }}</span>
                    <span class="stat-label">@lang('l.assignments')</span>
                </div>

                <div class="stat-card">
                    <span class="stat-number">{{ $lecture->files ? 1 : 0 }}</span>
                    <span class="stat-label">@lang('l.materials')</span>
                </div>

                <div class="stat-card">
                    <span class="stat-number">{{ $lecture->video_url ? 1 : 0 }}</span>
                    <span class="stat-label">@lang('l.video')</span>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    @if ($lecture->video_url)
                        <div class="lesson-card" id="@lang('l.video')Lesson">
                            <h3 class="lesson-card-title">
                                <i class="fas fa-play-circle"></i>
                                @lang('l.video_lesson')
                            </h3>

                            <div class="video-container">
                                @if($youtubeId)
                                    <div id="plyr-video-player"
                                        data-plyr-provider="youtube"
                                        data-plyr-embed-id="{{ $youtubeId }}"
                                        data-plyr-config='{
                                            "controls": ["play-large", "play", "progress", "current-time", "mute", "volume", "fullscreen", "settings"],
                                            "settings": ["speed"],
                                            "youtube": {"noCookie": true, "rel": 0, "showinfo": 0, "modestbranding": 1}
                                        }'>
                                    </div>
                                @else
                                    <video id="plyr-video-player" controls>
                                        <source src="{{ asset($lecture->video_url) }}" type="video/mp4">
                                    </video>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if ($lecture->description)
                        <div class="lesson-card">
                            <h3 class="lesson-card-title">
                                <i class="fas fa-align-left"></i>
                                @lang('l.lesson_description')
                            </h3>

                            <div class="text-muted">
                                {!! nl2br(e($lecture->description)) !!}
                            </div>
                        </div>
                    @endif

                    @if ($lecture->files)
                        <div class="lesson-card" id="PDFMaterial">
                            <h3 class="lesson-card-title">
                                <i class="fas fa-file-download"></i>
                                @lang('l.pdf_material')
                            </h3>

                            <a href="{{ asset($lecture->files) }}" target="_blank" class="material-card">
                                <div class="material-icon">
                                    <i class="fas fa-file-pdf"></i>
                                </div>

                                <div>
                                    <div class="material-title">@lang('l.open_lesson_material')</div>
                                    <div class="material-meta">
                                        @if (file_exists(public_path($lecture->files)))
                                            {{ round(filesize(public_path($lecture->files)) / 1024, 2) }} KB
                                        @else
                                            File attached
                                        @endif
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endif

                    @if ($lecture->assignments && $lecture->assignments->count() > 0)
                        <div class="lesson-card" id="Assignments">
                            <h3 class="lesson-card-title">
                                <i class="fas fa-tasks"></i>
                                @lang('l.assignments')
                                <span class="badge bg-secondary ms-2">{{ $lecture->assignments->count() }}</span>
                            </h3>

                            @foreach ($lecture->assignments as $assignment)
                                @php
                                    $userAssignment = $assignment->studentAssignments
                                        ->where('student_id', auth()->id())
                                        ->first();

                                    $status = 'not-started';
                                    $statusText = __('l.not_started');
                                    $progress = 0;

                                    if ($userAssignment) {
                                        if ($userAssignment->submitted_at) {
                                            $status = 'completed';
                                            $statusText = __('l.completed');
                                            $progress = 100;
                                        } elseif ($userAssignment->started_at) {
                                            $status = 'in-progress';
                                            $statusText = __('l.in_progress');
                                            $progress = 50;
                                        }
                                    }
                                @endphp

                                <div class="assignment-card">
                                    <div class="assignment-header">
                                        <div>
                                            <h5 class="assignment-title">{{ $assignment->title }}</h5>
                                            @if($assignment->description)
                                                <p class="assignment-description">{{ $assignment->description }}</p>
                                            @endif
                                        </div>

                                        <span class="assignment-status status-{{ $status }}">{{ $statusText }}</span>
                                    </div>

                                    <div class="assignment-meta">
                                        <div class="assignment-meta-item">
                                            <span class="assignment-meta-icon">
                                                <i class="fas fa-question-circle"></i>
                                            </span>
                                            <span>
                                                {{ $assignment->questions->count() }} @lang('l.questions')
                                            </span>
                                        </div>

                                        <div class="assignment-meta-item">
                                            <span class="assignment-meta-icon">
                                                <i class="fas fa-clock"></i>
                                            </span>
                                            <span>
                                                {{ $assignment->time_limit ? $assignment->time_limit . ' ' . __('l.minutes') : __('l.unlimited') }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="progress-line">
                                        <div class="progress-fill" style="width: {{ $progress }}%"></div>
                                    </div>

                                    @if ($userAssignment && $userAssignment->submitted_at)
                                        <div class="score-box">
                                            <div class="row text-center">
                                                <div class="col-4">
                                                    <strong class="text-primary">{{ $userAssignment->score ?? 0 }}</strong>
                                                    <br><small>@lang('l.score')</small>
                                                </div>

                                                <div class="col-4">
                                                    <strong class="text-info">{{ $userAssignment->total_points ?? 0 }}</strong>
                                                    <br><small>@lang('l.total_points')</small>
                                                </div>

                                                <div class="col-4">
                                                    <strong class="text-success">{{ $userAssignment->percentage ?? 0 }}%</strong>
                                                    <br><small>@lang('l.percentage')</small>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="mt-3 text-end">
                                        @if (!$userAssignment || !$userAssignment->submitted_at)
                                            <a href="{{ route('dashboard.users.assignments-start', ['id' => encrypt($assignment->id)]) }}" class="btn-custom btn-primary-custom">
                                                <i class="fas fa-play"></i>
                                                {{ $userAssignment && $userAssignment->started_at ? __('l.continue_assignment') : __('l.start_assignment') }}
                                            </a>
                                        @else
                                            <a href="{{ route('dashboard.users.assignments-results', ['id' => encrypt($userAssignment->id)]) }}" class="btn-custom btn-success-custom">
                                                <i class="fas fa-chart-bar"></i>
                                                @lang('l.view_results')
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="lesson-card">
                            <div class="empty-card">
                                <i class="fas fa-tasks fa-2x mb-3"></i>
                                <h5>@lang('l.no_assignments_for_this_lesson')</h5>
                                <p class="mb-0">@lang('l.assignments_will_appear_here_when_available')</p>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="col-lg-4">
                    <div class="sidebar-card">
                        <h5 class="sidebar-title">
                            <i class="fas fa-book"></i>
                            @lang('l.course_info')
                        </h5>

                        <div class="info-item">
                            <span class="info-icon">
                                <i class="fas fa-graduation-cap"></i>
                            </span>

                            <div>
                                <strong>@lang('l.course')</strong>
                                <br>
                                <a href="{{ $courseBackUrl }}" class="text-decoration-none">
                                    {{ $displayCourseName }}
                                </a>
                            </div>
                        </div>

                        @if ($lecture->course->level)
                            <div class="info-item">
                                <span class="info-icon">
                                    <i class="fas fa-layer-group"></i>
                                </span>

                                <div>
                                    <strong>@lang('l.Level')</strong>
                                    <br>
                                    {{ $lecture->course->level->name }}
                                </div>
                            </div>
                        @endif

                        <div class="info-item">
                            <span class="info-icon">
                                <i class="fas fa-book-open"></i>
                            </span>

                            <div>
                                <strong>@lang('l.lessons')</strong>
                                <br>
                                {{ $lecture->course->lectures->count() }}
                            </div>
                        </div>

                        <div class="mt-3">
                            <a href="{{ $courseBackUrl }}" class="btn btn-outline-primary btn-custom w-100">
                                <i class="fas fa-arrow-left"></i>
                                @lang('l.back_to_course')
                            </a>
                        </div>
                    </div>

                    <div class="sidebar-card">
                        <h5 class="sidebar-title">
                            <i class="fas fa-bolt"></i>
                            @lang('l.quick_actions')
                        </h5>

                        @if ($lecture->assignments->count() > 0)
                            <a class="btn btn-warning btn-custom quick-action" href="#Assignments">
                                <i class="fas fa-tasks"></i>
                                @lang('l.go_to_assignments')
                            </a>
                        @endif

                        @if ($lecture->files)
                            <a class="btn btn-info btn-custom quick-action" href="{{ asset($lecture->files) }}" target="_blank">
                                <i class="fas fa-download"></i>
                                @lang('l.download_material')
                            </a>
                        @endif

                        <button class="btn btn-outline-primary btn-custom quick-action" onclick="shareLesson()">
                            <i class="fas fa-share-alt"></i>
                            @lang('l.share_lesson')
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://cdn.plyr.io/3.7.8/plyr.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const videoElement = document.getElementById('plyr-video-player');

            if (videoElement) {
                new Plyr(videoElement);
            }
        });

        document.addEventListener('contextmenu', function(e) {
            if (e.target.closest('.video-container')) {
                e.preventDefault();
            }
        });

        function shareLesson() {
            const url = window.location.href;
            const title = @json($lecture->name);

            if (navigator.share) {
                navigator.share({
                    title: title,
                    url: url
                });
            } else if (navigator.clipboard) {
                navigator.clipboard.writeText(url).then(() => {
                    Swal.fire({
                        title: @json(__('l.copied')),
                        text: '@lang('l.lesson_link_copied_to_clipboard')',
                        icon: 'success',
                        timer: 1800,
                        showConfirmButton: false
                    });
                });
            }
        }
    </script>
@endsection
