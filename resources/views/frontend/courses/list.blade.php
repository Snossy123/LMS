@extends('frontend.layout')

@section('content')
    <main class="main">

        <!-- Page Title -->
        <div class="page-title" data-aos="fade">
            <div class="heading">
                <div class="container">
                    <div class="row d-flex justify-content-center text-center">
                        <div class="col-lg-8">
                            <h1>Courses</h1>
                            <p class="mb-0">Odio et unde deleniti. Deserunt numquam exercitationem. Officiis quo odio sint
                                voluptas consequatur ut a odio voluptatem. Sit dolorum debitis veritatis natus dolores.
                                Quasi ratione sint. Sit quaerat ipsum dolorem.</p>
                        </div>
                    </div>
                </div>
            </div>
            <nav class="breadcrumbs py-3 border-bottom">
                <div class="container d-flex justify-content-between align-items-center">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Courses</li>
                    </ol>
                    <form action="{{ route(isset($myCourses) ? 'courses' : 'studentCourses') }}" method="get">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked" onchange="this.form.submit()" {{ isset($myCourses) ? 'checked' : '' }}>
                            <label class="form-check-label" for="flexSwitchCheckChecked">Show My Courses</label>
                        </div>
                    </form>
                </div>
            </nav>

        </div><!-- End Page Title -->

        <!-- Courses Section -->
        <section id="courses" class="courses section">

            <div class="container">

                <div class="row">
                    @forelse($courses as $course)
                        <div class="col-lg-4 col-md-6 d-flex align-items-stretch" data-aos="zoom-in" data-aos-delay="100">
                            <div class="course-item">
                                <img src={{ $course['imageURL'] ?? asset('Mentor/assets/img/course-1.jpg') }}
                                    class="img-fluid" alt="...">
                                <div class="course-content">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <p class="category">{{ $course['category'] ?? '' }}</p>
                                    </div>
                                    {{-- replace # with {{ route("courseProfile", ["course_id"=>$course['id']] }} --}}
                                    <h3><a href={{ route("showCourse", ["course_id"=>$course['id']])}}>{{ $course['title'] ?? '' }}</a></h3>
                                    <p class="description">{{ $course['description'] ?? '' }}</p>
                                    <div class="trainer d-flex justify-content-between align-items-center">
                                        <div class="trainer-profile d-flex align-items-center">
                                            <img src="assets/img/trainers/trainer-1-2.jpg" class="img-fluid" alt="">
                                            {{-- replace # with {{ route("teacherProfile", ["teacher_id"=>$course['teacher_id']] }} --}}
                                            <a href="#" class="trainer-link">{{ $course['teacher_name'] ?? '' }}</a>
                                        </div>
                                        <div class="trainer-rank d-flex align-items-center">
                                            <i class="bi bi-person user-icon"></i>&nbsp;50
                                            &nbsp;&nbsp;
                                            <i class="bi bi-heart heart-icon"></i>&nbsp;65
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> <!-- End Course Item-->
                    @empty
                        <p colspan="6" class="text-center">No courses available.</p>
                    @endforelse

                </div>
                <!-- Pagination Links -->
                <div class="d-flex justify-content-center">
                    {{ $courses->links() }}
                </div>
            </div>

        </section><!-- /Courses Section -->

    </main>
@endsection
