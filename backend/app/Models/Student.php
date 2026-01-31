<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class Student extends Model
{
    use HasFactory;
    /*
    |--------------------------------------------------------------------------
    | Constants
    |--------------------------------------------------------------------------
    */
    public const STATUS_ACTIVE    = 'active';
    public const STATUS_GRADUATED = 'graduated';
    public const STATUS_SUSPENDED = 'suspended';
    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */
    protected $fillable = [
        'user_id',
        'email',
        'first_name',
        'last_name',
        'phone',
        'date_of_birth',
        'gender',
        'student_index',
        'code',
        'year_of_study',
        'department',
        'gpa',
        'status',
    ];
    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */
    protected $casts = [
        'date_of_birth' => 'date',
        'gpa' => 'decimal:2',
    ];
    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }
    public function fullName(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
    /*
    |--------------------------------------------------------------------------
    | Retrieval Methods
    |--------------------------------------------------------------------------
    */
    public static function getAllStudents(): Collection
    {
        return self::all();
    }

    public static function getStudentById(int $id): ?self
    {
        return self::find($id);
    }

    public static function getStudentByCode(string $code): ?self
    {
        return self::where('code', $code)->first();
    }

    public static function getStudentsByYearOfStudy(int $year): Collection
    {
        return self::where('year_of_study', $year)->get();
    }

    /*
    |--------------------------------------------------------------------------
    | Admin actions
    |--------------------------------------------------------------------------
    */
    public static function updateByAdmin(int $id, array $data): self
    {
        $student = self::findOrFail($id);
        $student->fill([
            'email'          => $data['email']          ?? $student->email,
            'first_name'     => $data['first_name']     ?? $student->first_name,
            'last_name'      => $data['last_name']      ?? $student->last_name,
            'phone'          => $data['phone']          ?? $student->phone,
            'date_of_birth'  => $data['date_of_birth']  ?? $student->date_of_birth,
            'gender'         => $data['gender']         ?? $student->gender,
            'student_index'  => $data['student_index']  ?? $student->student_index,
            'code'           => $data['code']           ?? $student->code,
            'year_of_study'  => $data['year_of_study']  ?? $student->year_of_study,
            'department'     => $data['department']     ?? $student->department,
            'gpa'            => $data['gpa']            ?? $student->gpa,
            'status'         => $data['status']         ?? $student->status,
        ]);
        $student->save();
        return $student;
    }
}
