import { HttpClient } from '@angular/common/http';
import { Injectable, signal } from '@angular/core';
import { Observable } from 'rxjs';
import { map } from 'rxjs/operators';
import { environment } from '../../environments/environment';

export interface User {
  id: number;
  email: string;
  type: 'admin' | 'student' | 'professor';
  status: string;
  created_at?: string;
  updated_at?: string;
}

export interface CreateUserPayload {
  email: string;
  password: string;
  type: 'admin' | 'student' | 'professor';
  first_name: string;
  last_name: string;
}

export interface UpdateUserPayload {
  email?: string;
  type?: 'admin' | 'student' | 'professor';
  status?: string;
}

@Injectable({
  providedIn: 'root',
})
export class UsersService {
  usersSignal = signal<User[]>([]);

  constructor(private http: HttpClient) {}

  getUsers() {
    this.http
      .get<{ data: User[] }>(`${environment.apiBaseUrl}/users`)
      .subscribe({
        next: (response) => {
          this.usersSignal.set(response.data || []);
        },
        error: (error) => {
          console.error('Error fetching users', error);
          this.usersSignal.set([]);
        },
      });
  }

  getUser(id: number): Observable<User> {
    return this.http
      .get<{ data: User }>(`${environment.apiBaseUrl}/users/${id}`)
      .pipe(map((response) => response.data));
  }

  createUser(payload: CreateUserPayload): Observable<any> {
    return this.http.post(`${environment.apiBaseUrl}/users`, payload);
  }

  updateUser(id: number, payload: UpdateUserPayload): Observable<any> {
    return this.http.put(`${environment.apiBaseUrl}/users/${id}`, payload);
  }

  deleteUser(id: number): Observable<any> {
    return this.http.delete(`${environment.apiBaseUrl}/users/${id}`);
  }
}
