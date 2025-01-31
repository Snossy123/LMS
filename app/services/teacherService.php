<?php

namespace App\Services;

use App\Http\Requests\teacherCreationRequest;
use App\Http\Requests\teacherUpdateRequest;
use App\Interfaces\teacherRepositoryInterface;
use Illuminate\Http\Request;

class teacherService
{
    public function __construct(protected teacherRepositoryInterface $teacherRepoInterface)
    {
    }


    public function addTeacher(teacherCreationRequest $request):array
    {
        try {
            $teacherId = $this->teacherRepoInterface->addTeacher($request);
            return [
                'message' => 'Teacher added successfully',
                'data' => $teacherId
            ];
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function getTeacher(Request $request):array
    {
        try {
            $teacher = $this->teacherRepoInterface->getTeacher($request->teacher_id);
            return [
                'message' => 'Teacher retrived successfully',
                'data' => $teacher
            ];
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function getAllTeachers(Request $request)
    {
        try {
            $teachers = $this->teacherRepoInterface->index($request);
            return $teachers;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }


    public function editTeacher(teacherUpdateRequest $request):array
    {
        try {
            $teacherId = $this->teacherRepoInterface->editTeacher($request);
            return [
                'message' => 'Teacher Updated Successfully',
                'data' => $teacherId
            ];
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function deleteTeacher(Request $request)
    {
        try {
            $this->teacherRepoInterface->deleteTeacher($request);
            return [
                'message' => 'Teacher Deleted Successfully',
            ];
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function getTeachers()
    {
        try {
            return $this->teacherRepoInterface->getTeachers();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
    public function reportData()
    {
        try {
            return $this->teacherRepoInterface->reportData();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
