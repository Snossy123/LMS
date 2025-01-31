@extends('dashboard.layout')

@section('content')

    <main id="main" class="main">

        <div class="pagetitle">
        <h1>Dashboard Page</h1>
        <nav class="d-flex justify-content-between">
            <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route(Auth::user()->data['type']==='Admin'?'dashboard.admin':'dashboard.teacher')}}">Dashboard</a></li>
            <li class="breadcrumb-item active">Teacher Report</li>
            </ol>
            <a href="{{ route('export.pdf') }}" class="btn btn-danger mb-3">
                Export as PDF
            </a>
        </nav>
        </div><!-- End Page Title -->

        @include('dashboard.reports.teacher-report')


    </main><!-- End #main -->


@endsection
