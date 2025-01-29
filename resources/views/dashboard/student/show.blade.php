@extends('dashboard.layout')

@section('content')
    <main id="main" class="main">
        <!-- Page Title -->
        <div class="page-title" data-aos="fade">
            <div class="heading">
                <div class="container">
                    <div class="row d-flex justify-content-center text-center">
                        <div class="col-lg-8">
                            <h1>Student Details</h1>
                        </div>
                    </div>
                </div>
            </div>
            <nav class="breadcrumbs">
                <div class="container">
                    <ol>
                        <li><a href={{ route('dashboard') }}>Dashboard</a></li>
                        <li class="current">Student Details</li>
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
                            <img src={{ isset($student['imageURL']) ? $student['imageURL'] : asset('/Mentor/assets/img/team/team-1.jpg') }}
                                class="img-fluid" alt="">
                            <div class="social">
                                <a href="#"><i class="bi bi-twitter-x"></i></a>
                                <a href="#"><i class="bi bi-facebook"></i></a>
                                <a href="#"><i class="bi bi-instagram"></i></a>
                                <a href="#"><i class="bi bi-linkedin"></i></a>
                            </div>
                        </div>
                        <div class="member-info text-center">
                            <h4>{{ isset($student['name']) ? $student['name'] : 'Unknown Student' }}</h4>
                            <span>{{ isset($student['specialty']) ? $student['specialty'] : 'Unknown specialty' }}</span>
                            <p>{{ isset($student['about']) ? $student['about'] : 'Unknown about' }}</p>
                            <div class="mt-3">
                                <a href="{{ route('editStudent', ['student_id' => $student['id']]) }}"
                                    class="btn btn-secondary text-white">Edit</a>
                                @include('dashboard.student.delete', ['student_id' => $student['id']])
                            </div>
                        </div>
                    </div><!-- End Team Member -->
                </div>
            </div>
        </section><!-- /Trainers Section -->
    </main><!-- End #main -->
@endsection
