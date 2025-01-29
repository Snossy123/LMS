<?php

namespace App\Interfaces;

use App\Http\Requests\studentCreationRequest;
use App\Http\Requests\studentUpdateRequest;
use Illuminate\Http\Request;

interface studentRepositoryInterface
{
    public function addStudent(studentCreationRequest $request):int;
    public function getStudent(int $studentId);
    public function index(Request $request);
    public function editStudent(studentUpdateRequest $request);
    public function deleteStudent(Request $request);
}
