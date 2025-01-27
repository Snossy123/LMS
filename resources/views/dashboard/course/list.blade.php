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
                        <li><a href={{ route('dashboard') }}>Dashboard</a></li>
                        <li class="current">All Courses</li>
                    </ol>
                </div>
            </nav>
        </div><!-- End Page Title -->

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
                                    @foreach($courses as $course)
                                    <tr>
                                        <td>{{$loop->iteration}}</td>
                                        <td>{{$course['title']}}</td>
                                        <td>{{$course['category']}}</td>
                                        <td>{{$course['level']}}</td>
                                        <td>{{$course['language']}}</td>
                                        <td>
                                            <a href="{{route('showCourse', ['course_id'=>$course['id']])}}" class="btn btn-primary">View</a>
                                            <a href="{{route('editCourse', ['course_id'=>$course['id']])}}" class="btn btn-secondary">Edit</a>
                                            {{-- <a href="{{route('deleteCourse', ['course_id'=>$course['id']])}}" class="btn btn-danger">Delete</a> --}}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <!-- End Table with stripped rows -->

                        </div>
                    </div>

                </div>
            </div>
        </section>

    </main><!-- End #main -->
@endsection
