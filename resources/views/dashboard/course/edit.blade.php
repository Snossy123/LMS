@extends('dashboard.layout')

@section('content')
    <main id="main" class="main">

        <!-- Page Title -->
        <div class="page-title" data-aos="fade">
            <div class="heading">
                <div class="container">
                    <div class="row d-flex justify-content-center text-center">
                        <div class="col-lg-8">
                            <h1>Edit Course</h1>
                        </div>
                    </div>
                </div>
            </div>
            <nav class="breadcrumbs">
                <div class="container">
                    <ol>
                        <li><a href={{ route('dashboard.admin') }}>Dashboard</a></li>
                        <li class="current">Edit Course</li>
                    </ol>
                </div>
            </nav>
        </div><!-- End Page Title -->

        <section class="section">
            <div class="row">
                <div class="card mb-3">

                    <div class="card-body">

                        <form class="row g-3 needs-validation" novalidate method="post" action={{ route('editCourse', ['course_id'=>$course['id']??0, 'prev_img'=>$course['imageURL']??'', 'prev_teacher'=>$course['teacher_id']??'']) }}
                            enctype="multipart/form-data">
                            @csrf
                            @method('put')
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
                                    <input type="text" class="form-control" name="course_title"
                                        value="{{ $course['title'] ?? '' }}" required>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <label class="col-sm-2 col-lg-4 col-form-label">Category</label>
                                <div class="col-sm-10">
                                    <select class="form-select" aria-label="Default select example" name="course_category"
                                        required>
                                        <option value="" {{ empty($course['category']) ? 'selected' : '' }}>Open this
                                            select menu</option>
                                        @php
                                            $categories = [
                                                'Web Development',
                                                'Data Science',
                                                'Artificial Intelligence',
                                                'Business',
                                                'Personal Development',
                                            ];
                                        @endphp
                                        @foreach ($categories as $category)
                                            <option value="{{ $category }}"
                                                {{ isset($course['category']) && $course['category'] == $category ? 'selected' : '' }}>
                                                {{ $category }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <label class="col-sm-2 col-lg-4 col-form-label">Level</label>
                                <div class="col-sm-10">
                                    <select class="form-select" aria-label="Default select example" name="course_level"
                                        required>
                                        <option value="" {{ empty($course['level']) ? 'selected' : '' }}>Open this
                                            select menu</option>
                                        @php
                                            $levels = ['Beginner', 'Intermediate', 'Advanced'];
                                        @endphp
                                        @foreach ($levels as $level)
                                            <option value="{{ $level }}"
                                                {{ isset($course['level']) && $course['level'] == $level ? 'selected' : '' }}>
                                                {{ $level }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>


                            <div class="col-lg-6">
                                <label class="col-sm-2 col-lg-4 col-form-label">Language</label>
                                <div class="col-sm-10">
                                    <select class="form-select" aria-label="Default select example" name="course_language"
                                        required>
                                        <option value="" {{ empty($course['language']) ? 'selected' : '' }}>Open this
                                            select menu</option>
                                        @php
                                            $languages = ['English', 'Arabic', 'Spanish', 'French'];
                                        @endphp
                                        @foreach ($languages as $language)
                                            <option value="{{ $language }}"
                                                {{ isset($course['language']) && $course['language'] == $language ? 'selected' : '' }}>
                                                {{ $language }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <label for="course_description" class="col-sm-2 col-lg-4 col-form-label">Course
                                    Description</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control" style="height: 100px" name="course_description"
                                        required>{{ isset($course['description']) && $course['description'] ? $course['description'] : '' }}</textarea>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <label for="course_description" class="col-sm-2 col-lg-4 col-form-label">Course
                                    Details</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control" style="height: 100px" name="course_details"
                                        required>{{ isset($course['details']) && $course['details'] ? $course['details'] : '' }}</textarea>
                                </div>
                            </div>

                            <!-- Image Upload with Preview -->
                            <div class="image-upload-container col-lg-6">
                                <!-- Image Preview -->
                                <div class="mb-4 d-flex justify-content-center">
                                    <img id="selectedImage"
                                        src={{ isset($course['imageURL']) && $course['imageURL'] ? asset($course['imageURL']) : 'https://mdbootstrap.com/img/Photos/Others/placeholder.jpg' }}
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
                                        <option value="" {{ empty($teachers) ? 'selected' : '' }}>Open this
                                            select menu</option>
                                        @forelse($teachers as $teacher)
                                        <option value={{$teacher['id']}}
                                        {{ isset($course['teacher_id']) && $course['teacher_id'] == $teacher['id'] ? 'selected' : '' }}>
                                        {{$teacher['name'].' : '.$teacher['specialty']}} </option>

                                        @empty
                                        <option value="">No Teacher</option>
                                        @endforelse
                                    </select>
                                </div>
                            </div>

                            <div class="col-12">
                                <button class="btn btn-secondary w-100" type="submit">Edit Course</button>
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
