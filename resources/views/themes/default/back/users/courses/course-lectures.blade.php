@extends('themes.default.layouts.back.student-master')

@section('title')
    {{ (($course->track_slug ?? null) === 'digital-sat') ? __('l.digital_sat_course') : $course->name }} - @lang('l.lessons')
@endsection

@section('css')
    <style>
        .course-header {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            color: white;
            padding: 30px 0;
            margin-bottom: 30px;
            border-radius: 15px;
        }

        .course-header h1 {
            color: white !important;
            font-weight: 800;
        }

        .course-header p {
            color: white !important;
        }

        .back-btn {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .back-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            color: white;
            transform: translateY(-2px);
        }

        .stats-card {
            background: rgba(255, 255, 255, 0.12);
            border-radius: 12px;
            padding: 16px;
            text-align: center;
            backdrop-filter: blur(10px);
            min-height: 88px;
        }

        .stats-number {
            font-size: 1.6rem;
            font-weight: 800;
            display: block;
            color: white;
        }

        .stats-label {
            font-size: 0.9rem;
            opacity: 0.95;
            margin-top: 5px;
            color: white;
        }

        .purchase-btn {
            border-radius: 12px;
            font-weight: 800;
            padding: 12px 25px;
            border: none;
            background: linear-gradient(45deg, #f59e0b, #fbbf24);
            color: #111827;
            box-shadow: 0 4px 15px rgba(245, 158, 11, 0.35);
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .purchase-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(245, 158, 11, 0.45);
            color: #111827;
        }

        .free-course-alert,
        .purchased-course-alert {
            color: white !important;
            border-radius: 12px;
            font-weight: 700;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.25);
        }

        .free-course-alert {
            background: rgba(16, 185, 129, 0.22) !important;
        }

        .purchased-course-alert {
            background: rgba(34, 197, 94, 0.22) !important;
        }

        .filters-section {
            background-color: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            padding: 18px 20px;
            margin-bottom: 25px;
            box-shadow: 0 4px 18px rgba(15, 23, 42, 0.06);
        }

        .filters-section h6 {
            color: #111827;
            font-weight: 800;
            margin-bottom: 16px;
        }

        .filters-section .form-select {
            border-radius: 10px;
            border: 1px solid #cbd5e1;
            min-height: 44px;
            transition: all 0.3s ease;
        }

        .filters-section .form-select:focus {
            border-color: #1e40af;
            box-shadow: 0 0 0 0.25rem rgba(30, 64, 175, 0.18);
        }

        .btn-filter {
            border-radius: 10px;
            font-weight: 700;
            padding: 10px 18px;
            transition: all 0.3s ease;
        }

        .btn-filter:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.12);
        }

        .table-container {
            background: white;
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.08);
        }

        #lecturesTable {
            width: 100% !important;
        }

        #lecturesTable th {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%) !important;
            border: none;
            font-weight: 800;
            color: white !important;
            padding: 14px 12px;
            font-size: 0.82rem;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            vertical-align: middle;
        }

        #lecturesTable td {
            padding: 15px 12px;
            vertical-align: middle;
            border-color: #eef2f7;
            color: #1f2937;
        }

        #lecturesTable tbody tr:hover {
            background: #f8fbff;
        }

        #lecturesTable th:nth-child(1),
        #lecturesTable td:nth-child(1) {
            width: 55px;
            text-align: center;
        }

        #lecturesTable th:nth-child(2),
        #lecturesTable td:nth-child(2) {
            width: 80px;
            text-align: center;
        }

        #lecturesTable th:nth-child(3),
        #lecturesTable td:nth-child(3) {
            width: 34%;
            min-width: 320px;
        }

        #lecturesTable td:nth-child(3) {
            white-space: normal;
            line-height: 1.45;
            font-weight: 700;
            max-width: 380px;
        }

        #lecturesTable th:nth-child(4),
        #lecturesTable td:nth-child(4) {
            width: 12%;
            text-align: center;
            white-space: nowrap;
        }

        #lecturesTable th:nth-child(5),
        #lecturesTable td:nth-child(5) {
            width: 10%;
            text-align: center;
            white-space: nowrap;
        }

        #lecturesTable th:nth-child(6),
        #lecturesTable td:nth-child(6) {
            width: 13%;
            text-align: center;
            white-space: nowrap;
        }

        #lecturesTable th:nth-child(7),
        #lecturesTable td:nth-child(7) {
            width: 16%;
            text-align: center;
            white-space: nowrap;
        }

        .lecture-image {
            border-radius: 8px;
            object-fit: cover;
        }

        .dataTables_wrapper .dataTables_length select,
        .dataTables_wrapper .dataTables_filter input {
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            padding: 6px 10px;
        }

        .dataTables_wrapper .dataTables_filter input {
            min-width: 220px;
        }

        @media (max-width: 992px) {
            .table-container {
                padding: 18px;
                overflow-x: auto;
            }

            #lecturesTable {
                min-width: 980px;
            }
        }

        @media (max-width: 768px) {
            .course-header {
                padding: 24px 0;
            }

            .course-header h1 {
                font-size: 1.6rem;
            }

            .stats-card {
                margin-top: 12px;
            }
        }
    </style>
@endsection

@section('content')
    @php
        $backToCoursesUrl = !empty($course->track_slug)
            ? route('dashboard.users.courses', ['track' => $course->track_slug])
            : route('dashboard.users.courses');

        $displayCourseName = $course->name;

        if (($course->track_slug ?? null) === 'digital-sat') {
            $displayCourseName = __('l.digital_sat_course');
        }
    @endphp

    <div class="main-content">
        <div class="course-header">
            <div class="container-fluid">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="d-flex align-items-center mb-3">
                            <a href="{{ $backToCoursesUrl }}" class="back-btn me-3">
                                <i class="fas fa-arrow-right"></i>
                                @lang('l.back_to_courses')
                            </a>
                        </div>

                        <h1 class="mb-2">{{ $displayCourseName }}</h1>
                        <p class="mb-2" style="opacity:0.95;">
                            @lang('l.lessons_materials_assignments_for_course')
                        </p>

                        <p class="mb-0">
                            <i class="fas fa-layer-group me-2"></i>{{ $course->level->name ?? '-' }}

                            @if($course->price && $course->price > 0)
                                <span class="ms-3">
                                    <i class="fas fa-tag me-2"></i>{{ $course->price }} @lang('l.currency')
                                </span>
                            @else
                                <span class="ms-3 text-success">
                                    <i class="fas fa-gift me-2"></i>@lang('l.Free')
                                </span>
                            @endif
                        </p>
                    </div>

                    <div class="col-md-4">
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="stats-card">
                                    <span class="stats-number">{{ $course->lectures->count() }}</span>
                                    <div class="stats-label">@lang('l.lessons')</div>
                                </div>
                            </div>

                            <div class="col-6">
                                <div class="stats-card">
                                    <span class="stats-number">
                                        {{ $course->lectures->sum(function($lecture) { return $lecture->assignments->count(); }) }}
                                    </span>
                                    <div class="stats-label">@lang('l.assignments')</div>
                                </div>
                            </div>
                        </div>

                        @if($course->price && $course->price > 0 && !auth()->user()->hasPurchasedCourseLectures($course->id))
                            <div class="mt-3 text-center">
                                <button type="button" class="btn btn-warning btn-lg purchase-btn w-100" onclick="purchaseCourse('{{ encrypt($course->id) }}')">
                                    <i class="fas fa-shopping-cart me-2"></i>
                                    @lang('l.purchase_course')
                                </button>
                            </div>
                        @elseif($course->price && $course->price > 0 && auth()->user()->hasPurchasedCourseLectures($course->id))
                            <div class="mt-3 text-center">
                                <div class="alert alert-success mb-0 purchased-course-alert">
                                    <i class="fas fa-check-circle me-2"></i>
                                    @lang('l.already_purchased')
                                </div>
                            </div>
                        @else
                            <div class="mt-3 text-center">
                                <div class="alert alert-info mb-0 free-course-alert">
                                    <i class="fas fa-gift me-2"></i>
                                    @lang('l.free_course_enjoy')
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="filters-section">
            <h6>
                <i class="fas fa-filter me-2"></i>@lang('l.filter_lessons')
            </h6>

            <div class="row align-items-end">
                <div class="col-md-4 mb-3 mb-md-0">
                    <label class="form-label small text-muted">@lang('l.lesson_type')</label>
                    <select class="form-select" id="filter_type">
                        <option value="">@lang('l.all_types')</option>
                        <option value="free">@lang('l.Free')</option>
                        <option value="price">@lang('l.Paid')</option>
                        <option value="month">@lang('l.Monthly')</option>
                        <option value="course">@lang('l.Course')</option>
                    </select>
                </div>

                <div class="col-md-8">
                    <button type="button" class="btn btn-outline-secondary btn-filter" id="clear_filters">
                        <i class="fas fa-times me-1"></i>
                        @lang('l.clear_filters')
                    </button>
                </div>
            </div>
        </div>

        <div class="table-container">
            <div class="table-responsive">
                <table class="table table-hover" id="lecturesTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>@lang('l.image')</th>
                            <th>@lang('l.lesson_name')</th>
                            <th>@lang('l.lesson_type')</th>
                            <th>@lang('l.price')</th>
                            <th>@lang('l.assignments')</th>
                            <th>@lang('l.Action')</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            var table = $('#lecturesTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('dashboard.users.courses-lectures') }}?id={{ encrypt($course->id) }}",
                    data: function (d) {
                        d.type = $('#filter_type').val();
                    }
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'image', name: 'image', orderable: false, searchable: false},
                    {data: 'name', name: 'name'},
                    {data: 'type', name: 'type'},
                    {data: 'price', name: 'price'},
                    {data: 'assignments_count', name: 'assignments_count'},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ],
                order: [[0, 'asc']],
                language: {
                    url: "{{ asset('assets/back/js/datatables-ar.json') }}"
                },
                pageLength: 10,
                responsive: false
            });

            $('#clear_filters').click(function() {
                $('#filter_type').val('');
                table.draw();
            });

            $('#filter_type').change(function() {
                table.draw();
            });
        });

        function purchaseCourse(courseId) {
            window.location.href = "{{ route('dashboard.users.courses-purchase') }}?course_id=" + courseId;
        }
    </script>
@endsection
