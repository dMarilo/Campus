import { Component, computed, inject, OnInit, signal } from '@angular/core';
import { CoursesService, CourseBook } from '../courses.service';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { DatePipe, TitleCasePipe } from '@angular/common';
import { toSignal } from '@angular/core/rxjs-interop';

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
