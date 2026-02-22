import { Component } from '@angular/core';
import { RouterLink } from '@angular/router';

@Component({
  selector: 'app-guide',
  standalone: true,
  imports: [RouterLink],
  templateUrl: './guide.component.html',
  styleUrl: './guide.component.scss',
})
export class GuideComponent {
  sections = [
    { id: 'library',    label: 'Library',       icon: 'fa-book-open',          color: 'success' },
    { id: 'courses',    label: 'Courses',        icon: 'fa-chalkboard-teacher', color: 'primary' },
    { id: 'classes',    label: 'Classes',        icon: 'fa-school',             color: 'info'    },
    { id: 'students',   label: 'Students',       icon: 'fa-user-graduate',      color: 'warning' },
    { id: 'professors', label: 'Professors',     icon: 'fa-user-tie',           color: 'secondary'},
    { id: 'exams',      label: 'Exams',          icon: 'fa-file-alt',           color: 'danger'  },
    { id: 'profile',    label: 'Your Profile',   icon: 'fa-id-card',            color: 'primary' },
  ];

  scrollTo(id: string) {
    document.getElementById(id)?.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }
}
