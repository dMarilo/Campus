import { Component, computed, inject, signal, effect } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { RouterLink } from '@angular/router';
import { ClassesService } from '../classes.service';
import { AuthService } from '../../auth/auth.service';

@Component({
  selector: 'app-classes-table',
  imports: [FormsModule, RouterLink],
  templateUrl: './classes-table.component.html',
  styleUrl: './classes-table.component.scss',
})
export class ClassesTable {
  classesService = inject(ClassesService);
  authService = inject(AuthService);
  user = this.authService.getUser();
  isStudent = this.user?.type === 'student';
  classes = computed(() => this.classesService.classesSignal() || []);
  searchTerm = signal<string>('');
  filterStatus = '';
  filterDepartment = '';
  isLoading = signal<boolean>(true);

  filteredClasses = computed(() => {
    let filtered = this.classes();

    if (!filtered || !Array.isArray(filtered)) {
      return [];
    }

    // Filter by search term
    if (this.searchTerm()) {
      const term = this.searchTerm().toLowerCase();
      filtered = filtered.filter(
        (cls) =>
          cls.course?.name?.toLowerCase().includes(term) ||
          cls.course?.code?.toLowerCase().includes(term) ||
          cls.course?.department?.toLowerCase().includes(term)
      );
    }

    // Filter by status
    if (this.filterStatus) {
      filtered = filtered.filter(
        (cls) => cls.status === this.filterStatus
      );
    }

    // Filter by department
    if (this.filterDepartment) {
      filtered = filtered.filter(
        (cls) => cls.course?.department === this.filterDepartment
      );
    }

    return filtered;
  });

  departments = computed(() => {
    const depts = new Set(
      this.classes()
        .map(c => c.course?.department)
        .filter(Boolean)
    );
    return Array.from(depts).sort();
  });

  constructor() {
    if (this.isStudent && this.user?.profile?.id) {
      this.classesService.getClassesByStudent(this.user.profile.id);
    } else {
      this.classesService.getClasses();
    }

    effect(() => {
      const classes = this.classes();
      if (classes) {
        this.isLoading.set(false);
      }
    });
  }

  applyFilters() {}
}
