import { HttpClient } from '@angular/common/http';
import { Injectable, signal } from '@angular/core';
import { environment } from '../../environments/environment';

@Injectable()
export class LibraryService {
  booksSignal = signal<any[]>([]);

  constructor(private http: HttpClient) {
    this.getBooks();
  }

  getBooks() {
    this.booksSignal.set([
      {
        id: 1,
        title: 'The Great Gatsby',
        author: 'F. Scott Fitzgerald',
        publisher: "Charles Scribner's Sons",
        publishedYear: 1925,
        edition: 'First',
        description: 'A novel set in the Jazz Age on Long Island, near New York City.',
        totalCopies: 15,
        availableCopies: 10,
      },
      {
        id: 2,
        title: '1984',
        author: 'George Orwell',
        publisher: 'Secker & Warburg',
        publishedYear: 1949,
        edition: 'First',
        description: 'A novel about the totalitarian state',
        totalCopies: 10,
        availableCopies: 10,
      },
      {
        id: 3,
        title: 'To Kill a Mockingbird',
        author: 'Harper Lee',
        publisher: 'J. B. Lippincott & Co.',
        publishedYear: 1960,
        edition: 'First',
        description: 'A novel about the racial tensions in the American South',
        totalCopies: 10,
        availableCopies: 10,
      },
    ]);
  }

  getBook(id: number) {
    return this.booksSignal().find((book) => book.id === id);
  }

  searchBooks(query: string) {
    return this.http.get(`${environment.apiBaseUrl}/api/books/search?q=${query}`);
  }

  createBook(book: any) {
    // this.http.post(`${environment.apiBaseUrl}/api/books`, book).subscribe((response) => {
    //   this.booksSignal.update((books) => [...books, response]);
    // });
    this.booksSignal.update((books) => [...books, book]);
  }

  updateBook(id: number, book: any) {
    return this.http.put(`${environment.apiBaseUrl}/api/books/${id}`, book);
  }

  deleteBook(id: number) {
    this.booksSignal.update((books) => books.filter((book) => book.id !== id));
  }
}
