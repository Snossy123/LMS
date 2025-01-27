<?php

namespace App\Interfaces;

use App\Http\Requests\courseCreationRequest;
use App\Http\Requests\courseUpdateRequest;

interface courseRepositoryInterface
{
    public function addCourse(courseCreationRequest $request):int;

    public function getCourse(int $courseId);
    public function getAllCourses();
    public function editCourse(courseUpdateRequest $request);
}
