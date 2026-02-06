import { Component, computed, inject, signal, effect } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { RouterLink } from '@angular/router';
import { ProfessorsService } from '../professors.service';

@Component({
  selector: 'app-professors-table',
  imports: [FormsModule, RouterLink],
  templateUrl: './professors-table.component.html',
  styleUrl: './professors-table.component.scss',
})
export class ProfessorsTable {
  professorsService = inject(ProfessorsService);
  professors = computed(() => this.professorsService.professorsSignal());
  searchTerm = signal<string>('');
  filterDepartment = '';
  filterEmployment = '';
  isLoading = signal<boolean>(true);

  filteredProfessors = computed(() => {
    let filtered = this.professors();

    // Filter by search term
    if (this.searchTerm()) {
      const term = this.searchTerm().toLowerCase();
      filtered = filtered.filter(
        (professor) =>
          professor.first_name.toLowerCase().includes(term) ||
          professor.last_name.toLowerCase().includes(term) ||
          professor.email.toLowerCase().includes(term) ||
          professor.code.toLowerCase().includes(term)
      );
    }

    // Filter by department
    if (this.filterDepartment) {
      filtered = filtered.filter(
        (professor) => professor.department === this.filterDepartment
      );
    }

    // Filter by employment type
    if (this.filterEmployment) {
      filtered = filtered.filter(
        (professor) => professor.employment_type === this.filterEmployment
      );
    }

    return filtered;
  });

  departments = computed(() => {
    const depts = new Set(this.professors().map(p => p.department));
    return Array.from(depts).sort();
  });

  constructor() {
    this.professorsService.getProfessors();

    effect(() => {
      if (this.professors().length > 0) {
        this.isLoading.set(false);
      }
    });
  }

  applyFilters() {
    // Triggers re-computation of filteredProfessors
  }

  formatEmploymentType(type: string): string {
      if (!type) return 'N/A';
      return type.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
  }
}
