@extends('themes.default.layouts.back.student-master')

@section('title')
    Books
@endsection

@section('css')
    <style>
        .books-track-hero {
            background: linear-gradient(135deg, #1e40af 0%, #2563eb 100%);
            border-radius: 16px;
            padding: 28px 30px;
            color: #fff;
            margin-bottom: 22px;
            box-shadow: 0 12px 30px rgba(30, 64, 175, 0.16);
        }

        .books-track-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255,255,255,.16);
            color: #fff;
            border-radius: 999px;
            padding: 8px 14px;
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 12px;
        }

        .books-track-hero h1 {
            color: #fff;
            font-size: 30px;
            font-weight: 900;
            margin-bottom: 8px;
        }

        .books-track-hero p {
            color: rgba(255,255,255,.90);
            margin-bottom: 0;
        }

        .books-dashboard {
            background: #fff;
            border-radius: 16px;
            padding: 22px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, .06);
            margin-bottom: 22px;
        }

        .books-dashboard-title {
            font-size: 22px;
            font-weight: 900;
            color: #111827;
            margin-bottom: 4px;
        }

        .books-dashboard-subtitle {
            color: #64748b;
            margin-bottom: 0;
        }

        .books-stats-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
            margin-top: 18px;
        }

        .books-stat-card {
            background: #f8fafc;
            border: 1px solid rgba(15, 23, 42, .08);
            border-radius: 14px;
            padding: 16px;
        }

        .books-stat-icon {
            width: 38px;
            height: 38px;
            border-radius: 12px;
            background: rgba(37, 99, 235, .12);
            color: #2563eb;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
        }

        .books-stat-number {
            display: block;
            color: #0f172a;
            font-size: 24px;
            font-weight: 900;
            line-height: 1;
        }

        .books-stat-label {
            display: block;
            color: #64748b;
            font-size: 13px;
            font-weight: 700;
            margin-top: 6px;
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

        .book-card {
            border: 2px solid #93c5fd;
            border-radius: 16px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, .10);
            transition: all .3s ease;
            height: 100%;
            overflow: hidden;
            background: #fff;
        }

        .book-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, .15);
        }

        .book-card-header {
            background: #f8fafc;
            border-bottom: 1px solid rgba(15, 23, 42, .08);
            padding: 16px 16px 14px;
        }

        .book-title {
            font-size: 17px;
            font-weight: 900;
            color: #111827;
            margin-bottom: 0;
        }

        .book-description {
            color: #64748b;
            font-size: 13px;
            min-height: 42px;
            margin-bottom: 14px;
        }

        .book-info-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 8px;
            margin-bottom: 10px;
        }

        .book-info-box {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 8px;
            padding: 8px 6px;
            text-align: center;
            color: #1d4ed8;
        }

        .book-info-number {
            display: block;
            font-size: 15px;
            font-weight: 900;
            line-height: 1.1;
        }

        .book-info-label {
            display: block;
            font-size: 10px;
            font-weight: 700;
            line-height: 1.2;
            margin-top: 2px;
        }

        .book-price-box {
            border-radius: 9px;
            padding: 9px 10px;
            text-align: center;
            font-weight: 900;
            margin-bottom: 12px;
        }

        .book-price-box.paid {
            background: #ecfdf5;
            border: 1px solid #bbf7d0;
            color: #047857;
        }

        .book-price-box.free {
            background: #ecfdf5;
            border: 1px solid #bbf7d0;
            color: #047857;
        }

        .book-price-box small {
            display: block;
            font-size: 11px;
            font-weight: 700;
            line-height: 1.1;
        }

        .book-status {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border-radius: 999px;
            padding: 6px 10px;
            font-size: 12px;
            font-weight: 800;
            margin-bottom: 10px;
        }

        .book-status.purchased {
            background: #dcfce7;
            color: #166534;
        }

        .book-status.not-purchased {
            background: #f1f5f9;
            color: #475569;
        }

        .book-lock-box {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            border-radius: 9px;
            padding: 9px 10px;
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

        .book-action-btn.btn-primary {
            background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 100%) !important;
            border-color: #1d4ed8 !important;
        }

        .book-action-btn.btn-success {
            background: linear-gradient(135deg, #059669 0%, #10b981 100%) !important;
            border-color: #059669 !important;
        }

        @media (max-width: 767.98px) {
            .books-stats-grid {
                grid-template-columns: 1fr;
            }

            .track-tabs {
                display: grid;
                grid-template-columns: 1fr;
            }

            .track-tabs .nav-link {
                justify-content: center;
                width: 100%;
            }
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

        $ownedBookIds = $ownedBookIds ?? [];
        $booksCount = $books->count();
        $ownedCount = collect($ownedBookIds)->count();
        $availableCount = max($booksCount - $ownedCount, 0);
    @endphp

    <div class="container py-4">

        <div class="books-track-hero">
            <div class="books-track-badge">
                <i class="fas fa-route"></i>
                <span>{{ $trackTitle }} Track</span>
            </div>

            <h1>{{ $trackTitle }} Books</h1>
            <p>Browse available books, purchase access, and read your books online.</p>
        </div>

        <div class="books-dashboard">
            <div class="d-flex flex-wrap align-items-start justify-content-between gap-3">
                <div>
                    <h2 class="books-dashboard-title">{{ $trackTitle }} Books Dashboard</h2>
                    <p class="books-dashboard-subtitle">
                        Your books are protected with personal watermark and available for online reading.
                    </p>
                </div>

                <a href="{{ route('dashboard.users.courses', ['track' => $track]) }}" class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i>
                    Back to Course
                </a>
            </div>

            <div class="books-stats-grid">
                <div class="books-stat-card">
                    <div class="books-stat-icon">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <span class="books-stat-number">{{ $booksCount }}</span>
                    <span class="books-stat-label">Available Books</span>
                </div>

                <div class="books-stat-card">
                    <div class="books-stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <span class="books-stat-number">{{ $ownedCount }}</span>
                    <span class="books-stat-label">Purchased Books</span>
                </div>

                <div class="books-stat-card">
                    <div class="books-stat-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <span class="books-stat-number">{{ $availableCount }}</span>
                    <span class="books-stat-label">Not Purchased Yet</span>
                </div>
            </div>
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
            @forelse($books as $book)
                @php
                    $owned = in_array($book->id, $ownedBookIds);
                    $isFree = (float) $book->price <= 0;
                @endphp

                <div class="col-xl-4 col-lg-6 col-md-6 mb-4">
                    <div class="card book-card">
                        <div class="book-card-header">
                            <h5 class="book-title">{{ $book->title }}</h5>
                        </div>

                        <div class="card-body d-flex flex-column">
                            <div class="book-description">
                                {{ \Illuminate\Support\Str::limit($book->description ?? 'No description available.', 110) }}
                            </div>

                            <div class="book-info-grid">
                                <div class="book-info-box">
                                    <span class="book-info-number">{{ $book->total_pages }}</span>
                                    <span class="book-info-label">Pages</span>
                                </div>

                                <div class="book-info-box">
                                    <span class="book-info-number">
                                        @if(empty($book->access_duration_days))
                                            ∞
                                        @else
                                            {{ $book->access_duration_days }}
                                        @endif
                                    </span>
                                    <span class="book-info-label">
                                        @if(empty($book->access_duration_days))
                                            Lifetime
                                        @else
                                            Days
                                        @endif
                                    </span>
                                </div>

                                <div class="book-info-box">
                                    <span class="book-info-number">{{ $book->allow_print ? 'Yes' : 'No' }}</span>
                                    <span class="book-info-label">Print</span>
                                </div>
                            </div>

                            @if($isFree)
                                <div class="book-price-box free">
                                    Free
                                    <small>Book Price</small>
                                </div>
                            @else
                                <div class="book-price-box paid">
                                    {{ number_format((float) $book->price, 2) }} EGP
                                    <small>Book Price</small>
                                </div>
                            @endif

                            @if($owned)
                                <div class="book-status purchased">
                                    <i class="fas fa-check-circle"></i>
                                    Purchased
                                </div>

                                <div class="book-lock-box unlocked">
                                    <i class="fas fa-unlock"></i>
                                    Access available
                                </div>

                                <div class="mt-auto">
                                    <a href="{{ route('books.reader.read', ['book' => $book->slug]) }}" class="btn btn-success book-action-btn">
                                        <i class="fas fa-book-open"></i>
                                        Read Book
                                    </a>
                                </div>
                            @else
                                <div class="book-status not-purchased">
                                    <i class="fas fa-play-circle"></i>
                                    Not Purchased
                                </div>

                                <div class="book-lock-box locked">
                                    <i class="fas fa-lock"></i>
                                    Locked - Purchase required
                                </div>

                                <div class="mt-auto">
                                    <a href="{{ route('dashboard.users.books-show', ['book' => $book->slug, 'track' => $track]) }}" class="btn btn-primary book-action-btn">
                                        <i class="fas fa-shopping-cart"></i>
                                        {{ $isFree ? 'Get Free Book' : 'Purchase Book' }}
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info">
                        No books are available for this track now.
                    </div>
                </div>
            @endforelse
        </div>
    </div>
@endsection

@section('js')
@endsection
