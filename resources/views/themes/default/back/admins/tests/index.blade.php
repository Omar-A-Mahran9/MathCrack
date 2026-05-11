@extends('themes.default.layouts.back.master')

@section('title')
    @lang('l.tests_management')
@endsection

@section('css')
    <style>
        .modal label.form-label,
        .modal label {
            font-size: 18px !important;
            font-weight: 600;
        }

        .modal h6.text-primary.border-bottom {
            font-size: 22px;
            font-weight: 700;
        }

        .apexcharts-canvas,
        #salesOverviewChart,
        #revenueChart,
        #growthChart {
            display: none !important;
        }

        .module-score-card {
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 16px;
            background: #f9fafb;
            margin-bottom: 16px;
        }

        .module-score-card h6 {
            margin-bottom: 14px;
            font-size: 18px;
            font-weight: 700;
            color: #2563eb;
        }

        /* ===== Admin Tests Management Polish ===== */
        .admin-page-header {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            color: #fff;
            border-radius: 18px;
            padding: 26px 28px;
            margin-bottom: 22px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 14px 34px rgba(30, 64, 175, 0.18);
        }

        .admin-page-header::before {
            content: '';
            position: absolute;
            top: 0;
            right: -90px;
            width: 180px;
            height: 100%;
            background: rgba(255,255,255,0.12);
            transform: skewX(-15deg);
        }

        .admin-page-header h4,
        .admin-page-header p {
            color: #fff !important;
            position: relative;
            z-index: 2;
        }

        .admin-page-header h4 {
            font-size: 1.65rem;
            font-weight: 900;
        }

        .admin-page-header p {
            opacity: 0.92;
            font-weight: 600;
        }

        .admin-header-actions {
            position: relative;
            z-index: 2;
        }

        .admin-filter-card,
        .admin-table-card {
            border: 1px solid #e5e7eb;
            border-radius: 18px;
            box-shadow: 0 10px 28px rgba(15, 23, 42, 0.06);
            overflow: hidden;
        }

        .admin-filter-card .card-body,
        .admin-table-card .card-body {
            padding: 22px;
        }

        .admin-filter-card label {
            font-weight: 800;
            color: #1f2937;
        }

        .admin-filter-card .form-select,
        .admin-filter-card .form-control {
            border-radius: 12px;
            min-height: 44px;
            border-color: #dbe3ef;
            font-weight: 600;
        }

        .tests-table-wrapper {
            border-radius: 14px;
            overflow: hidden;
            border: 1px solid #e5e7eb;
        }

        #testsTable {
            margin-bottom: 0 !important;
        }

        #testsTable thead th {
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            color: #1e3a8a;
            font-weight: 900;
            text-align: center;
            vertical-align: middle;
            border-bottom: 1px solid #bfdbfe;
            white-space: nowrap;
        }

        #testsTable tbody td {
            vertical-align: middle;
            text-align: center;
            color: #334155;
            font-weight: 600;
        }

        #testsTable tbody td:nth-child(2),
        #testsTable tbody td:nth-child(3) {
            text-align: left;
        }

        [dir="rtl"] #testsTable tbody td:nth-child(2),
        [dir="rtl"] #testsTable tbody td:nth-child(3) {
            text-align: right;
        }

        #testsTable tbody tr:hover {
            background: #f8fbff;
        }

        .modal-content {
            border-radius: 20px;
            overflow: hidden;
            border: none;
            box-shadow: 0 24px 70px rgba(15, 23, 42, 0.22);
        }

        .modal-header {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            color: #fff;
            border-bottom: none;
            padding: 20px 24px;
        }

        .modal-header .modal-title {
            color: #fff;
            font-weight: 900;
        }

        .modal-header .btn-close {
            filter: invert(1);
            opacity: .9;
        }

        .modal-body {
            background: #f8fafc;
        }

        .modal-footer {
            background: #fff;
            border-top: 1px solid #e5e7eb;
        }

        .modal .form-control,
        .modal .form-select {
            border-radius: 12px;
            min-height: 44px;
            border-color: #dbe3ef;
            font-weight: 600;
        }

        .modal textarea.form-control {
            min-height: 90px;
        }

        .section-divider-title {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            padding: 12px 14px;
            color: #1e40af !important;
            font-size: 1.05rem !important;
            font-weight: 900 !important;
        }

        .module-score-card {
            background: #ffffff;
            border: 1px solid #dbeafe;
            box-shadow: 0 6px 18px rgba(15, 23, 42, 0.04);
        }

        .module-score-card h6 {
            color: #1e40af;
            font-weight: 900;
        }

        .module-block .mb-3 {
            background: #fff;
            border: 1px solid #e5e7eb;
            padding: 14px;
            border-radius: 14px;
        }

        .admin-help-note {
            background: #eff6ff;
            border: 1px solid #93c5fd;
            color: #1e3a8a;
            border-radius: 14px;
            padding: 14px 16px;
            font-weight: 700;
        }

        .btn {
            border-radius: 10px;
            font-weight: 800;
        }

        @media (max-width: 768px) {
            .admin-page-header {
                padding: 22px;
            }

            .admin-page-header h4 {
                font-size: 1.35rem;
            }

            .admin-header-actions {
                width: 100%;
                margin-top: 14px;
            }

            .admin-header-actions .btn {
                width: 100%;
            }
        }

    </style>
@endsection

@section('content')
    <div class="main-content">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    {{ $error }}
                @endforeach
            </div>
        @endif

        @can('show lectures')
            <div class="admin-page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h4 class="mb-1">
                        <i class="fa fa-clipboard-list me-2"></i>
                        @lang('l.tests_management')
                    </h4>
                    <p class="mb-0">@lang('l.manage_all_tests')</p>
                </div>

                <div class="admin-header-actions">
                    @can('add tests')
                        <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#addTestModal">
                            <i class="fa fa-plus ti-xs me-1"></i>
                            @lang('l.add_new_test')
                        </button>
                    @endcan
                </div>
            </div>

            <div class="card admin-filter-card mb-3">
                <div class="card-body">
                    <form id="filterForm">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="course_filter" class="form-label">@lang('l.Course')</label>
                                <select class="form-select" id="course_filter" name="course_id">
                                    <option value="">@lang('l.all_courses')</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}">{{ $course->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label for="status_filter" class="form-label">@lang('l.status')</label>
                                <select class="form-select" id="status_filter" name="status">
                                    <option value="">@lang('l.all_statuses')</option>
                                    <option value="1">@lang('l.active')</option>
                                    <option value="0">@lang('l.inactive')</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label for="price_filter" class="form-label">@lang('l.price_type')</label>
                                <select class="form-select" id="price_filter" name="price_type">
                                    <option value="">@lang('l.all_prices')</option>
                                    <option value="free">@lang('l.free')</option>
                                    <option value="paid">@lang('l.paid')</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12 d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-secondary btn-sm" id="clearFilters">
                                    <i class="fa fa-undo me-1"></i>@lang('l.clear_filters')
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card admin-table-card">
                <div class="card-body">
                    <div class="table-responsive tests-table-wrapper">
                        <table id="testsTable" class="table table-striped table-bordered nowrap w-100">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>@lang('l.test_name')</th>
                                <th>@lang('l.Course')</th>
                                <th>@lang('l.price')</th>
                                <th>@lang('l.questions_status')</th>
                                <th>@lang('l.students_count')</th>
                                <th>@lang('l.status')</th>
                                <th>@lang('l.actions')</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>

            @can('add tests')
                <div class="modal fade" id="addTestModal" tabindex="-1" aria-labelledby="addTestModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xl modal-dialog-scrollable">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addTestModalLabel">@lang('l.add_new_test')</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>

                            <form action="{{ route('dashboard.admins.tests-store') }}" method="POST" id="addTestForm">
                                @csrf

                                <div class="modal-body" style="max-height: calc(100vh - 180px); overflow-y: auto;">
                                    <div class="row">

                                        <div class="col-12">
                                            <h6 class="section-divider-title mb-3">@lang('l.basic_information')</h6>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="name" class="form-label">@lang('l.test_name') <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="name" name="name" required>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="course_id" class="form-label">@lang('l.Course') <span class="text-danger">*</span></label>
                                                <select class="form-select" id="course_id" name="course_id" required>
                                                    <option value="">@lang('l.select_course')</option>
                                                    @foreach($courses as $course)
                                                        <option value="{{ $course->id }}">{{ $course->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="mb-3">
                                                <label for="description" class="form-label">@lang('l.test_description')</label>
                                                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="price" class="form-label">@lang('l.test_price') (EGP) <span class="text-danger">*</span></label>
                                                <input type="number" class="form-control" id="price" name="price" min="0" step="0.01" value="0" required>
                                                <small class="form-text text-muted">@lang('l.put_zero_if_free')</small>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="mb-3 mt-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                                                    <label class="form-check-label" for="is_active">
                                                        @lang('l.Active')
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <h6 class="section-divider-title mb-3">@lang('l.scoring_system')</h6>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="initial_score" class="form-label">Initial Score <span class="text-danger">*</span></label>
                                                <input type="number" class="form-control" id="initial_score" name="initial_score" min="0" max="100000" value="200" required>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="admin-help-note">
                                                <strong>@lang('l.Note'):</strong>
                                                Final score will be calculated automatically after adding the test questions based on each module scoring settings.
                                                <br>
                                                <span id="score-calculation-preview" class="fw-bold text-success"></span>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <h6 class="section-divider-title mb-2">@lang('l.test_structure')</h6>
                                        </div>

                                        <div class="col-md-4">
                                            <label for="modules_count" class="form-label">@lang('l.modules_count')</label>
                                            <select class="form-select" id="modules_count" name="modules_count">
                                                <option value="1">1</option>
                                                <option value="2" selected>2</option>
                                                <option value="3">3</option>
                                                <option value="4">4</option>
                                                <option value="5">5</option>
                                            </select>
                                            <small class="form-text text-muted">
                                                Select number of modules (1–5). Hidden modules will be treated as 0 questions.
                                            </small>
                                        </div>

                                        <div class="col-12 mt-3">
                                            <h6 class="section-divider-title mb-3">Module Scoring Settings</h6>
                                        </div>

                                        @for($i = 1; $i <= 5; $i++)
                                            <div class="col-12 module-score-block" data-module="{{ $i }}">
                                                <div class="module-score-card">
                                                    <h6>Module {{ $i }} Scoring</h6>
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="mb-3">
                                                                <label for="module{{ $i }}_easy_score" class="form-label">Module {{ $i }} Easy Score <span class="text-danger">*</span></label>
                                                                <input type="number"
                                                                       class="form-control module-score-input"
                                                                       id="module{{ $i }}_easy_score"
                                                                       name="module{{ $i }}_easy_score"
                                                                       min="0"
                                                                       max="100000"
                                                                       value="10">
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4">
                                                            <div class="mb-3">
                                                                <label for="module{{ $i }}_medium_score" class="form-label">Module {{ $i }} Medium Score <span class="text-danger">*</span></label>
                                                                <input type="number"
                                                                       class="form-control module-score-input"
                                                                       id="module{{ $i }}_medium_score"
                                                                       name="module{{ $i }}_medium_score"
                                                                       min="0"
                                                                       max="100000"
                                                                       value="13">
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4">
                                                            <div class="mb-3">
                                                                <label for="module{{ $i }}_hard_score" class="form-label">Module {{ $i }} Hard Score <span class="text-danger">*</span></label>
                                                                <input type="number"
                                                                       class="form-control module-score-input"
                                                                       id="module{{ $i }}_hard_score"
                                                                       name="module{{ $i }}_hard_score"
                                                                       min="0"
                                                                       max="100000"
                                                                       value="17">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endfor

                                        <div class="col-12"></div>

                                        @for($i = 1; $i <= 5; $i++)
                                            <div class="col-md-6 module-block" data-module="{{ $i }}">
                                                <div class="mb-3">
                                                    <label for="part{{ $i }}_questions_count" class="form-label">
                                                        Module {{ $i }} Questions Count
                                                        @if($i === 1) <span class="text-danger">*</span> @endif
                                                    </label>
                                                    <input type="number"
                                                           class="form-control"
                                                           id="part{{ $i }}_questions_count"
                                                           name="part{{ $i }}_questions_count"
                                                           min="{{ $i === 1 ? 1 : 0 }}"
                                                           max="100"
                                                           value="{{ $i <= 2 ? 22 : 0 }}"
                                                           @if($i === 1) required @endif>
                                                </div>
                                            </div>

                                            <div class="col-md-6 module-block" data-module="{{ $i }}">
                                                <div class="mb-3">
                                                    <label for="part{{ $i }}_time_minutes" class="form-label">
                                                        Module {{ $i }} Time (Minutes)
                                                        @if($i === 1) <span class="text-danger">*</span> @endif
                                                    </label>
                                                    <input type="number"
                                                           class="form-control"
                                                           id="part{{ $i }}_time_minutes"
                                                           name="part{{ $i }}_time_minutes"
                                                           min="{{ $i === 1 ? 1 : 0 }}"
                                                           max="300"
                                                           value="{{ $i <= 2 ? 35 : 0 }}"
                                                           @if($i === 1) required @endif>
                                                </div>
                                            </div>
                                        @endfor

                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="break_time_minutes" class="form-label">@lang('l.break_time_minutes')</label>
                                                <input type="number" class="form-control" id="break_time_minutes" name="break_time_minutes" min="0" max="60" value="15">
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="max_attempts" class="form-label">@lang('l.max_attempts') <span class="text-danger">*</span></label>
                                                <input type="number" class="form-control" id="max_attempts" name="max_attempts" min="1" max="10" value="1" required>
                                                <small class="form-text text-muted">@lang('l.max_attempts_help')</small>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="admin-help-note">
                                                <i class="fas fa-info-circle"></i>
                                                <strong>@lang('l.Note'):</strong> @lang('l.test_timing_info')
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('l.Cancel')</button>
                                    <button type="submit" class="btn btn-primary">@lang('l.save')</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endcan
        @endcan
    </div>
@endsection

@section('js')
<script>
$(document).ready(function() {
    var testsTable = $('#testsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('dashboard.admins.tests') }}",
            data: function (d) {
                d.course_id  = $('#course_filter').val();
                d.status     = $('#status_filter').val();
                d.price_type = $('#price_filter').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'name', name: 'name' },
            { data: 'course_name', name: 'course.name', defaultContent: '' },
            { data: 'price_formatted', name: 'price', orderable: false, searchable: false },
            { data: 'questions_status', name: 'questions_status', orderable: false, searchable: false },
            { data: 'students_count', name: 'students_count', searchable: false },
            { data: 'status', name: 'status', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false },
        ],
        order: [[1, 'asc']],
        pageLength: 25,
        responsive: true,
        autoWidth: false,
        language: {
            url: "{{ app()->getLocale() == 'ar' ? asset('back-assets/js/datatable-ar.json') : '' }}"
        }
    });

    $('#course_filter, #status_filter, #price_filter').on('change', function() {
        testsTable.draw();
    });

    $('#clearFilters').click(function() {
        $('#filterForm')[0].reset();
        testsTable.draw();
    });

    $(document).on('change', '.status-toggle', function() {
        var checkbox = $(this);
        var testId   = checkbox.data('id');
        var isActive = checkbox.is(':checked');

        $.ajax({
            url: "{{ route('dashboard.admins.tests-toggle-status') }}",
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                id: testId
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                } else {
                    toastr.error(response.message);
                    checkbox.prop('checked', !isActive);
                }
            },
            error: function() {
                toastr.error('@lang("l.error_occurred")');
                checkbox.prop('checked', !isActive);
            }
        });
    });

    function getInt(selector) {
        return parseInt($(selector).val()) || 0;
    }

    function updateScorePreview() {
        const modulesCount = getInt('#modules_count');
        const initialScore = getInt('#initial_score');

        let message = `Initial Score: ${initialScore}`;

        for (let i = 1; i <= modulesCount; i++) {
            const easy   = getInt(`#module${i}_easy_score`);
            const medium = getInt(`#module${i}_medium_score`);
            const hard   = getInt(`#module${i}_hard_score`);

            message += ` | Module ${i}: Easy ${easy}, Medium ${medium}, Hard ${hard}`;
        }

        message += ` | Final score will be calculated automatically after adding questions.`;

        $('#score-calculation-preview')
            .removeClass('text-danger')
            .addClass('text-success')
            .html(message);
    }

    function toggleModules() {
        var count = parseInt($('#modules_count').val()) || 1;

        $('.module-block').each(function () {
            var mod = parseInt($(this).data('module'));
            if (mod <= count) {
                $(this).show();
            } else {
                $(this).hide();
                $(this).find('input[type="number"]').val(0);
            }
        });

        $('.module-score-block').each(function () {
            var mod = parseInt($(this).data('module'));
            if (mod <= count) {
                $(this).show();
                $(this).find('input').prop('required', true);
            } else {
                $(this).hide();
                $(this).find('input').prop('required', false);
            }
        });

        if (count <= 1) {
            $('#break_time_minutes').val(0);
            $('#break_time_minutes').closest('.col-md-6').hide();
        } else {
            $('#break_time_minutes').closest('.col-md-6').show();
            if (!$('#break_time_minutes').val()) {
                $('#break_time_minutes').val(15);
            }
        }

        updateScorePreview();
    }

    $('#modules_count').on('change', toggleModules);
    $('#initial_score').on('input', updateScorePreview);
    $(document).on('input', '.module-score-input', updateScorePreview);

    toggleModules();
    updateScorePreview();

    $('#addTestForm').on('submit', function () {
        var count = parseInt($('#modules_count').val()) || 1;

        for (var i = count + 1; i <= 5; i++) {
            $('#part' + i + '_questions_count').val(0);
            $('#part' + i + '_time_minutes').val(0);

            $('#module' + i + '_easy_score').val(0);
            $('#module' + i + '_medium_score').val(0);
            $('#module' + i + '_hard_score').val(0);
        }
    });

    $(document).on('click', '.delete-record', function() {
        var href     = $(this).attr('href');
        var testName = $(this).closest('tr').find('td:eq(1)').text().trim() || 'this test';
        if (confirm('@lang("l.are_you_sure_delete_test") "' + testName + '"?')) {
            window.location.href = href;
        }
        return false;
    });
});
</script>
@endsection