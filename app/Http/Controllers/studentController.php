<?php

namespace App\Http\Controllers;

use App\Http\Requests\studentCreationRequest;
use App\Http\Requests\studentUpdateRequest;
use App\Services\studentService;
use Illuminate\Http\Request;

class studentController extends Controller
{
    /**
     * Constructor to inject the studentService dependency.
     *
     * @param studentService $studentService
     */
    public function __construct(protected studentService $studentService)
    {
    }

    // ==================== Page Rendering Methods ====================

    /**
     * Display the page to add a new student.
     *
     * @return \Illuminate\View\View
     */
    public function addStudentPage()
    {
        return view('dashboard.student.add');
    }

    /**
     * Display the page to show details of a specific student.
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showStudent(Request $request)
    {
        $request->validate([
            'student_id' => 'required|integer',
        ]);

        try {
            $result = $this->studentService->getStudent($request);
            return view('dashboard.student.show', ['student' => $result['data'], 'message' => $result['message']]);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Display the page to list all students.
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showAllStudents(Request $request)
    {
        try {
            $paginatedStudents = $this->studentService->getAllStudents($request);
            return view('dashboard.student.list', ['students' => $paginatedStudents]);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Display the page to edit a specific student.
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function editStudentPage(Request $request)
    {
        $request->validate([
            'student_id' => 'required|integer',
        ]);

        try {
            $result = $this->studentService->getStudent($request);
            return view('dashboard.student.edit', ['student' => $result['data'], 'message' => $result['message']]);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    // ==================== CRUD Operations ====================

    /**
     * Add a new student.
     *
     * @param studentCreationRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addStudent(studentCreationRequest $request)
    {
        try {
            $result = $this->studentService->addStudent($request);
            return redirect()->route('showStudent', ['student_id' => $result['data']])->with(["success" => $result['message']]);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Update an existing student.
     *
     * @param studentUpdateRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function editStudent(studentUpdateRequest $request)
    {
        try {
            $result = $this->studentService->editStudent($request);
            return redirect()->route('showStudent', ['student_id' => $result['data']])->with(["success" => $result['message']]);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Delete a specific student.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteStudent(Request $request)
    {
        try {
            $result = $this->studentService->deleteStudent($request);
            return redirect()->route('showAllStudents')->with(["success" => $result['message']]);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    // ==================== Additional Functionality ====================

    /**
     * Enroll a student in a course.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function enrollInCourse(Request $request)
    {
        try {
            $result = $this->studentService->enrollInCourse($request);
            return redirect()->route('studentCourses')->with(["success" => $result['message']]);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
