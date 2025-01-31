<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            padding: 0;
        }
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .logo {
            max-width: 100px;
        }
        .title {
            margin-left: 15px;
        }
        .title h1 {
            font-size: 22px;
            margin-bottom: 5px;
        }
        .title p {
            margin: 2px 0;
            font-size: 14px;
            color: #555;
        }
        .teacher-card {
            text-align: center;
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
        }
        .teacher-card img {
            max-width: 120px;
            border-radius: 50%;
            margin-bottom: 10px;
        }
        .teacher-card p {
            font-size: 14px;
            margin: 5px 0;
        }
        .teacher-card h4 {
            margin: 5px 0;
            font-size: 18px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .table th, .table td {
            border: 1px solid #ddd;
            padding: 8px;
            font-size: 14px;
            text-align: left;
        }
        .table th {
            background-color: #f8f8f8;
            font-weight: bold;
        }
        .table img {
            max-width: 40px;
            border-radius: 5px;
        }
        .course-section {
            margin-bottom: 30px;
        }
        .course-section h3 {
            font-size: 18px;
            margin-bottom: 10px;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="d-flex align-items-center">
            {{-- <img src="{{ asset('Mentor/assets/img/logo.png') }}" alt="Mentor Company Logo" class="logo"> --}}
            <div class="title">
                <h1>Mentor Company</h1>
                <p><strong>Teacher Report:</strong> {{ $teacher_report->get('name') }}</p>
                <p>Report Date: {{ now()->format('d/m/Y H:i') }}</p>
            </div>
        </div>
    </div>

    <div class="teacher-card">
        {{-- <img src="{{ $teacher_report->get('image') }}" alt="Teacher Image"> --}}
        <p><strong>ID:</strong> {{ $teacher_report->get('id') }}</p>
        <h4>{{ $teacher_report->get('name') }}</h4>
        <p><strong>Specialty:</strong> {{ $teacher_report->get('specialty') }}</p>
        <p><strong>Total Courses:</strong> {{ $teacher_report->get('totalCourses') }}</p>
        <p><strong>Total Students:</strong> {{ $teacher_report->get('totalStudents') }}</p>
    </div>

    <h2>Courses Overview</h2>
    <table class="table">
        <thead>
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
                    <td colspan="5" class="text-center">No Courses Available</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @foreach ($teacher_report->get('courses') as $course)
        <div class="course-section">
            <h3>{{ $loop->iteration }}. {{ $course->get('title') }}</h3>
            @if(empty($course->get('students')) || $course->get('students')[0]->get('id') === null)
                <p>No Students Enrolled</p>
            @else
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            {{-- <th>Image</th> --}}
                            <th>Student Name</th>
                            <th>Student Code</th>
                            <th>Email</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($course->get('students') as $student)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                {{-- <td>
                                    <img src="{{ $student->get('image') }}" alt="Student Image">
                                </td> --}}
                                <td>{{ $student->get('name') }}</td>
                                <td>{{ $student->get('id') }}</td>
                                <td>{{ $student->get('email') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    @endforeach

</body>
</html>
