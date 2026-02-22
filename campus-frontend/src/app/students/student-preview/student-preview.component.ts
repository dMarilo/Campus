import { Component, computed, inject, OnInit, signal } from '@angular/core';
import { StudentsService, Student, Borrowing, ExamResult } from '../students.service';
import { AuthService } from '../../auth/auth.service';
import { ActivatedRoute, RouterLink } from '@angular/router';
import { DatePipe, TitleCasePipe } from '@angular/common';
import { FormsModule } from '@angular/forms';

@Component({
  selector: 'app-student-preview',
  imports: [RouterLink, DatePipe, TitleCasePipe, FormsModule],
  templateUrl: './student-preview.component.html',
  styleUrl: './student-preview.component.scss',
})
export class StudentPreview implements OnInit {
  studentsService = inject(StudentsService);
  authService = inject(AuthService);
  activatedRoute = inject(ActivatedRoute);

  student = signal<Student | undefined>(undefined);

  borrowings = signal<Borrowing[]>([]);
  borrowingType = signal<'all' | 'current'>('current');
  loadingBorrowings = signal<boolean>(false);

  examResults = signal<ExamResult[]>([]);
  loadingExamResults = signal<boolean>(false);

  isEditing = signal(false);
  isSaving = signal(false);
  saveError = signal<string | null>(null);
  saveSuccess = signal(false);

  // Plain object for [(ngModel)] two-way binding
  editForm = {
    first_name: '',
    last_name: '',
    email: '',
    phone: '',
    date_of_birth: '',
    gender: '',
    student_index: '',
    code: '',
    year_of_study: 1,
    department: '',
    gpa: '',
    status: '',
  };

  currentBorrowings = computed(() =>
    this.borrowings().filter(b => b.status === 'borrowed')
  );

  passedExams = computed(() => this.examResults().filter(r => r.passed).length);
  failedExams = computed(() => this.examResults().filter(r => !r.passed).length);

  Math = Math;

  get isAdmin(): boolean {
    return this.authService.isAdmin();
  }

  private get studentId(): number {
    return Number(this.activatedRoute.snapshot.params['id']);
  }

  ngOnInit() {
    this.studentsService.getStudent(this.studentId).subscribe(s => this.student.set(s));
    this.loadBorrowings();
    this.loadExamResults();
  }

  loadBorrowings() {
    this.loadingBorrowings.set(true);
    this.studentsService.getStudentBorrowings(this.studentId, this.borrowingType()).subscribe({
      next: (borrowings) => {
        this.borrowings.set(borrowings);
        this.loadingBorrowings.set(false);
      },
      error: () => {
        this.borrowings.set([]);
        this.loadingBorrowings.set(false);
      }
    });
  }

  switchBorrowingType(type: 'all' | 'current') {
    this.borrowingType.set(type);
    this.loadBorrowings();
  }

  loadExamResults() {
    this.loadingExamResults.set(true);
    this.studentsService.getStudentExamResults(this.studentId).subscribe({
      next: (results) => {
        this.examResults.set(results);
        this.loadingExamResults.set(false);
      },
      error: () => {
        this.examResults.set([]);
        this.loadingExamResults.set(false);
      }
    });
  }

  openEditForm(): void {
    const s = this.student();
    if (!s) return;
    this.editForm.first_name = s.first_name ?? '';
    this.editForm.last_name = s.last_name ?? '';
    this.editForm.email = s.email ?? '';
    this.editForm.phone = s.phone ?? '';
    this.editForm.date_of_birth = s.date_of_birth ? s.date_of_birth.split('T')[0] : '';
    this.editForm.gender = s.gender ?? '';
    this.editForm.student_index = s.student_index ?? '';
    this.editForm.code = s.code ?? '';
    this.editForm.year_of_study = s.year_of_study ?? 1;
    this.editForm.department = s.department ?? '';
    this.editForm.gpa = s.gpa ?? '';
    this.editForm.status = s.status ?? '';
    this.saveError.set(null);
    this.saveSuccess.set(false);
    this.isEditing.set(true);
  }

  closeEditForm(): void {
    this.isEditing.set(false);
    this.saveError.set(null);
  }

  saveStudent(): void {
    this.isSaving.set(true);
    this.saveError.set(null);
    this.saveSuccess.set(false);

    const payload: Partial<Student> = {
      first_name: this.editForm.first_name,
      last_name: this.editForm.last_name,
      email: this.editForm.email || undefined,
      phone: this.editForm.phone || undefined,
      date_of_birth: this.editForm.date_of_birth || undefined,
      gender: this.editForm.gender || undefined,
      student_index: this.editForm.student_index || undefined,
      code: this.editForm.code || undefined,
      year_of_study: this.editForm.year_of_study,
      department: this.editForm.department || undefined,
      gpa: this.editForm.gpa || undefined,
      status: this.editForm.status || undefined,
    };

    this.studentsService.updateStudent(this.studentId, payload).subscribe({
      next: (updated) => {
        this.isSaving.set(false);
        this.saveSuccess.set(true);
        this.student.set(updated);
        setTimeout(() => {
          this.isEditing.set(false);
          this.saveSuccess.set(false);
        }, 800);
      },
      error: (err) => {
        this.isSaving.set(false);
        this.saveError.set(err?.error?.message || 'Failed to save. Please try again.');
      }
    });
  }

  isOverdue(dueDate: string): boolean {
    return new Date(dueDate) < new Date() && this.borrowingType() === 'current';
  }

  getDaysUntilDue(dueDate: string): number {
    const due = new Date(dueDate);
    const now = new Date();
    const diffTime = due.getTime() - now.getTime();
    return Math.ceil(diffTime / (1000 * 60 * 60 * 24));
  }
}
