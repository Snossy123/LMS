@extends('dashboard.layout')

@section('content')
    <main id="main" class="main">

        <!-- Page Title -->
        <div class="page-title" data-aos="fade">
            <div class="heading">
                <div class="container">
                    <div class="row d-flex justify-content-center text-center">
                        <div class="col-lg-8">
                            <h1>Add New Teacher</h1>
                        </div>
                    </div>
                </div>
            </div>
            <nav class="breadcrumbs">
                <div class="container">
                    <ol>
                        <li><a href={{ route('dashboard.admin') }}>Dashboard</a></li>
                        <li class="current">Add Teacher</li>
                    </ol>
                </div>
            </nav>
        </div><!-- End Page Title -->

        <section class="section">
            <div class="row">
                <div class="card mb-3">

                    <div class="card-body">

                        <form class="row g-3 needs-validation" novalidate method="post" action={{ route('addTeacher') }}
                            enctype="multipart/form-data">
                            @csrf
                            <div class="pt-4 pb-2">
                                <h5 class="card-title text-center pb-0 fs-4">Teacher Information</h5>
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
                                <label for="teacherName" class="col-sm-2 col-lg-4 col-form-label">Teacher Name</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="teacher_name" required value="{{old('teacher_name')}}">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <label for="teacherEmail" class="col-sm-2 col-lg-4 col-form-label">Teacher Email</label>
                                <div class="col-sm-10">
                                    <input type="email" class="form-control" name="teacher_email" required value="{{old('teacher_email')}}">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <label for="teacherPassword" class="col-sm-2 col-lg-4 col-form-label">Teacher Password</label>
                                <div class="col-sm-10">
                                    <input type="password" class="form-control" name="teacher_password" required value="{{old('teacher_password')}}">
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <label class="col-sm-2 col-lg-4 col-form-label">Specialty</label>
                                <div class="col-sm-10">
                                    <select class="form-select" aria-label="Default select example" name="teacher_specialty"
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
                                <label for="teacher_about" class="col-sm-2 col-lg-4 col-form-label">About Teacher</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control" style="height: 100px" name="teacher_about" required>{{old('teacher_about')}}</textarea>
                                </div>
                            </div>

                            <!-- Image Upload with Preview -->
                            <div class="image-upload-container col-lg-6">
                                <!-- Image Preview -->
                                <div class="mb-4 d-flex justify-content-center">
                                    <img id="selectedImage" src="https://mdbootstrap.com/img/Photos/Others/placeholder.jpg"
                                        alt="Image Preview" class="img-fluid rounded-circle shadow" style="max-width: 300px;" />
                                </div>

                                <!-- File Upload Button -->
                                <div class="d-flex justify-content-center">
                                    <label for="customFile1"
                                        class="btn btn-primary btn-rounded d-inline-flex align-items-center"
                                        style="cursor: pointer;">
                                        <span class="text-white">Teacher Image</span>
                                        <input type="file" class="form-control d-none" name="teacher_img" id="customFile1"
                                            accept="image/*" onchange="displaySelectedImage(event, 'selectedImage')" />
                                    </label>
                                </div>
                            </div>

                            <div class="col-12">
                                <button class="btn btn-primary w-100" type="submit">Add Teacher</button>
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
