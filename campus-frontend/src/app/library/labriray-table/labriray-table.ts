import { Component, computed, inject, signal } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { NgbPagination } from '@ng-bootstrap/ng-bootstrap';
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
  filteredBooks = computed(() => {
    return this.books().filter(
      (book) =>
        book.title.toLowerCase().includes(this.searchTerm().toLowerCase()) ||
        book.author.toLowerCase().includes(this.searchTerm().toLowerCase()) ||
        book.publisher.toLowerCase().includes(this.searchTerm().toLowerCase()) ||
        book.description.toLowerCase().includes(this.searchTerm().toLowerCase()),
    );
  });
  page = 1;
  pageSize = 10;

  deleteBook(id: number) {
    this.libraryService.deleteBook(id);
  }
}
