import { HttpClient } from '@angular/common/http';
import { Injectable, signal } from '@angular/core';
import { environment } from '../../environments/environment';
import { Observable } from 'rxjs';
import { tap, map } from 'rxjs/operators';

interface Student {
  id: number;
  user_id: number;
  first_name: string;
  last_name: string;
  email: string;
  index_number: string;
  date_of_birth: string;
  gender: string;
  phone: string;
  address: string;
  city: string;
  country: string;
  enrollment_date: string;
  year_of_study: number;
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

interface Borrowing {
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

  getStudentBorrowings(studentId: number, type: 'all' | 'current' = 'all'): Observable<Borrowing[]> {
    return this.http.post<BorrowingsResponse>(
      `${environment.apiBaseUrl}/borrowings/student`,
      {
        student_id: studentId,
        type: type
      }
    ).pipe(map(response => response.data));
  }
}
