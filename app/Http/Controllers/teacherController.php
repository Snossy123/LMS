<?php

namespace App\Http\Controllers;

use App\Http\Requests\teacherCreationRequest;
use App\Http\Requests\teacherUpdateRequest;
use App\Services\teacherService;
use Illuminate\Http\Request;

class teacherController extends Controller
{
    public function __construct(protected teacherService $teacherService)
    {
    }
    public function addTeacherPage()
    {
        return view('dashboard.teacher.add');
    }

    public function addTeacher(teacherCreationRequest $request)
    {
        try{
            $result = $this->teacherService->addTeacher($request);
            return redirect()->route('showTeacher', ['teacher_id'=>$result['data']])->with(["success"=>$result['message']]);
        }catch(\Exception $e){
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function showTeacher(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required|integer',
        ]);
        try{
            $result = $this->teacherService->getTeacher($request);

            return view('dashboard.teacher.show', ['teacher' => $result['data'], 'message' => $result['message']]);
        }catch(\Exception $e){
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function showAllTeachers(Request $request)
    {
        try{
            $paginatedTeachers = $this->teacherService->getAllTeachers($request);
            return view('dashboard.teacher.list',['teachers' => $paginatedTeachers]);
        }catch(\Exception $e){
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function editTeacherPage(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required|integer',
        ]);

        try{
            $result = $this->teacherService->getTeacher($request);
            return view('dashboard.teacher.edit', ['teacher' => $result['data'], 'message' => $result['message']]);
        }catch(\Exception $e){
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function editTeacher(teacherUpdateRequest $request)
    {
        try{
            $result = $this->teacherService->editTeacher($request);
            return redirect()->route('showTeacher', ['teacher_id'=>$result['data']])->with(["success"=>$result['message']]);
        }catch(\Exception $e){
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function deleteTeacher(Request $request)
    {
        try{
            $result = $this->teacherService->deleteTeacher($request);
            return redirect()->route('showAllTeachers')->with(["success"=>$result['message']]);
        }catch(\Exception $e){
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
