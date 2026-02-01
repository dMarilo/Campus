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

    const formValue = this.form.value;

    // Transform form values to match backend API
    const book = {
      title: formValue.title!,
      author: formValue.author!,
      publisher: formValue.publisher!,
      published_year: parseInt(formValue.publishedYear!),
      edition: formValue.edition!,
      description: formValue.description!,
      total_copies: parseInt(formValue.totalCopies!),
      available_copies: parseInt(formValue.availableCopies!),
    };

    this.libraryService.createBook(book).subscribe({
      next: (createdBook) => {
        console.log('Book created successfully:', createdBook);
        this.router.navigate(['/library']);
      },
      error: (error) => {
        console.error('Error creating book:', error);
        alert('Failed to create book. Please try again.');
      }
    });
  }
}
