import { HttpClient } from '@angular/common/http';
import { Injectable, signal } from '@angular/core';
import { environment } from '../../environments/environment';
import { Observable } from 'rxjs';
import { map } from 'rxjs/operators';

export interface CourseClass {
  id: number;
  course_id: number;
  semester_id: number;
  academic_year_id: number;
  iteration: number;
  status: string;
  pin: string;
  created_at?: string;
  updated_at?: string;
  course?: {
    id: number;
    code: string;
    name: string;
    description: string;
    ects: number;
    department: string;
    level: string;
    mandatory: boolean;
    status: string;
  };
  semester?: {
    id: number;
    name: string;
  };
  academic_year?: {
    id: number;
    name: string;
  };
}

export interface ClassProfessor {
  id: number;
  code: string;
  first_name: string;
  last_name: string;
  email: string;
  phone: string | null;
  academic_title: string | null;
  department: string;
  employment_type: string;
  status: string;
  office_location: string | null;
  office_hours: string | null;
}

export interface ClassStudent {
  id: number;
  first_name: string;
  last_name: string;
  email: string;
  index_number: string;
  phone: string | null;
  year_of_study: number;
  status: string;
}

interface ClassesResponse {
  data?: CourseClass[];
}

interface ClassResponse {
  data?: CourseClass;
}

interface ProfessorsResponse {
  data?: ClassProfessor[];
}

interface StudentsResponse {
  data?: ClassStudent[];
}

@Injectable({
  providedIn: 'root'
})
export class ClassesService {
  classesSignal = signal<CourseClass[]>([]);

  constructor(private http: HttpClient) {}

  getClasses() {
    this.http.get<ClassesResponse>(`${environment.apiBaseUrl}/classes`).subscribe({
      next: (response: any) => {
        const classes = Array.isArray(response) ? response : (response.data || []);
        this.classesSignal.set(classes);
      },
      error: (error) => {
        console.error('Error fetching classes', error);
        this.classesSignal.set([]);
      }
    });
  }

  getClass(id: number): Observable<CourseClass> {
    return this.http.get<ClassResponse>(`${environment.apiBaseUrl}/classes/${id}`)
      .pipe(
        map(response => {
          return (response as any).data || response as CourseClass;
        })
      );
  }

  getClassProfessors(classId: number): Observable<ClassProfessor[]> {
    return this.http.get<ProfessorsResponse>(`${environment.apiBaseUrl}/classes/${classId}/professors`)
      .pipe(
        map(response => {
          return Array.isArray(response) ? response : (response.data || []);
        })
      );
  }

  getClassStudents(classId: number): Observable<ClassStudent[]> {
    return this.http.get<StudentsResponse>(`${environment.apiBaseUrl}/classes/${classId}/students`)
      .pipe(
        map(response => {
          return Array.isArray(response) ? response : (response.data || []);
        })
      );
  }

  getClassesByStudent(studentId: number) {
    this.http.get<ClassesResponse>(`${environment.apiBaseUrl}/classes/students/${studentId}/classes`).subscribe({
      next: (response: any) => {
        const classes = Array.isArray(response) ? response : (response.data || []);
        this.classesSignal.set(classes);
      },
      error: (error) => {
        console.error('Error fetching student classes', error);
        this.classesSignal.set([]);
      }
    });
  }
}
