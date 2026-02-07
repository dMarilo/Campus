import { Component, computed, inject, signal, effect } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { RouterLink } from '@angular/router';
import { DatePipe, TitleCasePipe, NgClass } from '@angular/common';
import { ExamsService } from '../exams.service';

@Component({
  selector: 'app-exams-table',
  imports: [FormsModule, RouterLink, DatePipe, TitleCasePipe, NgClass],
  templateUrl: './exams-table.component.html',
  styleUrl: './exams-table.component.scss',
})
export class ExamsTable {
  examsService = inject(ExamsService);
  exams = computed(() => this.examsService.examsSignal() || []);
  searchTerm = signal<string>('');
  filterType = '';
  filterStatus = '';
  isLoading = signal<boolean>(true);

  filteredExams = computed(() => {
    let filtered = this.exams();

    if (!filtered || !Array.isArray(filtered)) {
      return [];
    }

    // Filter by search term
    if (this.searchTerm()) {
      const term = this.searchTerm().toLowerCase();
      filtered = filtered.filter(
        (exam) =>
          exam.title?.toLowerCase().includes(term) ||
          exam.type?.toLowerCase().includes(term) ||
          exam.course_class?.course?.name?.toLowerCase().includes(term) ||
          exam.course_class?.course?.code?.toLowerCase().includes(term) ||
          exam.classroom_name?.toLowerCase().includes(term)
      );
    }

    // Filter by type
    if (this.filterType) {
      filtered = filtered.filter((exam) => exam.type === this.filterType);
    }

    // Filter by status
    if (this.filterStatus) {
      filtered = filtered.filter((exam) => exam.status === this.filterStatus);
    }

    return filtered;
  });

  examTypes = computed(() => {
    const types = new Set(
      this.exams()
        .map((e) => e.type)
        .filter(Boolean)
    );
    return Array.from(types).sort();
  });

  constructor() {
    this.examsService.getExams();

    effect(() => {
      const exams = this.exams();
      if (exams) {
        this.isLoading.set(false);
      }
    });
  }

  applyFilters() {}

  formatExamType(type: string): string {
    if (!type) return 'N/A';
    return type.replace('_', ' ').replace(/\b\w/g, (l) => l.toUpperCase());
  }

  getStatusBadgeClass(status: string): string {
    const statusMap: { [key: string]: string } = {
      planned: 'bg-info',
      open: 'bg-success',
      closed: 'bg-warning',
      graded: 'bg-primary',
      canceled: 'bg-danger',
    };
    return statusMap[status] || 'bg-secondary';
  }
}
