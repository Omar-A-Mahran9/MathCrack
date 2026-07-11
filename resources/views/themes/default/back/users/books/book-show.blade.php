@extends('themes.default.layouts.back.student-master')

@section('title')
    {{ $book->title }}
@endsection

@section('css')
    <style>
        .book-purchase-hero {
            background: linear-gradient(135deg, #1e40af 0%, #2563eb 100%);
            border-radius: 16px;
            padding: 30px;
            color: #fff;
            margin-bottom: 22px;
            box-shadow: 0 12px 30px rgba(30, 64, 175, 0.16);
        }

        .book-purchase-hero h1 {
            color: #fff;
            font-size: 30px;
            font-weight: 900;
            margin-bottom: 8px;
        }

        .book-purchase-hero p {
            color: rgba(255,255,255,.90);
            margin-bottom: 0;
        }

        .purchase-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, .10);
        }

        .book-cover-large {
            height: 260px;
            border-radius: 16px;
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .book-cover-large i {
            font-size: 76px;
            color: rgba(255,255,255,.78);
        }

        .book-price-box-large {
            border-radius: 10px;
            padding: 12px 14px;
            text-align: center;
            font-size: 22px;
            font-weight: 900;
            background: #ecfdf5;
            border: 1px solid #bbf7d0;
            color: #047857;
            margin-bottom: 16px;
        }

        .book-price-box-large small {
            display: block;
            font-size: 12px;
            font-weight: 700;
            line-height: 1.1;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            padding: 13px 0;
            border-bottom: 1px solid rgba(0,0,0,.07);
        }

        .summary-row span:first-child {
            color: #64748b;
        }

        .summary-row span:last-child {
            color: #111827;
            font-weight: 800;
        }

        .policy-list li {
            margin-bottom: 8px;
            color: #64748b;
        }

        .track-tabs-wrap {
            background: #fff;
            border-radius: 16px;
            padding: 12px;
            margin-bottom: 24px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, .06);
        }

        .track-tabs {
            gap: 10px;
        }

        .track-tabs .nav-link {
            border: none;
            border-radius: 12px;
            background: #f1f5f9;
            color: #334155;
            font-weight: 800;
            padding: 12px 18px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .track-tabs .nav-link.active {
            background: #2563eb;
            color: #fff;
        }

        .book-lock-box {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            border-radius: 9px;
            padding: 10px;
            font-size: 13px;
            font-weight: 900;
            margin-bottom: 12px;
        }

        .book-lock-box.locked {
            background: #fff7ed;
            border: 1px solid #fed7aa;
            color: #c2410c;
        }

        .book-lock-box.unlocked {
            background: #ecfdf5;
            border: 1px solid #bbf7d0;
            color: #047857;
        }

        .book-action-btn {
            width: 100%;
            min-height: 42px;
            border-radius: 9px !important;
            font-weight: 900 !important;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
    </style>
@endsection

@section('content')
    @php
        $track = $track ?? request('track', 'digital-sat');

        $trackTitle = match ($track) {
            'digital-sat' => 'Digital SAT',
            'act' => 'ACT',
            default => $track ? \Illuminate\Support\Str::title(str_replace('-', ' ', $track)) : 'Books',
        };

        $isFree = (float) $book->price <= 0;
    @endphp

    <div class="container py-4">

        <div class="book-purchase-hero">
            <h1>{{ $book->title }}</h1>
            <p>Review book details and complete your purchase to start reading online.</p>
        </div>

        <div class="track-tabs-wrap">
            <ul class="nav track-tabs">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard.users.courses', ['track' => $track]) }}">
                        <i class="fas fa-book-open"></i>
                        Course
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard.users.tests.index', ['track' => $track]) }}">
                        <i class="fas fa-pen"></i>
                        Practice Tests
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link active" href="{{ route('dashboard.users.books', ['track' => $track]) }}">
                        <i class="fas fa-book"></i>
                        Books
                    </a>
                </li>
            </ul>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="row">
            <div class="col-lg-8 mb-4">
                <div class="card purchase-card">
                    <div class="card-body">
                        <div class="book-cover-large mb-4">
                            <i class="fas fa-book-open"></i>
                        </div>

                        <h3 class="mb-3">{{ $book->title }}</h3>

                        <p class="text-muted">
                            {{ $book->description ?? 'No description available.' }}
                        </p>

                        <hr>

                        <h5 class="mb-3">Reading Policy</h5>

                        <ul class="policy-list mb-0">
                            <li>This book is available for online reading only.</li>
                            <li>Downloading the original PDF is not allowed.</li>
                            <li>Pages are protected with a personal watermark.</li>
                            <li>Printing is {{ $book->allow_print ? 'allowed for this book.' : 'not allowed for this book.' }}</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 mb-4">
                <div class="card purchase-card">
                    <div class="card-body">
                        <h5 class="mb-3">Purchase Summary</h5>

                        <div class="book-price-box-large">
                            @if($isFree)
                                Free
                            @else
                                {{ number_format((float) $book->price, 2) }} EGP
                            @endif
                            <small>Book Price</small>
                        </div>

                        <div class="summary-row">
                            <span>Track</span>
                            <span>{{ $trackTitle }}</span>
                        </div>

                        <div class="summary-row">
                            <span>Access</span>
                            <span>
                                @if(empty($book->access_duration_days))
                                    Lifetime
                                @else
                                    {{ $book->access_duration_days }} days
                                @endif
                            </span>
                        </div>

                        <div class="summary-row">
                            <span>Pages</span>
                            <span>{{ $book->total_pages }}</span>
                        </div>

                        <div class="summary-row">
                            <span>Printing</span>
                            <span>{{ $book->allow_print ? 'Allowed' : 'Not allowed' }}</span>
                        </div>

                        <div class="mt-4">
                            @if($userBook)
                                <div class="book-lock-box unlocked">
                                    <i class="fas fa-unlock"></i>
                                    Access available
                                </div>

                                <a href="{{ route('books.reader.read', ['book' => $book->slug]) }}" class="btn btn-success book-action-btn">
                                    <i class="fas fa-book-open"></i>
                                    Read Book
                                </a>
                            @else
                                <div class="book-lock-box locked">
                                    <i class="fas fa-lock"></i>
                                    Locked - Purchase required
                                </div>

                                <form action="{{ route('dashboard.users.books-purchase', ['book' => $book->slug, 'track' => $track]) }}" method="POST">
                                    @csrf

                                    <button type="submit" class="btn btn-primary book-action-btn" onclick="return confirm('Confirm book purchase?')">
                                        <i class="fas fa-shopping-cart"></i>
                                        {{ $isFree ? 'Get Free Book' : 'Purchase Book' }}
                                    </button>
                                </form>

                                <div class="text-muted mt-3" style="font-size: 12px;">
                                    Temporary internal purchase button. Payment gateway will be connected later.
                                </div>
                            @endif

                            <a href="{{ route('dashboard.users.books', ['track' => $track]) }}" class="btn btn-light w-100 mt-2">
                                Back to Books
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('js')
@endsection
