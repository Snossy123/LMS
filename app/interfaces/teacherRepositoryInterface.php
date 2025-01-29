<?php

namespace App\Interfaces;

use App\Http\Requests\teacherCreationRequest;
use App\Http\Requests\teacherUpdateRequest;
use Illuminate\Http\Request;

interface teacherRepositoryInterface
{
    public function addTeacher(teacherCreationRequest $request):int;

    public function getTeacher(int $teacherId);
    public function index(Request $request);
    public function editTeacher(teacherUpdateRequest $request);
    public function deleteTeacher(Request $request);
}
