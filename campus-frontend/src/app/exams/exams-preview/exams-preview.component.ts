import { Component, computed, inject, OnInit, signal } from '@angular/core';
import { ExamsService, ExamStatistics } from '../exams.service';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { DatePipe, TitleCasePipe, DecimalPipe, NgClass } from '@angular/common';
import { toSignal } from '@angular/core/rxjs-interop';

@Component({
  selector: 'app-exams-preview',
  imports: [RouterLink, DatePipe, TitleCasePipe, DecimalPipe, NgClass],
  templateUrl: './exams-preview.component.html',
  styleUrl: './exams-preview.component.scss',
})
export class ExamsPreview implements OnInit {
  examsService = inject(ExamsService);
  activatedRoute = inject(ActivatedRoute);
  router = inject(Router);

  exam = toSignal(
    this.examsService.getExam(
      Number(this.activatedRoute.snapshot.params['id'])
    )
  );

  // Statistics
  statistics = signal<ExamStatistics | null>(null);
  loadingStatistics = signal<boolean>(false);

  // Active tab
  activeTab = signal<'results' | 'statistics'>('results');

  ngOnInit() {
    this.loadStatistics();
  }

  loadStatistics() {
    const examId = Number(this.activatedRoute.snapshot.params['id']);
    this.loadingStatistics.set(true);

    this.examsService.getExamStatistics(examId).subscribe({
      next: (stats) => {
        this.statistics.set(stats);
        this.loadingStatistics.set(false);
      },
      error: (error) => {
        console.error('Error loading statistics:', error);
        this.statistics.set(null);
        this.loadingStatistics.set(false);
      },
    });
  }

  switchTab(tab: 'results' | 'statistics') {
    this.activeTab.set(tab);
  }

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

  getLetterGrade(percentage: number | undefined): string {
    if (percentage === undefined || percentage === null) return 'N/A';
    if (percentage >= 90) return 'A';
    if (percentage >= 80) return 'B';
    if (percentage >= 70) return 'C';
    if (percentage >= 60) return 'D';
    return 'F';
  }
}
