@extends('dashboard.layout')

@section('content')
    <main id="main" class="main">

        <!-- Page Title -->
        <div class="page-title" data-aos="fade">
            <div class="heading">
                <div class="container">
                    <div class="row d-flex justify-content-center text-center">
                        <div class="col-lg-8">
                            <h1>Create New Course</h1>
                        </div>
                    </div>
                </div>
            </div>
            <nav class="breadcrumbs">
                <div class="container">
                    <ol>
                        <li><a href={{ route('dashboard.admin') }}>Dashboard</a></li>
                        <li class="current">Add Course</li>
                    </ol>
                </div>
            </nav>
        </div><!-- End Page Title -->

        <section class="section">
            <div class="row">
                <div class="card mb-3">

                    <div class="card-body">

                        <form class="row g-3 needs-validation" novalidate method="post" action={{ route('addCourse') }}
                            enctype="multipart/form-data">
                            @csrf
                            <div class="pt-4 pb-2">
                                <h5 class="card-title text-center pb-0 fs-4">Course Information</h5>
                                @if ($errors->any())
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <ul class="list-unstyled">
                                            @foreach ($errors->all() as $error)
                                                <li><i class="bi bi-exclamation-octagon me-1"></i>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"
                                            aria-label="Close"></button>
                                    </div>
                                @endif
                            </div>
                            <div class="col-lg-6">
                                <label for="courseTitle" class="col-sm-2 col-lg-4 col-form-label">Course Title</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="course_title" required value="{{old('course_title')}}">
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <label class="col-sm-2 col-lg-4 col-form-label">Category</label>
                                <div class="col-sm-10">
                                    <select class="form-select" aria-label="Default select example" name="course_category"
                                        required>
                                        <option selected value="">Open this select menu</option>
                                        <option value="Web Development">Web Development</option>
                                        <option value="Data Science">Data Science</option>
                                        <option value="Artificial Intelligence">Artificial Intelligence</option>
                                        <option value="Business">Business</option>
                                        <option value="Personal Development">Personal Development</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <label class="col-sm-2 col-lg-4 col-form-label">Level</label>
                                <div class="col-sm-10">
                                    <select class="form-select" aria-label="Default select example" name="course_level"
                                        required>
                                        <option selected value="">Open this select menu</option>
                                        <option value="Beginner">Beginner</option>
                                        <option value="Intermediate">Intermediate</option>
                                        <option value="Advanced">Advanced</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <label class="col-sm-2 col-lg-4 col-form-label">Language</label>
                                <div class="col-sm-10">
                                    <select class="form-select" aria-label="Default select example" name="course_language"
                                        required>
                                        <option selected value="">Open this select menu</option>
                                        <option value="English">English</option>
                                        <option value="Arabic">Arabic</option>
                                        <option value="Spanish">Spanish</option>
                                        <option value="French">French</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <label for="course_description" class="col-sm-2 col-lg-4 col-form-label">Course
                                    Description</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control" style="height: 100px" name="course_description" required>{{old('course_description')}}</textarea>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <label for="course_description" class="col-sm-2 col-lg-4 col-form-label">Course
                                    Details</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control" style="height: 100px" name="course_details" required>{{old('course_details')}}</textarea>
                                </div>
                            </div>

                            <!-- Image Upload with Preview -->
                            <div class="image-upload-container col-lg-6">
                                <!-- Image Preview -->
                                <div class="mb-4 d-flex justify-content-center">
                                    <img id="selectedImage" src="https://mdbootstrap.com/img/Photos/Others/placeholder.jpg"
                                        alt="Image Preview" class="img-fluid rounded shadow" style="max-width: 300px;" />
                                </div>

                                <!-- File Upload Button -->
                                <div class="d-flex justify-content-center">
                                    <label for="customFile1"
                                        class="btn btn-primary btn-rounded d-inline-flex align-items-center"
                                        style="cursor: pointer;">
                                        <span class="text-white">Course Image</span>
                                        <input type="file" class="form-control d-none" name="course_img" id="customFile1"
                                            accept="image/*" onchange="displaySelectedImage(event, 'selectedImage')" />
                                    </label>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <label class="col-sm-2 col-lg-4 col-form-label">Teacher</label>
                                <div class="col-sm-10">
                                    <select class="form-select" aria-label="Default select example" name="course_teacher"
                                        required>
                                        <option selected value="">Open this select menu</option>
                                        @forelse($teachers as $teacher)
                                        <option value={{$teacher['id']}}>{{$teacher['name'].' '.$teacher['specialty']}} </option>
                                        @empty
                                        <option value="">No Teacher</option>
                                        @endforelse
                                    </select>
                                </div>
                            </div>

                            <div class="col-12">
                                <button class="btn btn-primary w-100" type="submit">Create Course</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </section>

    </main><!-- End #main -->

    <!-- JavaScript for Image Preview -->
    <script>
        function displaySelectedImage(event, imageElementId) {
            const fileInput = event.target;
            const imageElement = document.getElementById(imageElementId);

            if (fileInput.files && fileInput.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    imageElement.src = e.target.result;
                };

                reader.readAsDataURL(fileInput.files[0]);
            }
        }
    </script>
@endsection
