@extends('dashboard.layout')

@section('content')
    <main id="main" class="main">

        <!-- Page Title -->
        <div class="page-title" data-aos="fade">
            <div class="heading">
                <div class="container">
                    <div class="row d-flex justify-content-center text-center">
                        <div class="col-lg-8">
                            <h1>Teachers</h1>
                        </div>
                    </div>
                </div>
            </div>
            <nav class="breadcrumbs">
                <div class="container">
                    <ol>
                        <li><a href={{ route('dashboard') }}>Dashboard</a></li>
                        <li class="current">All Teachers</li>
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
                            <h5 class="card-title">Teachers</h5>
                            <!-- Table with stripped rows -->
                            <table class="table datatable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Image</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Specialty</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($teachers as $teacher)
                                        <tr class="align-middle">
                                            <td>{{ $loop->iteration + ($teachers->currentPage() - 1) * $teachers->perPage() }}
                                            </td>
                                            <td>
                                                <img id="selectedImage"
                                                    src="{{ $teacher['image'] ?? 'https://mdbootstrap.com/img/Photos/Others/placeholder.jpg' }}"
                                                    alt="Image Preview" class="img-fluid rounded-circle shadow"
                                                    style="max-width: 40px;" />
                                            </td>
                                            <td>{{ $teacher['name'] }}</td>
                                            <td>{{ $teacher['email'] }}</td>
                                            <td>{{ $teacher['specialty'] }}</td>
                                            <td>
                                                <a href="{{ route('showTeacher', ['teacher_id' => $teacher['id']]) }}"
                                                    class="btn btn-primary">View</a>
                                                <a href="{{ route('editTeacher', ['teacher_id' => $teacher['id']]) }}"
                                                    class="btn btn-secondary">Edit</a>
                                                @include('dashboard.teacher.delete', [
                                                    'teacher_id' => $teacher['id'],
                                                ])
                                            </td>
                                        </tr>

                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">No teachers available.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            <!-- End Table with stripped rows -->

                            <!-- Pagination Links -->
                            <div class="d-flex justify-content-center">
                                {{ $teachers->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>


    </main><!-- End #main -->
@endsection
