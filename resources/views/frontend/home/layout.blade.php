@extends('frontend.layout')

@section('content')
<main class="main">

    <!-- Hero Section -->
    @include('frontend.home.sections.hero')

    <!-- About Section -->
    @include('frontend.home.sections.about')

    <!-- Counts Section -->
    @include('frontend.home.sections.counts')

    <!-- Why Us Section -->
    @include('frontend.home.sections.whyUs')

    <!-- Features Section -->
    @include('frontend.home.sections.features')

    <!-- Courses Section -->
    @include('frontend.home.sections.courses')

    <!-- Trainers Index Section -->
    @include('frontend.home.sections.trainers')

</main>
@endsection
