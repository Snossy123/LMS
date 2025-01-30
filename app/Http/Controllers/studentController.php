<?php

namespace App\Http\Controllers;

use App\Http\Requests\studentCreationRequest;
use App\Http\Requests\studentUpdateRequest;
use App\Services\studentService;
use Illuminate\Http\Request;

class studentController extends Controller
{
    public function __construct(protected studentService $studentService)
    {
    }
    public function addStudentPage()
    {
        return view('dashboard.student.add');
    }

    public function addStudent(studentCreationRequest $request)
    {
        try{
            $result = $this->studentService->addStudent($request);
            return redirect()->route('showStudent', ['student_id'=>$result['data']])->with(["success"=>$result['message']]);
        }catch(\Exception $e){
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function showStudent(Request $request)
    {
        $request->validate([
            'student_id' => 'required|integer',
        ]);
        try{
            $result = $this->studentService->getStudent($request);

            return view('dashboard.student.show', ['student' => $result['data'], 'message' => $result['message']]);
        }catch(\Exception $e){
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function showAllStudents(Request $request)
    {
        try{
            $paginatedStudents = $this->studentService->getAllStudents($request);
            return view('dashboard.student.list',['students' => $paginatedStudents]);
        }catch(\Exception $e){
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function editStudentPage(Request $request)
    {
        $request->validate([
            'student_id' => 'required|integer',
        ]);

        try{
            $result = $this->studentService->getStudent($request);
            return view('dashboard.student.edit', ['student' => $result['data'], 'message' => $result['message']]);
        }catch(\Exception $e){
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function editStudent(studentUpdateRequest $request)
    {
        try{
            $result = $this->studentService->editStudent($request);
            return redirect()->route('showStudent', ['student_id'=>$result['data']])->with(["success"=>$result['message']]);
        }catch(\Exception $e){
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function deleteStudent(Request $request)
    {
        try{
            $result = $this->studentService->deleteStudent($request);
            return redirect()->route('showAllStudents')->with(["success"=>$result['message']]);
        }catch(\Exception $e){
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function enrollInCourse(Request $request)
    {
        try{
            $result = $this->studentService->enrollInCourse($request);
            return redirect()->route('studentCourses')->with(["success"=>$result['message']]);
        }catch(\Exception $e){
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
