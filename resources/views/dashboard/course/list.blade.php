@extends('dashboard.layout')

@section('content')
    <main id="main" class="main">

        <!-- Page Title -->
        <div class="page-title" data-aos="fade">
            <div class="heading">
                <div class="container">
                    <div class="row d-flex justify-content-center text-center">
                        <div class="col-lg-8">
                            <h1>Courses</h1>
                        </div>
                    </div>
                </div>
            </div>
            <nav class="breadcrumbs">
                <div class="container">
                    <ol>
                        <li><a href={{ route('dashboard.admin') }}>Dashboard</a></li>
                        <li class="current">All Courses</li>
                    </ol>
                </div>
            </nav>
        </div><!-- End Page Title -->

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <ul class="list-unstyled">
                    <li><i class="bi bi-exclamation-octagon me-1"></i>{{ session('success') }}</li>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Courses</h5>
                            <!-- Table with stripped rows -->
                            <table class="table datatable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Title</th>
                                        <th>Category</th>
                                        <th>Level</th>
                                        <th>Language</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($courses as $course)
                                        <tr>
                                            <td>{{ $loop->iteration + ($courses->currentPage() - 1) * $courses->perPage() }}
                                            </td>
                                            <td>{{ $course['title']??'' }}</td>
                                            <td>{{ $course['category']??'' }}</td>
                                            <td>{{ $course['level']??'' }}</td>
                                            <td>{{ $course['language']??'' }}</td>
                                            <td>
                                                <a href="{{ route('showCourse', ['course_id' => $course['id']]) }}"
                                                    class="btn btn-primary">View</a>
                                                <a href="{{ route('editCourse', ['course_id' => $course['id']]) }}"
                                                    class="btn btn-secondary">Edit</a>
                                                @include('dashboard.course.delete', ['course_id' => $course['id']])
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">No courses available.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            <!-- End Table with stripped rows -->

                            <!-- Pagination Links -->
                            <div class="d-flex justify-content-center">
                                {{ $courses->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>


    </main><!-- End #main -->
@endsection
