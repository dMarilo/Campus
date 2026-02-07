import { Component, inject } from '@angular/core';
import { ProfileService, StudentProfile, ProfessorProfile } from '../profile.service';
import { RouterLink } from '@angular/router';
import { DatePipe, TitleCasePipe, NgClass } from '@angular/common';
import { toSignal } from '@angular/core/rxjs-interop';

@Component({
  selector: 'app-profile-preview',
  imports: [RouterLink, DatePipe, TitleCasePipe, NgClass],
  templateUrl: './profile-preview.component.html',
  styleUrl: './profile-preview.component.scss',
  standalone: true
})
export class ProfilePreview {
  profileService = inject(ProfileService);

  // Convert Observable to Signal
  profile = toSignal(this.profileService.getProfile());

  // Helper methods for template
  isStudent(): boolean {
    return this.profile()?.type === 'student';
  }

  isProfessor(): boolean {
    return this.profile()?.type === 'professor';
  }

  isAdmin(): boolean {
    return this.profile()?.type === 'admin';
  }

  // Type-safe getters for profiles
  get studentProfile(): StudentProfile | undefined {
    const prof = this.profile();
    return (prof?.type === 'student' ? prof.profile as StudentProfile : undefined);
  }

  get professorProfile(): ProfessorProfile | undefined {
    const prof = this.profile();
    return (prof?.type === 'professor' ? prof.profile as ProfessorProfile : undefined);
  }

  getStatusBadgeClass(status: string): string {
    const statusMap: { [key: string]: string } = {
      'active': 'bg-success',
      'pending': 'bg-warning',
      'inactive': 'bg-secondary',
      'blocked': 'bg-danger'
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
