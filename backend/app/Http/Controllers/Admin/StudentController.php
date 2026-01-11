<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    public function update(Request $request, int $id)
    {
        $validated = $request->validate([
            'email'         => ['nullable', 'email'],
            'first_name'    => ['nullable', 'string', 'max:100'],
            'last_name'     => ['nullable', 'string', 'max:100'],
            'phone'         => ['nullable', 'string', 'max:30'],
            'date_of_birth' => ['nullable', 'date'],
            'gender'        => ['nullable', 'string', 'in:male,female,other'],
            'student_index' => ['nullable', 'string', 'max:50'],
            'code'          => ['nullable', 'string', 'max:50'],
            'year_of_study' => ['nullable', 'integer', 'min:1', 'max:10'],
            'department'    => ['nullable', 'string', 'max:100'],
            'gpa'           => ['nullable', 'numeric', 'min:0', 'max:10'],
            'status'        => [
                'nullable',
                Rule::in([
                    Student::STATUS_ACTIVE,
                    Student::STATUS_GRADUATED,
                    Student::STATUS_SUSPENDED,
                ]),
            ],
        ]);

        $student = Student::updateByAdmin($id, $validated);

        return response()->json([
            'data' => $student,
        ]);
    }
}
