import { HttpClient } from '@angular/common/http';
import { Injectable, signal } from '@angular/core';
import { environment } from '../../environments/environment';
import { Observable } from 'rxjs';
import { map } from 'rxjs/operators';

export interface Session {
  id: number;
  classroom_id: number;
  course_class_id: number;
  professor_id: number | null;
  starts_at: string;
  ends_at: string | null;
  status: string;
  created_at?: string;
  updated_at?: string;
  classroom?: {
    id: number;
    name: string;
  };
  course_class?: {
    id: number;
    course?: {
      id: number;
      code: string;
      name: string;
      department: string;
    };
  };
  professor?: {
    id: number;
    code: string;
    first_name: string;
    last_name: string;
    academic_title: string | null;
    department: string;
    email: string;
  };
  students?: SessionStudent[];
}

export interface SessionStudent {
  id: number;
  first_name: string;
  last_name: string;
  code: string;
  index_number: string;
  checked_in: boolean;
  checked_in_at: string | null;
  status: string | null;
}

interface SessionsResponse {
  data: Session[];
}

interface SessionResponse {
  data: Session;
}

@Injectable({
  providedIn: 'root',
})
export class SessionsService {
  sessionsSignal = signal<Session[]>([]);

  constructor(private http: HttpClient) {}

  getSessions() {
    this.http
      .get<SessionsResponse>(`${environment.apiBaseUrl}/sessions`)
      .subscribe({
        next: (response: any) => {
          this.sessionsSignal.set(response.data || []);
        },
        error: (error) => {
          console.error('Error fetching sessions', error);
          this.sessionsSignal.set([]);
        },
      });
  }

  getSession(id: number): Observable<Session> {
    return this.http
      .get<SessionResponse>(`${environment.apiBaseUrl}/sessions/${id}`)
      .pipe(map((response) => response.data));
  }
}
