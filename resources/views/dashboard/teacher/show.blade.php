@extends('dashboard.layout')

@section('content')
    <main id="main" class="main">
        <!-- Page Title -->
        <div class="page-title" data-aos="fade">
            <div class="heading">
                <div class="container">
                    <div class="row d-flex justify-content-center text-center">
                        <div class="col-lg-8">
                            <h1>Teacher Details</h1>
                        </div>
                    </div>
                </div>
            </div>
            <nav class="breadcrumbs">
                <div class="container">
                    <ol>
                        <li><a href={{ route('dashboard') }}>Dashboard</a></li>
                        <li class="current">Teacher Details</li>
                    </ol>
                </div>
            </nav>
        </div><!-- End Page Title -->

        <!-- Trainers Section -->
        <section id="trainers" class="section trainers">
            <div class="container">
                <div class="row gy-5">
                    <div class="col-lg-4 col-md-6 member" data-aos="fade-up" data-aos-delay="100">
                        <div class="member-img">
                            <img src={{ isset($teacher['imageURL']) ? $teacher['imageURL'] : asset('/Mentor/assets/img/team/team-1.jpg') }}
                                class="img-fluid" alt="">
                            <div class="social">
                                <a href="#"><i class="bi bi-twitter-x"></i></a>
                                <a href="#"><i class="bi bi-facebook"></i></a>
                                <a href="#"><i class="bi bi-instagram"></i></a>
                                <a href="#"><i class="bi bi-linkedin"></i></a>
                            </div>
                        </div>
                        <div class="member-info text-center">
                            <h4>{{ isset($teacher['name']) ? $teacher['name'] : 'Unknown Teacher' }}</h4>
                            <span>{{ isset($teacher['specialty']) ? $teacher['specialty'] : 'Unknown specialty' }}</span>
                            <p>{{ isset($teacher['about']) ? $teacher['about'] : 'Unknown about' }}</p>
                            <div class="mt-3">
                                <a href="{{ route('editTeacher', ['teacher_id' => $teacher['id']]) }}"
                                    class="btn btn-secondary text-white">Edit</a>
                                @include('dashboard.teacher.delete', ['teacher_id' => $teacher['id']])
                            </div>
                        </div>
                    </div><!-- End Team Member -->
                </div>
            </div>
        </section><!-- /Trainers Section -->
    </main><!-- End #main -->
@endsection
