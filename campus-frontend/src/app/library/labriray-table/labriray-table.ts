import { Component, computed, inject, signal, effect } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { ReactiveFormsModule } from '@angular/forms';
import { LibraryService } from '../library.service';
import { RouterLink } from '@angular/router';
import { AuthService } from '../../auth/auth.service';
import { ConfirmModalService } from '../../shared/confirm-modal/confirm-modal.service';

@Component({
  selector: 'app-labriray-table',
  imports: [FormsModule, ReactiveFormsModule, RouterLink],
  templateUrl: './labriray-table.html',
  styleUrl: './labriray-table.scss',
})
export class LabrirayTable {
  libraryService = inject(LibraryService);
  authService = inject(AuthService);
  confirmModal = inject(ConfirmModalService);
  isAdmin = this.authService.isAdmin();
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
    effect(() => {
      if (this.books().length > 0) {
        this.isLoading.set(false);
      }
    });
  }

  async deleteBook(id: number) {
    const book = this.books().find(b => b.id === id);
    const confirmed = await this.confirmModal.confirm({
      title: 'Delete Book',
      itemName: book?.title ?? '',
      message: 'This action cannot be undone.',
    });
    if (!confirmed) return;

    this.libraryService.deleteBook(id).subscribe({
      next: () => {
        this.libraryService.getBooks();
      },
      error: (err) => {
        alert(err?.error?.message ?? 'Failed to delete book.');
      },
    });
  }
}
