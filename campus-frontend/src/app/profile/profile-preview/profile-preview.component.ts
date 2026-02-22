import { Component, computed, effect, inject, signal } from '@angular/core';
import { ProfileService, UserProfile, StudentProfile, ProfessorProfile } from '../profile.service';
import { StudentsService, ExamResult } from '../../students/students.service';
import { RouterLink } from '@angular/router';
import { DatePipe, TitleCasePipe, NgClass } from '@angular/common';
import { FormsModule } from '@angular/forms';

@Component({
  selector: 'app-profile-preview',
  imports: [RouterLink, DatePipe, TitleCasePipe, NgClass, FormsModule],
  templateUrl: './profile-preview.component.html',
  styleUrl: './profile-preview.component.scss',
  standalone: true
})
export class ProfilePreview {
  profileService = inject(ProfileService);
  studentsService = inject(StudentsService);

  profile = signal<UserProfile | undefined>(undefined);

  examResults = signal<ExamResult[]>([]);
  loadingExamResults = signal<boolean>(false);

  isEditing = signal(false);
  isSaving = signal(false);
  saveError = signal<string | null>(null);
  saveSuccess = signal(false);

  // Plain mutable objects — compatible with [(ngModel)]
  studentForm = { first_name: '', last_name: '', phone: '', date_of_birth: '', gender: '' };
  professorForm = { first_name: '', last_name: '', phone: '', office_location: '', office_hours: '' };

  passedExams = computed(() => this.examResults().filter(r => r.passed).length);
  failedExams = computed(() => this.examResults().filter(r => !r.passed).length);

  constructor() {
    this.loadProfile();

    effect(() => {
      const prof = this.profile();
      if (prof?.type === 'student' && prof.profile && this.examResults().length === 0 && !this.loadingExamResults()) {
        const studentId = (prof.profile as StudentProfile).id;
        this.loadingExamResults.set(true);
        this.studentsService.getStudentExamResults(studentId).subscribe({
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
    });
  }

  private loadProfile(): void {
    this.profileService.getProfile().subscribe(data => this.profile.set(data));
  }

  openEditForm(): void {
    const sp = this.studentProfile;
    const pp = this.professorProfile;

    if (sp) {
      this.studentForm.first_name = sp.first_name ?? '';
      this.studentForm.last_name = sp.last_name ?? '';
      this.studentForm.phone = sp.phone ?? '';
      this.studentForm.date_of_birth = sp.date_of_birth ? sp.date_of_birth.split('T')[0] : '';
      this.studentForm.gender = sp.gender ?? '';
    }

    if (pp) {
      this.professorForm.first_name = pp.first_name ?? '';
      this.professorForm.last_name = pp.last_name ?? '';
      this.professorForm.phone = pp.phone ?? '';
      this.professorForm.office_location = pp.office_location ?? '';
      this.professorForm.office_hours = pp.office_hours ?? '';
    }

    this.saveError.set(null);
    this.saveSuccess.set(false);
    this.isEditing.set(true);
  }

  closeEditForm(): void {
    this.isEditing.set(false);
    this.saveError.set(null);
  }

  saveProfile(): void {
    this.isSaving.set(true);
    this.saveError.set(null);
    this.saveSuccess.set(false);

    const payload = this.studentProfile
      ? this.buildStudentPayload()
      : this.buildProfessorPayload();

    this.profileService.updateProfile(payload).subscribe({
      next: (updatedSubProfile) => {
        this.isSaving.set(false);
        this.saveSuccess.set(true);

        const current = this.profile();
        if (current) {
          this.profile.set({ ...current, profile: updatedSubProfile });
        }

        setTimeout(() => {
          this.isEditing.set(false);
          this.saveSuccess.set(false);
        }, 800);
      },
      error: (err) => {
        this.isSaving.set(false);
        const message = err?.error?.message || 'Failed to save profile. Please try again.';
        this.saveError.set(message);
      }
    });
  }

  private buildStudentPayload(): Partial<StudentProfile> {
    return {
      first_name: this.studentForm.first_name,
      last_name: this.studentForm.last_name,
      phone: this.studentForm.phone || undefined,
      date_of_birth: this.studentForm.date_of_birth || undefined,
      gender: this.studentForm.gender || undefined,
    } as Partial<StudentProfile>;
  }

  private buildProfessorPayload(): Partial<ProfessorProfile> {
    return {
      first_name: this.professorForm.first_name,
      last_name: this.professorForm.last_name,
      phone: this.professorForm.phone || undefined,
      office_location: this.professorForm.office_location || undefined,
      office_hours: this.professorForm.office_hours || undefined,
    } as Partial<ProfessorProfile>;
  }

  isStudent(): boolean {
    return this.profile()?.type === 'student';
  }

  isProfessor(): boolean {
    return this.profile()?.type === 'professor';
  }

  isAdmin(): boolean {
    return this.profile()?.type === 'admin';
  }

  get studentProfile(): StudentProfile | undefined {
    const prof = this.profile();
    return prof?.type === 'student' ? prof.profile as StudentProfile : undefined;
  }

  get professorProfile(): ProfessorProfile | undefined {
    const prof = this.profile();
    return prof?.type === 'professor' ? prof.profile as ProfessorProfile : undefined;
  }

  getProfileAvatar(): string {
    const type = this.profile()?.type;
    const avatarMap: Record<string, string> = {
      student: 'assets/images/student.jpg',
      professor: 'assets/images/profesor.jpg',
      admin: 'assets/images/admin.jpg',
    };
    return avatarMap[type || ''] || 'assets/images/student.jpg';
  }

  getStatusBadgeClass(status: string): string {
    const statusMap: { [key: string]: string } = {
      active: 'bg-success',
      pending: 'bg-warning',
      inactive: 'bg-secondary',
      blocked: 'bg-danger',
    };
    return statusMap[status] || 'bg-secondary';
  }

  formatEmploymentType(type: string | null | undefined): string {
    if (!type) return 'N/A';
    return type.split('_').map(word =>
      word.charAt(0).toUpperCase() + word.slice(1)
    ).join(' ');
  }
}
