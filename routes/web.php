<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\courseController;
use App\Http\Controllers\studentController;
use App\Http\Controllers\teacherController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TeacherReportController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

//--------------------------------------------------
// Main Landing Page Route
//--------------------------------------------------
Route::get('/', function () {
    return view('frontend.home.layout');
});

//--------------------------------------------------
// Student Frontend Routes
//--------------------------------------------------
Route::group(['middleware' => 'auth'], function () {
    Route::get('/showCourses', [courseController::class, 'showAllCourses'])->name('courses');
    Route::get('/showCourse', [courseController::class, 'showCourse'])->name('showCourse');
    Route::get('/enrollInCourse', [studentController::class, 'enrollInCourse'])->name('enrollInCourse');
    Route::get('/studentCourses', [courseController::class, 'studentCourses'])->name('studentCourses');
});

//--------------------------------------------------
// Authenticated Dashboard Routes
//--------------------------------------------------
Route::group(['prefix' => 'dashboard', 'middleware' => 'auth'], function () {
    // Teacher Dashboard
    Route::get('/teacher', [teacherController::class, 'showDashboard'])->name('dashboard.teacher');
    // Teacher Report Export Route
    Route::get('/teacher-report/export', [TeacherReportController::class, 'exportPDF'])->name('export.pdf');

    // Admin Dashboard
    Route::get('/admin', function () {
        return view('dashboard.admin');
    })->name('dashboard.admin');

    //--------------------------------------------------
    // Course Management Routes
    //--------------------------------------------------
    Route::get('/addCourse', [courseController::class, 'addCoursePage'])->name('addCourse');
    Route::post('/addCourse', [courseController::class, 'addCourse']);
    Route::get('/showCourse', [courseController::class, 'showCourse'])->name('showCourse');
    Route::get('/showAllCourses', [courseController::class, 'showAllCourses'])->name('showAllCourses');
    Route::get('/editCourse', [courseController::class, 'editCoursePage'])->name('editCourse');
    Route::put('/editCourse', [courseController::class, 'editCourse']);
    Route::delete('/deleteCourse', [courseController::class, 'deleteCourse'])->name('deleteCourse');

    //--------------------------------------------------
    // Teacher Management Routes
    //--------------------------------------------------
    Route::get('/addTeacher', [teacherController::class, 'addTeacherPage'])->name('addTeacher');
    Route::post('/addTeacher', [teacherController::class, 'addTeacher']);
    Route::get('/showTeacher', [teacherController::class, 'showTeacher'])->name('showTeacher');
    Route::get('/showAllTeachers', [teacherController::class, 'showAllTeachers'])->name('showAllTeachers');
    Route::get('/editTeacher', [teacherController::class, 'editTeacherPage'])->name('editTeacher');
    Route::put('/editTeacher', [teacherController::class, 'editTeacher']);
    Route::delete('/deleteTeacher', [teacherController::class, 'deleteTeacher'])->name('deleteTeacher');

    //--------------------------------------------------
    // Student Management Routes
    //--------------------------------------------------
    Route::get('/addStudent', [studentController::class, 'addStudentPage'])->name('addStudent');
    Route::post('/addStudent', [studentController::class, 'addStudent']);
    Route::get('/showStudent', [studentController::class, 'showStudent'])->name('showStudent');
    Route::get('/showAllStudents', [studentController::class, 'showAllStudents'])->name('showAllStudents');
    Route::get('/editStudent', [studentController::class, 'editStudentPage'])->name('editStudent');
    Route::put('/editStudent', [studentController::class, 'editStudent']);
    Route::delete('/deleteStudent', [studentController::class, 'deleteStudent'])->name('deleteStudent');
});

//--------------------------------------------------
// Authentication Routes
//--------------------------------------------------
Route::get('/login', [LoginController::class, 'showLoginPage'])->name('login');
Route::post('/login', [LoginController::class, 'authenticate']);
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

//--------------------------------------------------
// Neo4j Database Test Route (Temporary/Development)
//--------------------------------------------------
Route::get('/test-aura', function () {
    // Get the Neo4j client from the service container
    $client = app('neo4j');

    // Create session and run test query
    $session = $client->createSession();

    // Cypher query to create admin node
    $query = '
        MERGE (a:Admin:Person {name: $name, email: $email, password: $password})
        RETURN a
    ';

    // Parameters for the query
    $params = [
        'name' => 'Admin User5',
        'email' => 'admin5@example.com',
        'password' => bcrypt('admin123'),
    ];

    // Execute query and get result
    $result = $session->run($query, $params);
    $adminNode = $result->first()->get('a');

    // Return JSON response with created node details
    return response()->json([
        'message' => 'Connection successful, node created.',
        'node' => $adminNode->getProperties(),
    ]);
});
