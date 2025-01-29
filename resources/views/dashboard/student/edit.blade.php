@extends('dashboard.layout')

@section('content')
    <main id="main" class="main">

        <!-- Page Title -->
        <div class="page-title" data-aos="fade">
            <div class="heading">
                <div class="container">
                    <div class="row d-flex justify-content-center text-center">
                        <div class="col-lg-8">
                            <h1>Edit Student</h1>
                        </div>
                    </div>
                </div>
            </div>
            <nav class="breadcrumbs">
                <div class="container">
                    <ol>
                        <li><a href={{ route('dashboard') }}>Dashboard</a></li>
                        <li class="current">Edit Student</li>
                    </ol>
                </div>
            </nav>
        </div><!-- End Page Title -->

        <section class="section">
            <div class="row">
                <div class="card mb-3">

                    <div class="card-body">

                        <form class="row g-3 needs-validation" novalidate method="post" action={{ route('editStudent', ['student_id'=>$student['id']??0, 'prev_img'=>$student['imageURL']??'']) }}
                            enctype="multipart/form-data">
                            @csrf
                            @method('put')
                            <div class="pt-4 pb-2">
                                <h5 class="card-title text-center pb-0 fs-4">Student Information</h5>
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
                                <label for="studentName" class="col-sm-2 col-lg-4 col-form-label">Student Name</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="student_name"
                                        value="{{ $student['name'] ?? '' }}" required>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <label for="studentEmail" class="col-sm-2 col-lg-4 col-form-label">Student Email</label>
                                <div class="col-sm-10">
                                    <input type="email" class="form-control" name="student_email" required value="{{ $student['email'] ?? '' }}">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <label for="studentPassword" class="col-sm-2 col-lg-4 col-form-label">Student Password</label>
                                <div class="col-sm-10">
                                    <input type="password" class="form-control" name="student_password" required>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <label class="col-sm-2 col-lg-4 col-form-label">Specialty</label>
                                <div class="col-sm-10">
                                    <select class="form-select" aria-label="Default select example" name="student_specialty"
                                        required>
                                        <option value="" {{ empty($student['specialty']) ? 'selected' : '' }}>Open this
                                            select menu</option>
                                        @php
                                            $specialties = [
                                                'Web Development',
                                                'Data Science',
                                                'Artificial Intelligence',
                                                'Business',
                                                'Personal Development',
                                            ];
                                        @endphp
                                        @foreach ($specialties as $specialty)
                                            <option value="{{ $specialty }}"
                                                {{ isset($student['specialty']) && $student['specialty'] == $specialty ? 'selected' : '' }}>
                                                {{ $specialty }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <label for="student_about" class="col-sm-2 col-lg-4 col-form-label">About Student
                                    </label>
                                <div class="col-sm-10">
                                    <textarea class="form-control" style="height: 100px" name="student_about"
                                        required>{{ isset($student['about']) && $student['about'] ? $student['about'] : '' }}</textarea>
                                </div>
                            </div>

                            <!-- Image Upload with Preview -->
                            <div class="image-upload-container col-lg-6">
                                <!-- Image Preview -->
                                <div class="mb-4 d-flex justify-content-center">
                                    <img id="selectedImage"
                                        src={{ isset($student['imageURL']) && $student['imageURL'] ? $student['imageURL'] : 'https://mdbootstrap.com/img/Photos/Others/placeholder.jpg' }}
                                        alt="Image Preview" class="img-fluid rounded shadow" style="max-width: 300px;" />
                                </div>

                                <!-- File Upload Button -->
                                <div class="d-flex justify-content-center">
                                    <label for="customFile1"
                                        class="btn btn-primary btn-rounded d-inline-flex align-items-center"
                                        style="cursor: pointer;">
                                        <span class="text-white">Student Image</span>
                                        <input type="file" class="form-control d-none" name="student_img" id="customFile1"
                                            accept="image/*" onchange="displaySelectedImage(event, 'selectedImage')" />
                                    </label>
                                </div>
                            </div>

                            <div class="col-12">
                                <button class="btn btn-secondary w-100" type="submit">Edit Student</button>
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
