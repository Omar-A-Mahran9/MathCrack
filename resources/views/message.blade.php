@extends('themes.default.layouts.master')

@section('content')

<div style="
min-height:80vh;
display:flex;
align-items:center;
justify-content:center;
background:#f8fafc;
">

<div style="
background:#ffffff;
padding:50px 60px;
border-radius:16px;
text-align:center;
box-shadow:0 15px 40px rgba(0,0,0,0.08);
max-width:520px;
width:100%;
">

<div style="font-size:50px;margin-bottom:20px;">
⚠️
</div>

<h3 style="
font-weight:700;
margin-bottom:15px;
color:#1e293b;
">
{{ $message ?? 'Something went wrong' }}
</h3>

<a href="{{ url('/') }}" style="
display:inline-block;
margin-top:20px;
background:#3b82f6;
color:white;
padding:12px 30px;
border-radius:8px;
text-decoration:none;
font-weight:600;
">
Back to Home
</a>

</div>

</div>

@endsection