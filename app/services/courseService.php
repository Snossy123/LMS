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

    public function getAllCourses(Request $request)
    {
        try {
            $courses = $this->courseRepoInterface->index($request);
            return $courses;
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

    public function deleteCourse(Request $request)
    {
        try {
            $this->courseRepoInterface->deleteCourse($request);
            return [
                'message' => 'Course Deleted Successfully',
            ];
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function studentCourses(Request $request)
    {
        try {
            $courses = $this->courseRepoInterface->studentCourses($request);
            return $courses;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
