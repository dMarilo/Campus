import { HttpClient } from '@angular/common/http';
import { Injectable, signal } from '@angular/core';
import { environment } from '../../environments/environment';
import { Observable } from 'rxjs';
import { map } from 'rxjs/operators';

export interface Course {
  id: number;
  code: string;
  name: string;
  description: string;
  ects: number;
  department: string;
  level: string;
  mandatory: boolean;
  status: string;
  created_at?: string;
  updated_at?: string;
}

export interface CourseBook {
  id: number;
  book_id: number;
  title: string;
  author: string;
  publisher: string;
  published_year: number;
  edition: string;
  description: string;
  total_copies: number;
  available_copies: number;
  mandatory: boolean;
}

interface CoursesResponse {
  data?: Course[];
}

interface CourseResponse {
  data?: Course;
}

interface CourseBooksResponse {
  data?: CourseBook[];
}

@Injectable({
  providedIn: 'root'
})
export class CoursesService {
  coursesSignal = signal<Course[]>([]);

  constructor(private http: HttpClient) {}

  getCourses() {
    this.http.get<Course[] | CoursesResponse>(`${environment.apiBaseUrl}/courses`).subscribe({
      next: (response: any) => {
        // Handle both direct array response and wrapped response
        const courses = Array.isArray(response) ? response : (response.data || []);
        this.coursesSignal.set(courses);
      },
      error: (error) => {
        console.error('Error fetching courses', error);
        this.coursesSignal.set([]);
      }
    });
  }

  getCourse(id: number): Observable<Course> {
    return this.http.get<Course | CourseResponse>(`${environment.apiBaseUrl}/courses/${id}`)
      .pipe(
        map(response => {
          // Handle both direct object response and wrapped response
          return (response as any).data || response as Course;
        })
      );
  }

  getCourseByCode(code: string): Observable<Course> {
    return this.http.get<Course | CourseResponse>(`${environment.apiBaseUrl}/courses/code/${code}`)
      .pipe(
        map(response => {
          return (response as any).data || response as Course;
        })
      );
  }

  getCoursesByDepartment(department: string): Observable<Course[]> {
    return this.http.get<Course[] | CoursesResponse>(`${environment.apiBaseUrl}/courses/department/${department}`)
      .pipe(
        map(response => {
          return Array.isArray(response) ? response : (response.data || []);
        })
      );
  }

  getActiveCourses(): Observable<Course[]> {
    return this.http.get<Course[] | CoursesResponse>(`${environment.apiBaseUrl}/courses/active`)
      .pipe(
        map(response => {
          return Array.isArray(response) ? response : (response.data || []);
        })
      );
  }

  getCourseBooks(courseId: number): Observable<CourseBook[]> {
    return this.http.get<CourseBooksResponse>(`${environment.apiBaseUrl}/books/course/${courseId}`)
      .pipe(
        map(response => response.data || [])
      );
  }

  createCourse(course: Omit<Course, 'id' | 'created_at' | 'updated_at'>): Observable<Course> {
    return this.http.post<CourseResponse>(`${environment.apiBaseUrl}/courses`, course)
      .pipe(
        map(response => (response as any).data || response as Course)
      );
  }

  updateCourse(id: number, course: Partial<Omit<Course, 'id' | 'created_at' | 'updated_at'>>): Observable<Course> {
    return this.http.put<CourseResponse>(`${environment.apiBaseUrl}/courses/${id}`, course)
      .pipe(
        map(response => (response as any).data || response as Course)
      );
  }

  deleteCourse(id: number): Observable<any> {
    return this.http.delete(`${environment.apiBaseUrl}/courses/${id}`);
  }
}
