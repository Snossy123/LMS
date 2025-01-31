<?php

namespace App\Http\Controllers;

use App\Http\Requests\courseCreationRequest;
use App\Http\Requests\courseUpdateRequest;
use App\Services\courseService;
use App\Services\teacherService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class courseController extends Controller
{
    /**
     * Constructor for dependency injection
     * @param courseService $courseService Service layer for course operations
     * @param teacherService $teacherService Service layer for teacher operations
     */
    public function __construct(
        protected courseService $courseService,
        protected teacherService $teacherService
    ) {}

    // ------------------------------
    // Course Creation Methods
    // ------------------------------

    /**
     * Display course creation form
     * @return \Illuminate\View\View Add course page with teacher list
     */
    public function addCoursePage()
    {
        $teachers = $this->teacherService->getTeachers();
        return view('dashboard.course.add', ["teachers" => $teachers]);
    }

    /**
     * Handle course creation form submission
     * @param courseCreationRequest $request Validated course data
     * @return \Illuminate\Http\RedirectResponse Redirect with success/error message
     */
    public function addCourse(courseCreationRequest $request)
    {
        try {
            $result = $this->courseService->addCourse($request);
            return redirect()->route('showCourse', ['course_id' => $result['data']])
                ->with(["success" => $result['message']]);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    // ------------------------------
    // Course Display Methods
    // ------------------------------

    /**
     * Display single course details
     * @param Request $request Expects course_id parameter
     * @return \Illuminate\View\View Course details page (dashboard or student view)
     */
    public function showCourse(Request $request):RedirectResponse|View
    {
        $request->validate(['course_id' => 'required|integer']);

        try {
            $result = $this->courseService->getCourse($request);

            if (Auth::user()->data['type'] === 'Student') {
                $enrolled = $this->courseService->checkStudentEnroll($request);
                return view('frontend.courses.show', [
                    'course' => $result['data'],
                    'message' => $result['message'],
                    'enrolled' => $enrolled
                ]);
            }

            return view('dashboard.course.show', [
                'course' => $result['data'],
                'message' => $result['message']
            ]);

        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Display paginated list of all courses
     * @param Request $request Pagination parameters
     * @return \Illuminate\View\View Course list page (dashboard or student view)
     */
    public function showAllCourses(Request $request):RedirectResponse|View
    {
        try {
            $paginatedCourses = $this->courseService->getAllCourses($request);
            $view = Auth::user()->data['type'] === 'Student'
                ? 'frontend.courses.list'
                : 'dashboard.course.list';

            return view($view, ['courses' => $paginatedCourses]);

        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    // ------------------------------
    // Course Modification Methods
    // ------------------------------

    /**
     * Display course edit form
     * @param Request $request Expects course_id parameter
     * @return \Illuminate\View\View Edit course page with current data
     */
    public function editCoursePage(Request $request):RedirectResponse|View
    {
        $request->validate(['course_id' => 'required|integer']);

        try {
            $teachers = $this->teacherService->getTeachers();
            $result = $this->courseService->getCourse($request);

            return view('dashboard.course.edit', [
                'course' => $result['data'],
                'message' => $result['message'],
                "teachers" => $teachers
            ]);

        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Handle course update form submission
     * @param courseUpdateRequest $request Validated course data
     * @return \Illuminate\Http\RedirectResponse Redirect with success/error message
     */
    public function editCourse(courseUpdateRequest $request)
    {
        try {
            $result = $this->courseService->editCourse($request);
            return redirect()->route('showCourse', ['course_id' => $result['data']])
                ->with(["success" => $result['message']]);

        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Delete a course
     * @param Request $request Expects course_id parameter
     * @return \Illuminate\Http\RedirectResponse Redirect with success/error message
     */
    public function deleteCourse(Request $request)
    {
        try {
            $result = $this->courseService->deleteCourse($request);
            return redirect()->route('showAllCourses')
                ->with(["success" => $result['message']]);

        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    // ------------------------------
    // Student-Specific Methods
    // ------------------------------

    /**
     * Display courses enrolled by current student
     * @param Request $request Pagination parameters
     * @return \Illuminate\View\View Student's course list view
     */
    public function studentCourses(Request $request):RedirectResponse|View
    {
        try {
            $paginatedCourses = $this->courseService->studentCourses($request);
            return view('frontend.courses.list', [
                'courses' => $paginatedCourses,
                'myCourses' => true
            ]);

        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
