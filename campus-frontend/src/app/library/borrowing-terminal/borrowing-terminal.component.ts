import { Component, OnDestroy } from '@angular/core';
import { CommonModule } from '@angular/common';
import {
  FormGroup,
  FormControl,
  Validators,
  ReactiveFormsModule,
} from '@angular/forms';

import { BorrowingTerminalService } from './borrowing-terminal.service';

@Component({
  standalone: true,
  selector: 'app-borrowing-terminal',
  templateUrl: './borrowing-terminal.component.html',
  styleUrls: ['./borrowing-terminal.component.scss'],
  imports: [
    CommonModule,
    ReactiveFormsModule,
  ],
})
export class BorrowingTerminalComponent implements OnDestroy {

  loading = false;
  errorMessage: string | null = null;
  successMessage: string | null = null;
  borrowedBook: any = null;

  // Clock
  currentTime = new Date();
  private clockInterval: any;

  form = new FormGroup({
    student_code: new FormControl('', Validators.required),
    isbn: new FormControl('', Validators.required),
  });

  constructor(
    private borrowingService: BorrowingTerminalService
  ) {
    // Update clock every second
    this.clockInterval = setInterval(() => {
      this.currentTime = new Date();
    }, 1000);
  }

  ngOnDestroy(): void {
    clearInterval(this.clockInterval);
  }

  borrowBook(): void {
    if (this.form.invalid || this.loading) return;

    this.loading = true;
    this.errorMessage = null;
    this.successMessage = null;
    this.borrowedBook = null;

    const { student_code, isbn } = this.form.value;

    this.borrowingService
      .borrowBook(student_code!, isbn!)
      .subscribe({
        next: (res: any) => {
          this.successMessage = res.message || 'Book successfully borrowed!';
          this.borrowedBook = res.data;
          this.loading = false;
          this.form.reset();

          // Clear success message after 5 seconds
          setTimeout(() => {
            this.successMessage = null;
            this.borrowedBook = null;
          }, 5000);
        },
        error: (err) => {
          this.errorMessage =
            err?.error?.message ?? 'Failed to borrow book. Please check the information and try again.';
          this.loading = false;
        },
      });
  }

  clearMessages(): void {
    this.errorMessage = null;
    this.successMessage = null;
    this.borrowedBook = null;
  }
}
