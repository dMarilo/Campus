import { HttpClient } from '@angular/common/http';
import { Injectable, signal } from '@angular/core';
import { environment } from '../../environments/environment';
import { Observable } from 'rxjs';
import { tap, map } from 'rxjs/operators';

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
  created_at?: string;
  updated_at?: string;
}

interface BooksResponse {
  data: Book[];
}

interface BookResponse {
  data: Book;
}

@Injectable()
export class LibraryService {
  booksSignal = signal<Book[]>([]);

  constructor(private http: HttpClient) {
    this.getBooks();
  }

  getBooks() {
    this.http.get<BooksResponse>(`${environment.apiBaseUrl}/books`).subscribe({
      next: (response: any) => {
        this.booksSignal.set(response.data);
      },
      error: (error) => {
        console.error('Error fetching books', error);
        this.booksSignal.set([]);
      }
    });
  }

  getBook(id: number): Book | undefined {
    return this.booksSignal().find((book) => book.id === id);
  }

  searchBooks(query: string): Observable<Book[]> {
    return this.http.get<BooksResponse>(`${environment.apiBaseUrl}/books/search?q=${query}`)
      .pipe(map(response => response.data));
  }

  createBook(book: Partial<Book>): Observable<Book> {
    return this.http.post<BookResponse>(`${environment.apiBaseUrl}/books`, book)
      .pipe(
        map(response => response.data),
        tap((newBook) => {
          this.booksSignal.update((books) => [...books, newBook]);
        })
      );
  }

  updateBook(id: number, book: Partial<Book>): Observable<Book> {
    return this.http.put<BookResponse>(`${environment.apiBaseUrl}/books/${id}`, book)
      .pipe(
        map(response => response.data),
        tap((updatedBook) => {
          this.booksSignal.update((books) =>
            books.map(b => b.id === id ? updatedBook : b)
          );
        })
      );
  }

  deleteBook(id: number): Observable<any> {
    return this.http.delete(`${environment.apiBaseUrl}/books/${id}`)
      .pipe(
        tap(() => {
          this.booksSignal.update((books) => books.filter((book) => book.id !== id));
        })
      );
  }

  getBooksByCourse(courseId: number): Observable<Book[]> {
    return this.http.get<BooksResponse>(`${environment.apiBaseUrl}/books/course/${courseId}`)
      .pipe(map(response => response.data));
  }
}
