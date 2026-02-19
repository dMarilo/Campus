import { Component, computed, inject, signal, effect } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { RouterLink } from '@angular/router';
import { DatePipe } from '@angular/common';
import { SessionsService } from '../sessions.service';

@Component({
  selector: 'app-sessions-table',
  imports: [FormsModule, RouterLink, DatePipe],
  templateUrl: './sessions-table.component.html',
  styleUrl: './sessions-table.component.scss',
})
export class SessionsTable {
  sessionsService = inject(SessionsService);

  sessions = computed(() => this.sessionsService.sessionsSignal() || []);
  searchTerm = signal<string>('');
  filterStatus = '';
  isLoading = signal<boolean>(true);

  filteredSessions = computed(() => {
    let filtered = this.sessions();

    if (!filtered || !Array.isArray(filtered)) {
      return [];
    }

    if (this.searchTerm()) {
      const term = this.searchTerm().toLowerCase();
      filtered = filtered.filter(
        (s) =>
          s.course_class?.course?.name?.toLowerCase().includes(term) ||
          s.course_class?.course?.code?.toLowerCase().includes(term) ||
          s.classroom?.name?.toLowerCase().includes(term)
      );
    }

    if (this.filterStatus) {
      filtered = filtered.filter((s) => s.status === this.filterStatus);
    }

    return filtered;
  });

  constructor() {
    this.sessionsService.getSessions();

    effect(() => {
      const sessions = this.sessions();
      if (sessions) {
        this.isLoading.set(false);
      }
    });
  }

  applyFilters() {}
}
