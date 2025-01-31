@extends('dashboard.layout')

@section('content')
    <main id="main" class="main">

        <!-- Page Title -->
        <div class="page-title" data-aos="fade">
            <div class="heading">
                <div class="container">
                    <div class="row d-flex justify-content-center text-center">
                        <div class="col-lg-8">
                            <h1>Students</h1>
                        </div>
                    </div>
                </div>
            </div>
            <nav class="breadcrumbs">
                <div class="container">
                    <ol>
                        <li><a href={{ route('dashboard.admin') }}>Dashboard</a></li>
                        <li class="current">All Students</li>
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
                            <h5 class="card-title">Students</h5>
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
                                    @forelse($students as $student)
                                        <tr class="align-middle">
                                            <td>{{ $loop->iteration + ($students->currentPage() - 1) * $students->perPage() }}
                                            </td>
                                            <td>
                                                <img id="selectedImage"
                                                    src="{{ $student['image'] ?? 'https://mdbootstrap.com/img/Photos/Others/placeholder.jpg' }}"
                                                    alt="Image Preview" class="img-fluid rounded-circle shadow"
                                                    style="max-width: 40px;" />
                                            </td>
                                            <td>{{ $student['name']??'' }}</td>
                                            <td>{{ $student['email']??'' }}</td>
                                            <td>{{ $student['specialty']??'' }}</td>
                                            <td>
                                                <a href="{{ route('showStudent', ['student_id' => $student['id']]) }}"
                                                    class="btn btn-primary">View</a>
                                                <a href="{{ route('editStudent', ['student_id' => $student['id']]) }}"
                                                    class="btn btn-secondary">Edit</a>
                                                @include('dashboard.student.delete', [
                                                    'student_id' => $student['id'],
                                                ])
                                            </td>
                                        </tr>

                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">No students available.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            <!-- End Table with stripped rows -->

                            <!-- Pagination Links -->
                            <div class="d-flex justify-content-center">
                                {{ $students->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>


    </main><!-- End #main -->
@endsection
