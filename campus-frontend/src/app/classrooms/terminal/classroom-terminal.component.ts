import { Component, OnDestroy, signal, computed } from '@angular/core';
import { CommonModule } from '@angular/common';
import {
  FormGroup,
  FormControl,
  Validators,
  ReactiveFormsModule,
} from '@angular/forms';
import { FormsModule } from '@angular/forms';
import { ActivatedRoute } from '@angular/router';

import { ClassroomSessionService } from '../classroom-session.service';

@Component({
  standalone: true,
  selector: 'app-classroom-terminal',
  templateUrl: './classroom-terminal.component.html',
  styleUrls: ['./classroom-terminal.component.scss'],
  imports: [
    CommonModule,
    ReactiveFormsModule,
    FormsModule,
  ],
})
export class ClassroomTerminalComponent implements OnDestroy {

  classroomId!: number;

  loading = signal(false);
  errorMessage = signal<string | null>(null);
  sessionData = signal<any>(null);

  // Clock
  currentTime = signal(new Date());
  elapsedTime = signal('00:00:00');
  private clockInterval: any;
  private elapsedInterval: any;
  private sessionPollInterval: any;

  // Student roster
  studentSearch = signal('');
  students = signal<any[]>([]);

  filteredStudents = computed(() => {
    const term = this.studentSearch()?.toLowerCase() || '';
    if (!term) return this.students();
    return this.students().filter(
      (s: any) =>
        s.first_name?.toLowerCase().includes(term) ||
        s.last_name?.toLowerCase().includes(term) ||
        s.index_number?.toLowerCase().includes(term)
    );
  });

  presentCount = computed(() =>
    this.students().filter((s: any) => s.checked_in).length
  );

  absentCount = computed(() =>
    this.students().length - this.presentCount()
  );

  totalStudents = computed(() =>
    this.students().length
  );

  form = new FormGroup({
    professor_code: new FormControl('', Validators.required),
    class_pin: new FormControl('', Validators.required),
  });

  constructor(
    private route: ActivatedRoute,
    private sessionService: ClassroomSessionService
  ) {
    this.classroomId = Number(this.route.snapshot.paramMap.get('id'));

    // Update clock every second
    this.clockInterval = setInterval(() => {
      this.currentTime.set(new Date());
    }, 1000);
  }

  ngOnDestroy(): void {
    clearInterval(this.clockInterval);
    clearInterval(this.elapsedInterval);
    clearInterval(this.sessionPollInterval);
  }

  startSession(): void {
    if (this.form.invalid || this.loading()) return;

    this.loading.set(true);
    this.errorMessage.set(null);

    const { professor_code, class_pin } = this.form.value;

    this.sessionService
      .startSession(this.classroomId, professor_code!, class_pin!)
      .subscribe({
        next: (res: any) => {
          this.sessionData.set(res.data);
          this.loading.set(false);

          // Populate students if the API returns them
          if (res.data?.students) {
            this.students.set(res.data.students);
          }

          this.startElapsedTimer();
          this.startSessionPolling();
        },
        error: (err) => {
          this.errorMessage.set(
            err?.error?.message ?? 'Session could not be started'
          );
          this.loading.set(false);
        },
      });
  }

  endSession(): void {
    if (!this.sessionData() || this.loading()) return;

    this.loading.set(true);

    this.sessionService.endSession(this.classroomId).subscribe({
      next: () => {
        this.sessionData.set(null);
        this.loading.set(false);
        this.students.set([]);
        this.elapsedTime.set('00:00:00');
        clearInterval(this.elapsedInterval);
        clearInterval(this.sessionPollInterval);
      },
      error: () => {
        this.errorMessage.set('Failed to end session');
        this.loading.set(false);
      },
    });
  }

  private startElapsedTimer(): void {
    clearInterval(this.elapsedInterval);
    const startedAt = new Date(this.sessionData().started_at).getTime();

    this.elapsedInterval = setInterval(() => {
      const diff = Date.now() - startedAt;
      const hrs = Math.floor(diff / 3600000);
      const mins = Math.floor((diff % 3600000) / 60000);
      const secs = Math.floor((diff % 60000) / 1000);
      this.elapsedTime.set(
        String(hrs).padStart(2, '0') + ':' +
        String(mins).padStart(2, '0') + ':' +
        String(secs).padStart(2, '0')
      );
    }, 1000);
  }

  private startSessionPolling(): void {
    clearInterval(this.sessionPollInterval);

    // Poll every 3 seconds to update student check-in status
    this.sessionPollInterval = setInterval(() => {
      this.sessionService.getCurrentSession(this.classroomId).subscribe({
        next: (res: any) => {
          if (res.data && res.data.students) {
            this.students.set(res.data.students);
          }
        },
        error: () => {
          // Session might have ended, stop polling
          clearInterval(this.sessionPollInterval);
        },
      });
    }, 3000);
  }
}
