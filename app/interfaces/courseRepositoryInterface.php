<?php

namespace App\Interfaces;

use App\Http\Requests\courseCreationRequest;
use App\Http\Requests\courseUpdateRequest;
use Illuminate\Http\Request;

interface courseRepositoryInterface
{
    public function addCourse(courseCreationRequest $request):int;
    public function getCourse(int $courseId);
    public function index(Request $request);
    public function editCourse(courseUpdateRequest $request);
    public function deleteCourse(Request $request);
    public function studentCourses(Request $request);
}
