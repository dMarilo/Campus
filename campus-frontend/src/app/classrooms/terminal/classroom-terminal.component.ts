import { Component, OnInit, OnDestroy, signal, computed } from '@angular/core';
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
export class ClassroomTerminalComponent implements OnInit, OnDestroy {

  classroomId!: number;

  loading = signal(false);
  errorMessage = signal<string | null>(null);
  sessionData = signal<any>(null);

  // Student check-in
  checkInCode = signal('');
  checkInMessage = signal<{ text: string; type: 'success' | 'error' } | null>(null);
  checkInLoading = signal(false);

  // Clock
  currentTime = signal(new Date());
  elapsedTime = signal('00:00:00');
  private clockInterval: any;
  private elapsedInterval: any;
  private sessionPollInterval: any;
  private checkInMessageTimeout: any;

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

    this.clockInterval = setInterval(() => {
      this.currentTime.set(new Date());
    }, 1000);
  }

  ngOnInit(): void {
    // On load/refresh, restore any already-running session without requiring
    // the professor to re-enter their codes.
    this.sessionService.getCurrentSession(this.classroomId).subscribe({
      next: (res: any) => {
        if (res.data) {
          this.sessionData.set(res.data);
          if (res.data.students) {
            this.students.set(res.data.students);
          }
          this.startElapsedTimer();
          this.startSessionPolling();
        }
      },
      error: () => {
        // No active session — login form stays, nothing to do
      },
    });
  }

  ngOnDestroy(): void {
    clearInterval(this.clockInterval);
    clearInterval(this.elapsedInterval);
    clearInterval(this.sessionPollInterval);
    clearTimeout(this.checkInMessageTimeout);
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

  checkIn(): void {
    const code = this.checkInCode().trim();
    if (!code || this.checkInLoading()) return;

    this.checkInLoading.set(true);
    this.checkInMessage.set(null);

    this.sessionService.checkInStudent(this.classroomId, code).subscribe({
      next: (res: any) => {
        const status = res.status === 'late' ? 'Late' : 'Present';
        this.checkInMessage.set({ text: `Checked in — ${status}`, type: 'success' });
        this.checkInCode.set('');
        this.checkInLoading.set(false);
        this.scheduleCheckInMessageClear();
      },
      error: (err) => {
        this.checkInMessage.set({
          text: err?.error?.message ?? 'Check-in failed',
          type: 'error',
        });
        this.checkInLoading.set(false);
        this.scheduleCheckInMessageClear();
      },
    });
  }

  private scheduleCheckInMessageClear(): void {
    clearTimeout(this.checkInMessageTimeout);
    this.checkInMessageTimeout = setTimeout(() => {
      this.checkInMessage.set(null);
    }, 3000);
  }

  private startElapsedTimer(): void {
    clearInterval(this.elapsedInterval);
    // Backend sends 'starts_at' (not 'started_at')
    const startedAt = new Date(this.sessionData().starts_at).getTime();

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

    this.sessionPollInterval = setInterval(() => {
      this.sessionService.getCurrentSession(this.classroomId).subscribe({
        next: (res: any) => {
          if (res.data) {
            if (res.data.students) {
              this.students.set(res.data.students);
            }
          } else {
            // Session ended externally — return to login form
            this.resetSessionState();
          }
        },
        error: () => {
          // 404 = no active session — return to login form
          this.resetSessionState();
        },
      });
    }, 3000);
  }

  private resetSessionState(): void {
    this.sessionData.set(null);
    this.students.set([]);
    this.elapsedTime.set('00:00:00');
    clearInterval(this.elapsedInterval);
    clearInterval(this.sessionPollInterval);
  }
}
