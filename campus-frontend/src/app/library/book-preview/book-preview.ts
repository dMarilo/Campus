import { Component, computed, inject } from '@angular/core';
import { LibraryService } from '../library.service';
import { ActivatedRoute, Router } from '@angular/router';

@Component({
  selector: 'app-book-preview',
  imports: [],
  templateUrl: './book-preview.html',
  styleUrl: './book-preview.scss',
})
export class BookPreview {
  libraryService = inject(LibraryService);
  activatedRoute = inject(ActivatedRoute)
  router = inject(Router);
  book = computed(() => this.libraryService.getBook(Number(this.activatedRoute.snapshot.params['id'])));
  deleteBook() {
    this.libraryService.deleteBook(Number(this.activatedRoute.snapshot.params['id']));
    this.router.navigate(['/library']);
  }
}
