import { Component, computed, inject, signal, effect } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { RouterLink } from '@angular/router';
import { StudentsService } from '../students.service';

@Component({
  selector: 'app-students-table',
  imports: [FormsModule, RouterLink],
  templateUrl: './students-table.component.html',
  styleUrl: './students-table.component.scss',
})
export class StudentsTable {
  studentsService = inject(StudentsService);
  students = computed(() => this.studentsService.studentsSignal());
  searchTerm = signal<string>('');
  filterYear = '';
  isLoading = signal<boolean>(true);

  filteredStudents = computed(() => {
    let filtered = this.students();

    // Filter by search term
    if (this.searchTerm()) {
      const term = this.searchTerm().toLowerCase();
      filtered = filtered.filter(
        (student) =>
          student.first_name.toLowerCase().includes(term) ||
          student.last_name.toLowerCase().includes(term) ||
          student.email.toLowerCase().includes(term) ||
          student.index_number.toLowerCase().includes(term)
      );
    }

    // Filter by year
    if (this.filterYear) {
      filtered = filtered.filter(
        (student) => student.year_of_study === parseInt(this.filterYear)
      );
    }

    return filtered;
  });

  constructor() {
    this.studentsService.getStudents();

    // Watch for when students are loaded
    effect(() => {
      if (this.students().length > 0) {
        this.isLoading.set(false);
      }
    });
  }

  applyFilters() {
    // Triggers re-computation of filteredStudents
  }
}
