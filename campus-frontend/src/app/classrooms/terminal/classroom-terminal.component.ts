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

  loading = false;
  errorMessage: string | null = null;
  sessionData: any = null;

  // Clock
  currentTime = new Date();
  elapsedTime = '00:00:00';
  private clockInterval: any;
  private elapsedInterval: any;

  // Student roster
  studentSearch = '';
  students = signal<any[]>([]);

  filteredStudents = computed(() => {
    const term = this.studentSearch?.toLowerCase() || '';
    if (!term) return this.students();
    return this.students().filter(
      (s: any) =>
        s.first_name?.toLowerCase().includes(term) ||
        s.last_name?.toLowerCase().includes(term) ||
        s.index_number?.toLowerCase().includes(term)
    );
  });

  get presentCount(): number {
    return this.students().filter((s: any) => s.checked_in).length;
  }

  get absentCount(): number {
    return this.totalStudents - this.presentCount;
  }

  get totalStudents(): number {
    return this.students().length;
  }

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
      this.currentTime = new Date();
    }, 1000);
  }

  ngOnDestroy(): void {
    clearInterval(this.clockInterval);
    clearInterval(this.elapsedInterval);
  }

  startSession(): void {
    if (this.form.invalid || this.loading) return;

    this.loading = true;
    this.errorMessage = null;

    const { professor_code, class_pin } = this.form.value;

    this.sessionService
      .startSession(this.classroomId, professor_code!, class_pin!)
      .subscribe({
        next: (res: any) => {
          this.sessionData = res.data;
          this.loading = false;

          // Populate students if the API returns them
          if (res.data?.students) {
            this.students.set(res.data.students);
          }

          this.startElapsedTimer();
        },
        error: (err) => {
          this.errorMessage =
            err?.error?.message ?? 'Session could not be started';
          this.loading = false;
        },
      });
  }

  endSession(): void {
    if (!this.sessionData || this.loading) return;

    this.loading = true;

    this.sessionService.endSession(this.classroomId).subscribe({
      next: () => {
        this.sessionData = null;
        this.loading = false;
        this.students.set([]);
        this.elapsedTime = '00:00:00';
        clearInterval(this.elapsedInterval);
      },
      error: () => {
        this.errorMessage = 'Failed to end session';
        this.loading = false;
      },
    });
  }

  private startElapsedTimer(): void {
    clearInterval(this.elapsedInterval);
    const startedAt = new Date(this.sessionData.started_at).getTime();

    this.elapsedInterval = setInterval(() => {
      const diff = Date.now() - startedAt;
      const hrs = Math.floor(diff / 3600000);
      const mins = Math.floor((diff % 3600000) / 60000);
      const secs = Math.floor((diff % 60000) / 1000);
      this.elapsedTime =
        String(hrs).padStart(2, '0') + ':' +
        String(mins).padStart(2, '0') + ':' +
        String(secs).padStart(2, '0');
    }, 1000);
  }
}
