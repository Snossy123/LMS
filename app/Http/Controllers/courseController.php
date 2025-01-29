<?php

namespace App\Http\Controllers;

use App\Http\Requests\courseCreationRequest;
use App\Http\Requests\courseUpdateRequest;
use App\Services\courseService;
use App\Services\teacherService;
use Illuminate\Http\Request;

class courseController extends Controller
{
    public function __construct(protected courseService $courseService, protected teacherService $teacherService)
    {
    }
    public function addCoursePage()
    {
        $teachers = $this->teacherService->getTeachers();
        return view('dashboard.course.add', ["teachers" => $teachers]);
    }

    public function addCourse(courseCreationRequest $request)
    {
        try{
            $result = $this->courseService->addCourse($request);
            return redirect()->route('showCourse', ['course_id'=>$result['data']])->with(["success"=>$result['message'], ]);
        }catch(\Exception $e){
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function showCourse(Request $request)
    {
        $request->validate([
            'course_id' => 'required|integer',
        ]);
        try{
            $result = $this->courseService->getCourse($request);

            return view('dashboard.course.show', ['course' => $result['data'], 'message' => $result['message']]);
        }catch(\Exception $e){
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function showAllCourses(Request $request)
    {
        try{
            $paginatedCourses = $this->courseService->getAllCourses($request);
            return view('dashboard.course.list',['courses' => $paginatedCourses]);
        }catch(\Exception $e){
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function editCoursePage(Request $request)
    {
        $request->validate([
            'course_id' => 'required|integer',
        ]);

        try{
            $teachers = $this->teacherService->getTeachers();
            $result = $this->courseService->getCourse($request);
            return view('dashboard.course.edit', ['course' => $result['data'], 'message' => $result['message'], "teachers" => $teachers]);
        }catch(\Exception $e){
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function editCourse(courseUpdateRequest $request)
    {
        try{
            $result = $this->courseService->editCourse($request);
            return redirect()->route('showCourse', ['course_id'=>$result['data']])->with(["success"=>$result['message'], ]);
        }catch(\Exception $e){
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function deleteCourse(Request $request)
    {
        try{
            $result = $this->courseService->deleteCourse($request);
            return redirect()->route('showAllCourses')->with(["success"=>$result['message']]);
        }catch(\Exception $e){
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
