import { Component, inject } from '@angular/core';
import { FormControl, ReactiveFormsModule, Validators } from '@angular/forms';
import { FormGroup } from '@angular/forms';
import { LibraryService } from '../library.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-add-book',
  imports: [ReactiveFormsModule],
  templateUrl: './add-book.html',
  styleUrl: './add-book.scss',
})
export class AddBook {
  libraryService = inject(LibraryService);
  router = inject(Router);
  form = new FormGroup({
    title: new FormControl('', [Validators.required]),
    author: new FormControl('', [Validators.required]),
    publisher: new FormControl('', [Validators.required]),
    publishedYear: new FormControl('', [Validators.required]),
    edition: new FormControl('', [Validators.required]),
    description: new FormControl('', [Validators.required]),
    totalCopies: new FormControl('', [Validators.required]),
    availableCopies: new FormControl('', [Validators.required]),
  }); 
  addBook() {
    if (this.form.invalid) {
      this.form.markAllAsTouched();
      return;
    }
    this.libraryService.createBook(this.form.value);
    this.router.navigate(['/library']);
  }
}
