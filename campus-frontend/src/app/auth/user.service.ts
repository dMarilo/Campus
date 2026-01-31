import { Injectable } from '@angular/core';
import { signal } from '@angular/core';

export interface User {
  id: number;
  full_name: string;
  email: string;
  role?: string;
}

@Injectable({
  providedIn: 'root',
})
export class UserService {

  private readonly _me = signal<User | null>(null);

  // public readonly signal
  me = this._me.asReadonly();

  set(user: User): void {
    this._me.set(user);
  }

  clear(): void {
    this._me.set(null);
  }
}
