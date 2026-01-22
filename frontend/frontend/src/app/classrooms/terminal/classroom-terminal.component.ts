import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import {
  FormGroup,
  FormControl,
  Validators,
  ReactiveFormsModule,
} from '@angular/forms';
import { ActivatedRoute } from '@angular/router';

import { ClassroomSessionService } from '../../services/classroom-session.service';

@Component({
  standalone: true,
  selector: 'app-classroom-terminal',
  templateUrl: './classroom-terminal.component.html',
  styleUrls: ['./classroom-terminal.component.scss'],
  imports: [
    CommonModule,         // ngIf, date pipe
    ReactiveFormsModule,  // formGroup, formControlName
  ],
})
export class ClassroomTerminalComponent {

  classroomId!: number;

  loading = false;
  errorMessage: string | null = null;
  sessionData: any = null;

  form = new FormGroup({
    professor_code: new FormControl('', Validators.required),
    class_pin: new FormControl('', Validators.required),
  });

  constructor(
    private route: ActivatedRoute,
    private sessionService: ClassroomSessionService
  ) {
    this.classroomId = Number(this.route.snapshot.paramMap.get('id'));
  }

  startSession(): void {
    if (this.form.invalid || this.loading) {
      return;
    }

    this.loading = true;
    this.errorMessage = null;

    const { professor_code, class_pin } = this.form.value;

    this.sessionService
      .startSession(
        this.classroomId,
        professor_code!,
        class_pin!
      )
      .subscribe({
        next: (res) => {
          this.sessionData = res.data;
          this.loading = false;
        },
        error: (err) => {
          this.errorMessage =
            err?.error?.message ?? 'Session could not be started';
          this.loading = false;
        },
      });
  }
}
