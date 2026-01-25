import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { tap } from 'rxjs/operators';
import { Observable, of } from 'rxjs';
import { environment } from '../../environments/environment';


@Injectable({
  providedIn: 'root',
})
export class AuthService {

  private readonly API_URL = environment.apiBaseUrl;
  private readonly TOKEN_KEY = 'auth_token';

  constructor(private http: HttpClient) {}

  login(email: string, password: string): Observable<any> {
    return this.http
      .post(`${this.API_URL}/auth/login`, { email, password })
      .pipe(
        tap((response: any) => {
          const token = response?.data?.token;
          if (token) {
            localStorage.setItem(this.TOKEN_KEY, token);
          }
        })
      );
  }

  // ✅ FIXED: synchronous, correct key
  logout(): void {
    localStorage.removeItem(this.TOKEN_KEY);
  }

  getToken(): string | null {
    return localStorage.getItem(this.TOKEN_KEY);
  }

  isAuthenticated(): boolean {
    return !!this.getToken();
  }
}
