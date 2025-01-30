<?php

namespace App\Services;

use App\Http\Requests\studentCreationRequest;
use App\Http\Requests\studentUpdateRequest;
use App\Interfaces\studentRepositoryInterface;
use Illuminate\Http\Request;

class studentService
{
    public function __construct(protected studentRepositoryInterface $studentRepoInterface)
    {
    }


    public function addStudent(studentCreationRequest $request):array
    {
        try {
            $studentId = $this->studentRepoInterface->addStudent($request);
            return [
                'message' => 'Student added successfully',
                'data' => $studentId
            ];
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function getStudent(Request $request):array
    {
        try {
            $student = $this->studentRepoInterface->getStudent($request->student_id);
            return [
                'message' => 'Student retrived successfully',
                'data' => $student
            ];
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function getAllStudents(Request $request)
    {
        try {
            $students = $this->studentRepoInterface->index($request);
            return $students;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }


    public function editStudent(studentUpdateRequest $request):array
    {
        try {
            $studentId = $this->studentRepoInterface->editStudent($request);
            return [
                'message' => 'Student Updated Successfully',
                'data' => $studentId
            ];
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function deleteStudent(Request $request)
    {
        try {
            $this->studentRepoInterface->deleteStudent($request);
            return [
                'message' => 'Student Deleted Successfully',
            ];
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function enrollInCourse(Request $request)
    {
        try {
            $this->studentRepoInterface->enrollInCourse($request);
            return [
                'message' => 'Course Enroll Successfully',
            ];
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
