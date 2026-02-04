import { Component, computed, inject, OnInit, signal } from '@angular/core';
import { StudentsService } from '../students.service';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { DatePipe, TitleCasePipe } from '@angular/common';
import { toSignal } from '@angular/core/rxjs-interop';

interface Book {
  id: number;
  title: string;
  author: string;
  publisher: string;
  published_year: number;
  edition: string;
  description: string;
  total_copies: number;
  available_copies: number;
  created_at: string;
  updated_at: string;
}

interface BookCopy {
  id: number;
  book_id: number;
  isbn: string;
  status: string;
  created_at: string;
  updated_at: string;
  book: Book;
}

interface Borrowing {
  id: number;
  book_copy_id: number;
  student_id: number;
  borrowed_at: string;
  due_at: string;
  returned_at: string | null;
  status: string;
  created_at: string;
  updated_at: string;
  book_copy: BookCopy;
}

@Component({
  selector: 'app-student-preview',
  imports: [RouterLink, DatePipe, TitleCasePipe],
  templateUrl: './student-preview.component.html',
  styleUrl: './student-preview.component.scss',
})
export class StudentPreview implements OnInit {
  studentsService = inject(StudentsService);
  activatedRoute = inject(ActivatedRoute);
  router = inject(Router);

  student = toSignal(
    this.studentsService.getStudent(
      Number(this.activatedRoute.snapshot.params['id'])
    )
  );

  borrowings = signal<Borrowing[]>([]);
  borrowingType = signal<'all' | 'current'>('current');
  loadingBorrowings = signal<boolean>(false);

  currentBorrowings = computed(() =>
    this.borrowings().filter(b => b.status === 'borrowed')
  );

  // Expose Math to template
  Math = Math;

  ngOnInit() {
    this.loadBorrowings();
  }

  loadBorrowings() {
    const studentId = Number(this.activatedRoute.snapshot.params['id']);
    this.loadingBorrowings.set(true);

    this.studentsService.getStudentBorrowings(studentId, this.borrowingType())
      .subscribe({
        next: (borrowings) => {
          this.borrowings.set(borrowings);
          this.loadingBorrowings.set(false);
        },
        error: (error) => {
          console.error('Error loading borrowings:', error);
          this.borrowings.set([]);
          this.loadingBorrowings.set(false);
        }
      });
  }

  switchBorrowingType(type: 'all' | 'current') {
    this.borrowingType.set(type);
    this.loadBorrowings();
  }

  isOverdue(dueDate: string): boolean {
    return new Date(dueDate) < new Date() && this.borrowingType() === 'current';
  }

  getDaysUntilDue(dueDate: string): number {
    const due = new Date(dueDate);
    const now = new Date();
    const diffTime = due.getTime() - now.getTime();
    return Math.ceil(diffTime / (1000 * 60 * 60 * 24));
  }
}
