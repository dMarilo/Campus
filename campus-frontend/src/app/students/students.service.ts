import { HttpClient } from '@angular/common/http';
import { Injectable, signal } from '@angular/core';
import { environment } from '../../environments/environment';
import { Observable } from 'rxjs';
import { map } from 'rxjs/operators';

export interface Student {
  id: number;
  user_id: number;
  first_name: string;
  last_name: string;
  email: string;
  student_index: string | null;
  code: string | null;
  date_of_birth: string | null;
  gender: string | null;
  phone: string | null;
  year_of_study: number;
  department: string | null;
  gpa: string | null;
  status: string;
  created_at?: string;
  updated_at?: string;
}

interface Book {
  id: number;
  title: string;
  author: string;
  publisher: string;
  published_year: number;
  edition: string;
  description: string;
  total_copies: number;
  available_copies: number;
  created_at: string;
  updated_at: string;
}

interface BookCopy {
  id: number;
  book_id: number;
  isbn: string;
  status: string;
  created_at: string;
  updated_at: string;
  book: Book;
}

export interface Borrowing {
  id: number;
  book_copy_id: number;
  student_id: number;
  borrowed_at: string;
  due_at: string;
  returned_at: string | null;
  status: string;
  created_at: string;
  updated_at: string;
  book_copy: BookCopy;
}

interface StudentsResponse {
  data: Student[];
}

interface StudentResponse {
  data: Student;
}

interface BorrowingsResponse {
  data: Borrowing[];
}

export interface ExamResult {
  id: number;
  exam_id: number;
  exam_title: string;
  exam_type: string;
  class_name: string | null;
  grade: number | null;
  max_points: number | null;
  percentage: number | null;
  letter_grade: string | null;
  passed: boolean;
  registration_date: string | null;
  exam_date: string | null;
}

interface ExamResultsResponse {
  data: ExamResult[];
}

@Injectable({
  providedIn: 'root'
})
export class StudentsService {
  studentsSignal = signal<Student[]>([]);

  constructor(private http: HttpClient) {}

  getStudents() {
    this.http.get<StudentsResponse>(`${environment.apiBaseUrl}/students`).subscribe({
      next: (response: any) => {
        this.studentsSignal.set(response.data);
      },
      error: (error) => {
        console.error('Error fetching students', error);
        this.studentsSignal.set([]);
      }
    });
  }

  getStudent(id: number): Observable<Student> {
    return this.http.get<StudentResponse>(`${environment.apiBaseUrl}/students/${id}`)
      .pipe(map(response => response.data));
  }

  getStudentByCode(code: string): Observable<Student> {
    return this.http.get<StudentResponse>(`${environment.apiBaseUrl}/students/code/${code}`)
      .pipe(map(response => response.data));
  }

  getStudentsByYear(year: number): Observable<Student[]> {
    return this.http.get<StudentsResponse>(`${environment.apiBaseUrl}/students/year/${year}`)
      .pipe(map(response => response.data));
  }

  updateStudent(id: number, data: Partial<Student>): Observable<Student> {
    return this.http.put<StudentResponse>(`${environment.apiBaseUrl}/admin/students/${id}`, data)
      .pipe(map(response => response.data));
  }

  getStudentBorrowings(studentId: number, type: 'all' | 'current' = 'all'): Observable<Borrowing[]> {
    return this.http.post<BorrowingsResponse>(
      `${environment.apiBaseUrl}/borrowings/student`,
      { student_id: studentId, type }
    ).pipe(map(response => response.data));
  }

  getStudentExamResults(studentId: number): Observable<ExamResult[]> {
    return this.http.get<ExamResultsResponse>(
      `${environment.apiBaseUrl}/students/${studentId}/exam-results`
    ).pipe(map(response => response.data));
  }
}
