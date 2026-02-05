import { Component, computed, inject, signal, effect } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { RouterLink } from '@angular/router';
import { CoursesService } from '../courses.service';

@Component({
  selector: 'app-courses-table',
  imports: [FormsModule, RouterLink],
  templateUrl: './courses-table.component.html',
  styleUrl: './courses-table.component.scss',
})
export class CoursesTable {
  coursesService = inject(CoursesService);
  courses = computed(() => this.coursesService.coursesSignal() || []);
  searchTerm = signal<string>('');
  filterDepartment = '';
  filterLevel = '';
  isLoading = signal<boolean>(true);
  viewMode = signal<'table' | 'cards'>('table'); // New: view mode toggle

  filteredCourses = computed(() => {
    let filtered = this.courses();

    // Safety check
    if (!filtered || !Array.isArray(filtered)) {
      return [];
    }

    // Filter by search term
    if (this.searchTerm()) {
      const term = this.searchTerm().toLowerCase();
      filtered = filtered.filter(
        (course) =>
          course.name.toLowerCase().includes(term) ||
          course.code.toLowerCase().includes(term) ||
          course.description.toLowerCase().includes(term)
      );
    }

    // Filter by department
    if (this.filterDepartment) {
      filtered = filtered.filter(
        (course) => course.department === this.filterDepartment
      );
    }

    // Filter by level
    if (this.filterLevel) {
      filtered = filtered.filter(
        (course) => course.level === this.filterLevel
      );
    }

    return filtered;
  });

  constructor() {
    this.coursesService.getCourses();

    // Watch for when courses are loaded
    effect(() => {
      const courses = this.courses();
      if (courses && courses.length > 0) {
        this.isLoading.set(false);
      } else if (courses && courses.length === 0) {
        // Data loaded but empty
        this.isLoading.set(false);
      }
    });
  }

  applyFilters() {
    // Triggers re-computation of filteredCourses
  }

  toggleViewMode(mode: 'table' | 'cards') {
    this.viewMode.set(mode);
  }
}
