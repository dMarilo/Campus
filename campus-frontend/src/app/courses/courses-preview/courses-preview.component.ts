import { Component, computed, inject, OnInit, signal } from '@angular/core';
import { CoursesService, CourseBook } from '../courses.service';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { TitleCasePipe } from '@angular/common';
import { toSignal } from '@angular/core/rxjs-interop';
import { AuthService } from '../../auth/auth.service';
import { ConfirmModalService } from '../../shared/confirm-modal/confirm-modal.service';

@Component({
  selector: 'app-course-preview',
  imports: [RouterLink, TitleCasePipe],
  templateUrl: './courses-preview.component.html',
  styleUrl: './courses-preview.component.scss',
})
export class CoursesPreview implements OnInit {
  coursesService = inject(CoursesService);
  activatedRoute = inject(ActivatedRoute);
  router = inject(Router);
  authService = inject(AuthService);
  confirmModal = inject(ConfirmModalService);

  course = toSignal(
    this.coursesService.getCourse(
      Number(this.activatedRoute.snapshot.params['id'])
    )
  );

  courseBooks = signal<CourseBook[]>([]);
  loadingBooks = signal<boolean>(false);

  mandatoryBooks = computed(() =>
    this.courseBooks().filter(cb => cb.mandatory)
  );

  optionalBooks = computed(() =>
    this.courseBooks().filter(cb => !cb.mandatory)
  );

  ngOnInit() {
    this.loadCourseBooks();
  }

  async deleteCourse() {
    const c = this.course();
    if (!c) return;

    const confirmed = await this.confirmModal.confirm({
      title: 'Delete Course',
      itemName: c.name,
      message: 'This action cannot be undone.',
    });
    if (!confirmed) return;

    this.coursesService.deleteCourse(c.id).subscribe({
      next: () => {
        this.router.navigate(['/courses']);
      },
      error: (err) => {
        alert(err?.error?.message ?? 'Failed to delete course.');
      },
    });
  }

  loadCourseBooks() {
    const courseId = Number(this.activatedRoute.snapshot.params['id']);
    this.loadingBooks.set(true);

    this.coursesService.getCourseBooks(courseId)
      .subscribe({
        next: (books) => {
          this.courseBooks.set(books);
          this.loadingBooks.set(false);
        },
        error: (error) => {
          console.error('Error loading course books:', error);
          this.courseBooks.set([]);
          this.loadingBooks.set(false);
        }
      });
  }
}
