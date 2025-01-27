<?php

namespace App\Services;

use App\Http\Requests\courseCreationRequest;
use App\Http\Requests\courseUpdateRequest;
use App\Interfaces\courseRepositoryInterface;
use Illuminate\Http\Request;

class courseService
{
    public function __construct(protected courseRepositoryInterface $courseRepoInterface)
    {
    }


    public function addCourse(courseCreationRequest $request):array
    {
        try {
            $courseId = $this->courseRepoInterface->addCourse($request);
            return [
                'message' => 'Course added successfully',
                'data' => $courseId
            ];
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function getCourse(Request $request):array
    {
        try {
            $course = $this->courseRepoInterface->getCourse($request->course_id);
            return [
                'message' => 'Course retrived successfully',
                'data' => $course
            ];
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function getAllCourses()
    {

        try {
            $courses = $this->courseRepoInterface->getAllCourses();
            return [
                'message' => 'Courses retrived successfully',
                'data' => $courses
            ];
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }


    public function editCourse(courseUpdateRequest $request):array
    {
        try {
            $courseId = $this->courseRepoInterface->editCourse($request);
            return [
                'message' => 'Course Updated Successfully',
                'data' => $courseId
            ];
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
