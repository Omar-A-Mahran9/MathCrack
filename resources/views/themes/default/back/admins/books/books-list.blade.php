@extends('themes.default.layouts.back.master')

@section('title')
    Books
@endsection

@section('seo')
@endsection

@section('css')
    <style>
        .books-card {
            border: 1px solid rgba(0,0,0,.08);
            box-shadow: 0 4px 16px rgba(0,0,0,.04);
        }

        .books-card .card-header {
            background: #fff;
            border-bottom: 1px solid rgba(0,0,0,.08);
        }

        .form-hint {
            font-size: 12px;
            color: #6c757d;
            margin-top: 4px;
        }

        .table td,
        .table th {
            vertical-align: middle;
        }

        #books-table .book-actions {
            display: flex;
            gap: 4px;
            flex-wrap: nowrap;
            align-items: center;
            white-space: nowrap;
        }

        #books-table .book-actions .btn {
            min-width: 70px;
            padding: 4px 8px;
            font-size: 11px;
        }

        #books-table .book-actions form {
            display: inline-block;
            margin: 0;
        }
    </style>
@endsection

@section('content')
    <div class="main-content">
        <div class="row">
            <div class="col-12">

                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger">
                        <strong>There are some errors:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="card books-card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Add New Book</h5>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('dashboard.admins.books-store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Course</label>
                                <select name="course_id" class="form-control" required>
    <option value="">Select Course</option>
    @foreach($courses as $course)
        <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
            {{ $course->name }}
        </option>
    @endforeach
</select>
                                    <div class="form-hint">
                                        This book will appear only to students who belong to this course level/track.
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Book Title</label>
                                    <input
                                        type="text"
                                        name="title"
                                        class="form-control"
                                        value="{{ old('title') }}"
                                        required
                                    >
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Price</label>
                                    <input
                                        type="number"
                                        name="price"
                                        class="form-control"
                                        value="{{ old('price', 0) }}"
                                        min="0"
                                        step="0.01"
                                        required
                                    >
                                    <div class="form-hint">
                                        Example: 250.00
                                    </div>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Access Duration Days</label>
                                    <input
                                        type="number"
                                        name="access_duration_days"
                                        class="form-control"
                                        value="{{ old('access_duration_days') }}"
                                        min="1"
                                        max="3650"
                                    >
                                    <div class="form-hint">
                                        Leave empty for lifetime access.
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">PDF File</label>
                                    <input
                                        type="file"
                                        name="pdf"
                                        class="form-control"
                                        accept="application/pdf"
                                        required
                                    >
                                    <div class="form-hint">
                                        PDF will be stored privately and converted to protected images.
                                    </div>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea
                                        name="description"
                                        class="form-control"
                                        rows="3"
                                    >{{ old('description') }}</textarea>
                                </div>

                                <div class="col-md-4 mb-3 d-flex align-items-center">
                                    <div class="form-check mt-4">
                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            name="allow_print"
                                            value="1"
                                            id="allow_print"
                                            {{ old('allow_print') ? 'checked' : '' }}
                                        >
                                        <label class="form-check-label" for="allow_print">
                                            Allow printing
                                        </label>
                                        <div class="form-hint">
                                            Recommended: keep disabled.
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">
                                        Upload Book
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card books-card">
                    <div class="card-header">
                        <h5 class="mb-0">Books List</h5>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="books-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Title</th>
                                        <th>Course</th>
                                        <th>Price</th>
                                        <th>Access</th>
                                        <th>Print</th>
                                        <th>Pages</th>
                                        <th>Status</th>
                                        <th>Created At</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>

                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function () {
            $('#books-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('dashboard.admins.books') }}",
                order: [[8, 'desc']],
                autoWidth: false,
                columns: [
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'title',
                        name: 'title'
                    },
                    {
                        data: 'course',
                        name: 'course',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'price',
                        name: 'price'
                    },
                    {
                        data: 'access_duration',
                        name: 'access_duration_days',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'allow_print',
                        name: 'allow_print',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'total_pages',
                        name: 'total_pages'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'text-nowrap'
                    }
                ]
            });
        });
    </script>
@endsection
