import { Component, computed, inject, signal } from '@angular/core';
import { LibraryService } from '../library.service';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { DatePipe } from '@angular/common';
import { AuthService } from '../../auth/auth.service';

@Component({
  selector: 'app-book-preview',
  imports: [RouterLink, DatePipe],
  templateUrl: './book-preview.html',
  styleUrl: './book-preview.scss',
})
export class BookPreview {
  libraryService = inject(LibraryService);
  activatedRoute = inject(ActivatedRoute);
  router = inject(Router);
  authService = inject(AuthService);
  isAdmin = this.authService.isAdmin();

  book = computed(() => this.libraryService.getBook(Number(this.activatedRoute.snapshot.params['id'])));

  // Courses data
  courses = signal<any[]>([]);
  isLoadingCourses = signal<boolean>(true);

  constructor() {
    // Load courses for this book
    const bookId = Number(this.activatedRoute.snapshot.params['id']);
    this.libraryService.getCoursesByBook(bookId).subscribe({
      next: (courses) => {
        this.courses.set(courses);
        this.isLoadingCourses.set(false);
      },
      error: (error) => {
        console.error('Error loading courses:', error);
        this.isLoadingCourses.set(false);
      }
    });
  }

  deleteBook() {
    const bookId = Number(this.activatedRoute.snapshot.params['id']);
    const bookTitle = this.book()?.title;

    if (confirm(`Are you sure you want to permanently delete "${bookTitle}"?\n\nThis action cannot be undone.`)) {
      this.libraryService.deleteBook(bookId).subscribe({
        next: () => {
          this.router.navigate(['/library']);
        },
        error: (error) => {
          console.error('Error deleting book:', error);
          alert('Failed to delete book. Please try again.');
        }
      });
    }
  }
}
