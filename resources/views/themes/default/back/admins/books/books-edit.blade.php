@extends('themes.default.layouts.back.master')

@section('title')
    Edit Book
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

                <div class="card books-card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="mb-0">Edit Book</h5>

                        <a href="{{ route('dashboard.admins.books') }}" class="btn btn-secondary btn-sm">
                            Back to Books
                        </a>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('dashboard.admins.books-update') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <input type="hidden" name="id" value="{{ encrypt($book->id) }}">

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Course</label>
                                    <select name="course_id" class="form-control" required>
                                        <option value="">Select Course</option>
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
                                        value="{{ old('title', $book->title) }}"
                                        required
                                    >
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Price</label>
                                    <input
                                        type="number"
                                        name="price"
                                        class="form-control"
                                        value="{{ old('price', $book->price) }}"
                                        min="0"
                                        step="0.01"
                                        required
                                    >
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Access Duration Days</label>
                                    <input
                                        type="number"
                                        name="access_duration_days"
                                        class="form-control"
                                        value="{{ old('access_duration_days', $book->access_duration_days) }}"
                                        min="1"
                                        max="3650"
                                    >
                                    <div class="form-hint">
                                        Leave empty for lifetime access.
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-control" required>
                                        <option value="draft" {{ old('status', $book->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                                        <option value="ready" {{ old('status', $book->status) === 'ready' ? 'selected' : '' }}>Ready</option>
                                        <option value="processing" {{ old('status', $book->status) === 'processing' ? 'selected' : '' }}>Processing</option>
                                        <option value="failed" {{ old('status', $book->status) === 'failed' ? 'selected' : '' }}>Failed</option>
                                        <option value="disabled" {{ old('status', $book->status) === 'disabled' ? 'selected' : '' }}>Disabled</option>
                                    </select>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea
                                        name="description"
                                        class="form-control"
                                        rows="3"
                                    >{{ old('description', $book->description) }}</textarea>
                                </div>

                                <div class="col-md-8 mb-3">
                                    <label class="form-label">Replace PDF File</label>
                                    <input
                                        type="file"
                                        name="pdf"
                                        class="form-control"
                                        accept="application/pdf"
                                    >
                                    <div class="form-hint">
                                        Leave empty to keep current PDF. If you upload a new PDF, pages will reset and the book will return to Draft.
                                    </div>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <div class="form-check">
                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            name="allow_print"
                                            value="1"
                                            id="allow_print"
                                            {{ old('allow_print', $book->allow_print) ? 'checked' : '' }}
                                        >
                                        <label class="form-check-label" for="allow_print">
                                            Allow printing
                                        </label>
                                        <div class="form-hint">
                                            Recommended: keep disabled.
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        Save Changes
                                    </button>

                                    <a href="{{ route('dashboard.admins.books') }}" class="btn btn-secondary">
                                        Cancel
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('js')
@endsection
