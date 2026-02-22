import { Component, computed, inject, OnInit, signal } from '@angular/core';
import { FormControl, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { ClassesService, Semester, AcademicYear } from '../classes.service';
import { CoursesService } from '../../courses/courses.service';

@Component({
  selector: 'app-class-form',
  imports: [ReactiveFormsModule, RouterLink],
  templateUrl: './class-form.component.html',
  styleUrl: './class-form.component.scss',
})
export class ClassForm implements OnInit {
  classesService = inject(ClassesService);
  coursesService = inject(CoursesService);
  router = inject(Router);

  courses = computed(() => this.coursesService.coursesSignal());
  semesters = signal<Semester[]>([]);
  academicYears = signal<AcademicYear[]>([]);

  isSubmitting = signal(false);
  errorMessage = signal<string | null>(null);
  loadingSemesters = signal(true);
  loadingYears = signal(true);

  form = new FormGroup({
    course_id: new FormControl<number | null>(null, [Validators.required]),
    semester_id: new FormControl<number | null>(null, [Validators.required]),
    academic_year_id: new FormControl<number | null>(null, [Validators.required]),
    iteration: new FormControl<number>(1, [Validators.required, Validators.min(1)]),
    status: new FormControl('planned', [Validators.required]),
  });

  ngOnInit() {
    this.coursesService.getCourses();

    this.classesService.getSemesters().subscribe({
      next: (data) => {
        this.semesters.set(data);
        this.loadingSemesters.set(false);
      },
      error: () => this.loadingSemesters.set(false),
    });

    this.classesService.getAcademicYears().subscribe({
      next: (data) => {
        this.academicYears.set(data);
        this.loadingYears.set(false);
      },
      error: () => this.loadingYears.set(false),
    });
  }

  get isLoadingDropdowns(): boolean {
    return this.loadingSemesters() || this.loadingYears();
  }

  submit() {
    if (this.form.invalid) {
      this.form.markAllAsTouched();
      return;
    }

    this.isSubmitting.set(true);
    this.errorMessage.set(null);

    const v = this.form.value;

    this.classesService.createClass({
      course_id: Number(v.course_id),
      semester_id: Number(v.semester_id),
      academic_year_id: Number(v.academic_year_id),
      iteration: Number(v.iteration),
      status: v.status!,
    }).subscribe({
      next: (created) => {
        this.router.navigate(['/classes', created.id]);
      },
      error: (err) => {
        this.errorMessage.set(err?.error?.message ?? 'Failed to create class.');
        this.isSubmitting.set(false);
      },
    });
  }
}
