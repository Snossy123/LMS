@extends('dashboard.layout')

@section('content')
    <main id="main" class="main">
        <!-- Page Title -->
        <div class="page-title" data-aos="fade">
            <div class="heading">
                <div class="container">
                    <div class="row d-flex justify-content-center text-center">
                        <div class="col-lg-8">
                            <h1>Course Details</h1>
                            <p class="mb-0">{{ isset($course['details']) ? $course['details'] : 'Unavailable details' }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <nav class="breadcrumbs">
                <div class="container">
                    <ol>
                        <li><a href={{ route('dashboard') }}>Dashboard</a></li>
                        <li class="current">Course Details</li>
                    </ol>
                </div>
            </nav>
        </div><!-- End Page Title -->

        <!-- Courses Course Details Section -->
        <section id="courses-course-details" class="courses-course-details section">

            <div class="container" data-aos="fade-up">

                <div class="row">
                    <div class="col-lg-8">
                        <img src={{ isset($course['imageURL']) ? $course['imageURL'] : asset('Mentor/assets/img/course-details.jpg') }} class="img-fluid" alt="">
                        <h3>{{ isset($course['title']) ? $course['title'] : 'Unavailable Title' }}</h3>
                        <p>
                            {{ isset($course['description']) ? $course['description'] : 'Unavailable description' }}
                        </p>
                    </div>
                    <div class="col-lg-4">

                        <div class="course-info d-flex justify-content-between align-items-center">
                            <h5>Teacher</h5>
                            @if (isset($teacher['name']))
                                <p><a href="#">{{ $teacher['name'] }}</a></p>
                            @else
                                <p>Unavailable Teacher</p>
                            @endif
                        </div>

                        <div class="course-info d-flex justify-content-between align-items-center">
                            <h5>Category</h5>
                            <p> {{ isset($course['category']) ? $course['category'] : 'Unavailable category' }}</p>
                        </div>

                        <div class="course-info d-flex justify-content-between align-items-center">
                            <h5>Level</h5>
                            <p> {{ isset($course['level']) ? $course['level'] : 'Unavailable level' }}</p>
                        </div>

                        <div class="course-info d-flex justify-content-between align-items-center">
                            <h5>Language</h5>
                            <p> {{ isset($course['language']) ? $course['language'] : 'Unavailable language' }}</p>
                        </div>

                    </div>
                </div>

            </div>

        </section><!-- /Courses Course Details Section -->
    </main><!-- End #main -->
@endsection
