import { HttpClient } from '@angular/common/http';
import { inject, Injectable, signal } from '@angular/core';
import { environment } from '../../environments/environment';
import { map, Observable, tap } from 'rxjs';

export interface Exam {
  id: number;
  class_id: number;
  type: string;
  title: string | null;
  exam_date: string;
  exam_time: string;
  classroom_name: string | null;
  max_points: number;
  status: string;
  created_at?: string;
  updated_at?: string;
  course_class?: {
    id: number;
    course_id: number;
    semester_id: number;
    academic_year_id: number;
    iteration: number;
    status: string;
    course?: {
      id: number;
      code: string;
      name: string;
      description: string;
      ects: number;
      department: string;
      level: string;
      mandatory: number;
      status: string;
    };
  };
  exam_results?: ExamResult[];
}

export interface ExamResult {
  id: number;
  exam_id: number;
  student_id: number;
  grade: number | null;
  passed: boolean;
  registration_date: string;
  letter_grade?: string;
  percentage?: number;
  student?: {
    id: number;
    student_code: string;
    user?: {
      name: string;
    };
  };
}

export interface ExamStatistics {
  exam_id: number;
  exam_title: string;
  total_students: number;
  graded_students: number;
  ungraded_students: number;
  passed_students: number;
  failed_students: number;
  pass_rate: number;
  average_grade: number;
}

@Injectable({
  providedIn: 'root',
})
export class ExamsService {
  http = inject(HttpClient);

  examsSignal = signal<Exam[] | null>(null);

  getExams(): void {
    this.http
      .get<{ success: boolean; data: Exam[] }>(
        `${environment.apiBaseUrl}/exams`
      )
      .pipe(
        map((response) => response.data),
        tap((exams) => this.examsSignal.set(exams))
      )
      .subscribe({
        error: (err) => {
          console.error('Error fetching exams:', err);
          this.examsSignal.set([]);
        },
      });
  }

  getExam(id: number): Observable<Exam> {
    return this.http
      .get<{ success: boolean; data: Exam }>(
        `${environment.apiBaseUrl}/exams/${id}`
      )
      .pipe(map((response) => response.data));
  }

  getExamsByClass(classId: number): Observable<Exam[]> {
    return this.http
      .get<{ success: boolean; data: Exam[] }>(
        `${environment.apiBaseUrl}/classes/${classId}/exams`
      )
      .pipe(map((response) => response.data));
  }

  getExamsByStudent(studentId: number): Observable<any[]> {
    return this.http
      .get<{ success: boolean; data: any[] }>(
        `${environment.apiBaseUrl}/students/${studentId}/exams`
      )
      .pipe(map((response) => response.data));
  }

  getExamStatistics(examId: number): Observable<ExamStatistics> {
    return this.http
      .get<{ success: boolean; data: ExamStatistics }>(
        `${environment.apiBaseUrl}/exams/${examId}/statistics`
      )
      .pipe(map((response) => response.data));
  }

  createExam(examData: Partial<Exam>): Observable<Exam> {
    return this.http
      .post<{ success: boolean; data: Exam }>(
        `${environment.apiBaseUrl}/exams`,
        examData
      )
      .pipe(map((response) => response.data));
  }

  updateExam(id: number, examData: Partial<Exam>): Observable<Exam> {
    return this.http
      .put<{ success: boolean; data: Exam }>(
        `${environment.apiBaseUrl}/exams/${id}`,
        examData
      )
      .pipe(map((response) => response.data));
  }

  deleteExam(id: number): Observable<void> {
    return this.http
      .delete<void>(`${environment.apiBaseUrl}/exams/${id}`);
  }

  registerStudent(examId: number, studentId: number): Observable<ExamResult> {
    return this.http
      .post<{ success: boolean; data: ExamResult }>(
        `${environment.apiBaseUrl}/exams/register`,
        { exam_id: examId, student_id: studentId }
      )
      .pipe(map((response) => response.data));
  }

  gradeExam(resultId: number, grade: number, passed: boolean): Observable<ExamResult> {
    return this.http
      .put<{ success: boolean; data: ExamResult }>(
        `${environment.apiBaseUrl}/exam-results/${resultId}/grade`,
        { grade, passed }
      )
      .pipe(map((response) => response.data));
  }

  changeExamStatus(examId: number, status: string): Observable<Exam> {
    return this.http
      .patch<{ success: boolean; data: Exam }>(
        `${environment.apiBaseUrl}/exams/${examId}/status`,
        { status }
      )
      .pipe(map((response) => response.data));
  }
}
