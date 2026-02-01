import { Component, computed, inject } from '@angular/core';
import { LibraryService } from '../library.service';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { DatePipe } from '@angular/common';

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
  book = computed(() => this.libraryService.getBook(Number(this.activatedRoute.snapshot.params['id'])));

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
