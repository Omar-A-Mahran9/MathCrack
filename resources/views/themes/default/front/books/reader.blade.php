<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $book->title }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        body {
            margin: 0;
            background: #f3f3f3;
            font-family: Arial, sans-serif;
        }

        .reader-header {
            position: sticky;
            top: 0;
            z-index: 10;
            background: #111;
            color: #fff;
            padding: 12px 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .reader-title {
            font-size: 16px;
            font-weight: 600;
        }

        .reader-pages {
            max-width: 980px;
            margin: 20px auto;
            padding: 0 12px 40px;
        }

        .page-box {
            margin-bottom: 24px;
            background: #fff;
            padding: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,.12);
        }

        .page-number {
            font-size: 13px;
            color: #555;
            margin-bottom: 8px;
        }

        .page-image {
            width: 100%;
            height: auto;
            display: block;
            user-select: none;
            -webkit-user-drag: none;
        }
    </style>

    <script>
        document.addEventListener('contextmenu', function (e) {
            e.preventDefault();
        });

        document.addEventListener('keydown', function (e) {
            if ((e.ctrlKey || e.metaKey) && ['s', 'p', 'u'].includes(e.key.toLowerCase())) {
                e.preventDefault();
            }
        });
    </script>
</head>
<body>

<div class="reader-header">
    <div class="reader-title">{{ $book->title }}</div>
    <div>{{ $book->total_pages }} pages</div>
</div>

<div class="reader-pages">
    @foreach($pages as $page)
        <div class="page-box">
            <div class="page-number">Page {{ $page->page_number }}</div>
            <img
                class="page-image"
                src="{{ route('books.reader.page', ['book' => $book->id, 'page' => $page->page_number]) }}"
                alt="Page {{ $page->page_number }}"
                loading="lazy"
                draggable="false"
            >
        </div>
    @endforeach
</div>

</body>
</html>
