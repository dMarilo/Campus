import { Component, inject, OnInit, signal } from '@angular/core';
import { FormControl, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { CoursesService } from '../courses.service';

@Component({
  selector: 'app-course-form',
  imports: [ReactiveFormsModule, RouterLink],
  templateUrl: './course-form.component.html',
  styleUrl: './course-form.component.scss',
})
export class CourseForm implements OnInit {
  coursesService = inject(CoursesService);
  router = inject(Router);
  activatedRoute = inject(ActivatedRoute);

  isEditMode = signal(false);
  courseId = signal<number | null>(null);
  isSubmitting = signal(false);
  errorMessage = signal<string | null>(null);

  form = new FormGroup({
    code: new FormControl('', [Validators.required]),
    name: new FormControl('', [Validators.required]),
    description: new FormControl('', [Validators.required]),
    ects: new FormControl<number | null>(null, [Validators.required, Validators.min(1)]),
    department: new FormControl('', [Validators.required]),
    level: new FormControl('', [Validators.required]),
    mandatory: new FormControl(false),
    status: new FormControl('active', [Validators.required]),
  });

  ngOnInit() {
    const id = this.activatedRoute.snapshot.params['id'];
    if (id) {
      this.isEditMode.set(true);
      this.courseId.set(Number(id));
      this.loadCourse(Number(id));
    }
  }

  loadCourse(id: number) {
    this.coursesService.getCourse(id).subscribe({
      next: (course) => {
        this.form.patchValue({
          code: course.code,
          name: course.name,
          description: course.description,
          ects: course.ects,
          department: course.department,
          level: course.level,
          mandatory: course.mandatory,
          status: course.status,
        });
      },
      error: () => {
        this.errorMessage.set('Failed to load course data.');
      },
    });
  }

  submit() {
    if (this.form.invalid) {
      this.form.markAllAsTouched();
      return;
    }

    this.isSubmitting.set(true);
    this.errorMessage.set(null);

    const payload = {
      code: this.form.value.code!,
      name: this.form.value.name!,
      description: this.form.value.description!,
      ects: Number(this.form.value.ects),
      department: this.form.value.department!,
      level: this.form.value.level!,
      mandatory: !!this.form.value.mandatory,
      status: this.form.value.status!,
    };

    if (this.isEditMode()) {
      this.coursesService.updateCourse(this.courseId()!, payload).subscribe({
        next: () => {
          this.router.navigate(['/courses', this.courseId()]);
        },
        error: (err) => {
          this.errorMessage.set(err?.error?.message ?? 'Failed to update course.');
          this.isSubmitting.set(false);
        },
      });
    } else {
      this.coursesService.createCourse(payload).subscribe({
        next: (created) => {
          this.router.navigate(['/courses', created.id]);
        },
        error: (err) => {
          this.errorMessage.set(err?.error?.message ?? 'Failed to create course.');
          this.isSubmitting.set(false);
        },
      });
    }
  }
}
