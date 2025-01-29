<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\courseController;
use App\Http\Controllers\teacherController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::group(['prefix' => 'dashboard', 'middleware' => 'auth'], function () {
    Route::get('/', function () {return view('dashboard.dashboard');})->name('dashboard');

    // Courses Section
    Route::get('/addCourse', [courseController::class, 'addCoursePage'])->name('addCourse');
    Route::post('/addCourse', [courseController::class, 'addCourse']);
    Route::get('/showCourse', [courseController::class, 'showCourse'])->name('showCourse');
    Route::get('/showAllCourses', [courseController::class, 'showAllCourses'])->name('showAllCourses');
    Route::get('/editCourse', [courseController::class, 'editCoursePage'])->name('editCourse');
    Route::put('/editCourse', [courseController::class, 'editCourse']);
    Route::delete('/deleteCourse', [courseController::class, 'deleteCourse'])->name('deleteCourse');

    // Teachers Section
    Route::get('/addTeacher', [teacherController::class, 'addTeacherPage'])->name('addTeacher');
    Route::post('/addTeacher', [teacherController::class, 'addTeacher']);
    Route::get('/showTeacher', [teacherController::class, 'showTeacher'])->name('showTeacher');
    Route::get('/showAllTeachers', [teacherController::class, 'showAllTeachers'])->name('showAllTeachers');
    Route::get('/editTeacher', [teacherController::class, 'editTeacherPage'])->name('editTeacher');
    Route::put('/editTeacher', [teacherController::class, 'editTeacher']);
    Route::delete('/deleteTeacher', [teacherController::class, 'deleteTeacher'])->name('deleteTeacher');
});

Route::get('/login', [LoginController::class, 'showLoginPage'])->name('login');
Route::post('/login', [LoginController::class, 'authenticate']);
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/test-aura', function() {
    // Get the Neo4j client from the service container
    $client = app('neo4j');

    // Open a session to run the query
    $session = $client->createSession();

    // Run a query to create a node
    $query = '
        MERGE (a:Admin:Person {name: $name, email: $email, password: $password})
        RETURN a
    ';

    // Define the parameters
    $params = [
        'name' => 'Admin User5',
        'email' => 'admin5@example.com',
        'password' => bcrypt('admin123'), // Use a secure password
    ];

    // Execute the query and get the result
    $result = $session->run($query, $params);

    // Fetch the node from the result
    $adminNode = $result->first()->get('a');

    // Return a response
    return response()->json([
        'message' => 'Connection successful, node created.',
        'node' => $adminNode->getProperties(),
    ]);
});

