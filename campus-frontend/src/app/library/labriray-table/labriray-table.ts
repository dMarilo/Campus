import { Component, computed, inject, signal, effect } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { ReactiveFormsModule } from '@angular/forms';
import { LibraryService } from '../library.service';
import { RouterLink } from '@angular/router';

@Component({
  selector: 'app-labriray-table',
  imports: [FormsModule, ReactiveFormsModule, RouterLink],
  templateUrl: './labriray-table.html',
  styleUrl: './labriray-table.scss',
})
export class LabrirayTable {
  libraryService = inject(LibraryService);
  books = computed(() => this.libraryService.booksSignal());
  searchTerm = signal<string>('');
  isLoading = signal<boolean>(true);

  filteredBooks = computed(() => {
    return this.books().filter(
      (book) =>
        book.title.toLowerCase().includes(this.searchTerm().toLowerCase()) ||
        book.author.toLowerCase().includes(this.searchTerm().toLowerCase()) ||
        book.publisher.toLowerCase().includes(this.searchTerm().toLowerCase()) ||
        book.description.toLowerCase().includes(this.searchTerm().toLowerCase()),
    );
  });

  constructor() {
    // Watch for when books are loaded
    effect(() => {
      if (this.books().length > 0) {
        this.isLoading.set(false);
      }
    });
  }

  deleteBook(id: number) {
    const book = this.books().find(b => b.id === id);
    if (confirm(`Are you sure you want to delete "${book?.title}"?`)) {
      this.libraryService.deleteBook(id).subscribe({
        next: () => {
          console.log('Book deleted successfully');
        },
        error: (error) => {
          console.error('Error deleting book:', error);
          alert('Failed to delete book');
        }
      });
    }
  }
}
