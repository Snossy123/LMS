<section class="section">
    <div class="container-fluid p-4 border-bottom d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <img src="{{ asset('Mentor/assets/img/logo.png') }}" alt="Mentor Company Logo" class="img-fluid" style="max-width: 100px;">
            <div class="ms-3">
                <h1 class="h4 mb-1">Mentor Company</h1>
                <p class="mb-1 fw-bold">Teacher Report: {{ $teacher_report->get('name') }}</p>
                <p class="mb-0 text-muted">Report Date: {{ now()->format('d/m/Y H:i') }}</p>
            </div>
        </div>
        

    </div>

    <div class="card my-3">
        <div class="card-body text-center">
            <img src="{{ $teacher_report->get('image') }}" alt="Teacher Image" class="img-fluid rounded-circle mb-3" style="max-width: 150px;">
            <p class="mb-1 text-muted">ID: {{ $teacher_report->get('id') }}</p>
            <h4 class="mb-2">{{ $teacher_report->get('name') }}</h4>
            <p class="mb-2 text-primary">{{ $teacher_report->get('specialty') }}</p>
            <p><strong>Total Courses:</strong> {{ $teacher_report->get('totalCourses') }}</p>
            <p><strong>Total Students:</strong> {{ $teacher_report->get('totalStudents') }}</p>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Course Name</th>
                    <th>Category</th>
                    <th>Level</th>
                    <th>Total Students</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($teacher_report->get('courses') as $course)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $course->get('title') }}</td>
                        <td>{{ $course->get('category') }}</td>
                        <td>{{ $course->get('level') }}</td>
                        <td>{{ $course->get('totalCourseStudent') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">No Courses Available</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @foreach ($teacher_report->get('courses') as $course)
        <div class="mb-5">
            <h3 class="mb-3">{{ $loop->iteration }}. {{ $course->get('title') }}</h3>
            @if(empty($course->get('students')) || $course->get('students')[0]->get('id') === null)
                <p>No Students Enrolled</p>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Image</th>
                                <th>Student Name</th>
                                <th>Student Code</th>
                                <th>Email</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($course->get('students') as $student)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <img src="{{ $student->get('image') }}" alt="Student Image" class="img-thumbnail" style="max-width: 50px;">
                                    </td>
                                    <td>{{ $student->get('name') }}</td>
                                    <td>{{ $student->get('id') }}</td>
                                    <td>{{ $student->get('email') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endforeach
</section>
