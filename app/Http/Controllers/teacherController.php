<?php

namespace App\Http\Controllers;

use App\Http\Requests\TeacherCreationRequest;
use App\Http\Requests\TeacherUpdateRequest;
use App\Services\TeacherService;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    /**
     * Constructor to inject the TeacherService dependency.
     *
     * @param TeacherService $teacherService
     */
    public function __construct(protected TeacherService $teacherService)
    {
    }

    // ==================== DASHBOARD RELATED METHODS ====================

    /**
     * Display the teacher dashboard with report data.
     *
     * @return \Illuminate\View\View
     */
    public function showDashboard()
    {
        try {
            $teacherReport = $this->teacherService->reportData();
            return view('dashboard.teacher', ['teacher_report' => $teacherReport]);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    // ==================== TEACHER LISTING METHODS ====================

    /**
     * Display a paginated list of all teachers.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function showAllTeachers(Request $request)
    {
        try {
            $paginatedTeachers = $this->teacherService->getAllTeachers($request);
            return view('dashboard.teacher.list', ['teachers' => $paginatedTeachers]);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    // ==================== TEACHER DETAILS METHODS ====================

    /**
     * Display details of a specific teacher.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function showTeacher(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required|integer',
        ]);

        try {
            $result = $this->teacherService->getTeacher($request);
            return view('dashboard.teacher.show', ['teacher' => $result['data'], 'message' => $result['message']]);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    // ==================== TEACHER CREATION METHODS ====================

    /**
     * Display the form for adding a new teacher.
     *
     * @return \Illuminate\View\View
     */
    public function addTeacherPage()
    {
        return view('dashboard.teacher.add');
    }

    /**
     * Handle the creation of a new teacher.
     *
     * @param TeacherCreationRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addTeacher(TeacherCreationRequest $request)
    {
        try {
            $result = $this->teacherService->addTeacher($request);
            return redirect()->route('showTeacher', ['teacher_id' => $result['data']])->with(["success" => $result['message']]);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    // ==================== TEACHER EDITING METHODS ====================

    /**
     * Display the form for editing an existing teacher.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function editTeacherPage(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required|integer',
        ]);

        try {
            $result = $this->teacherService->getTeacher($request);
            return view('dashboard.teacher.edit', ['teacher' => $result['data'], 'message' => $result['message']]);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Handle the update of an existing teacher.
     *
     * @param TeacherUpdateRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function editTeacher(TeacherUpdateRequest $request)
    {
        try {
            $result = $this->teacherService->editTeacher($request);
            return redirect()->route('showTeacher', ['teacher_id' => $result['data']])->with(["success" => $result['message']]);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    // ==================== TEACHER DELETION METHODS ====================

    /**
     * Handle the deletion of a teacher.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteTeacher(Request $request)
    {
        try {
            $result = $this->teacherService->deleteTeacher($request);
            return redirect()->route('showAllTeachers')->with(["success" => $result['message']]);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
