import { HttpClient } from '@angular/common/http';
import { Injectable, signal } from '@angular/core';
import { environment } from '../../environments/environment';
import { Observable } from 'rxjs';
import { map } from 'rxjs/operators';

export interface Professor {
  id: number;
  user_id: number | null;
  code: string;
  first_name: string;
  last_name: string;
  email: string;
  phone: string | null;
  academic_title: string | null;
  department: string;
  employment_type: string;
  status: string;
  office_location: string | null;
  office_hours: string | null;
  created_at?: string;
  updated_at?: string;
}

interface ProfessorsResponse {
  data: Professor[];
}

interface ProfessorResponse {
  data: Professor;
}

@Injectable({
  providedIn: 'root'
})
export class ProfessorsService {
  professorsSignal = signal<Professor[]>([]);

  constructor(private http: HttpClient) {}

  getProfessors() {
    this.http.get<ProfessorsResponse>(`${environment.apiBaseUrl}/professors`).subscribe({
      next: (response: any) => {
        this.professorsSignal.set(response.data);
      },
      error: (error) => {
        console.error('Error fetching professors', error);
        this.professorsSignal.set([]);
      }
    });
  }

  getProfessor(id: number): Observable<Professor> {
    return this.http.get<ProfessorResponse>(`${environment.apiBaseUrl}/professors/${id}`)
      .pipe(map(response => response.data));
  }

  getProfessorByCode(code: string): Observable<Professor> {
    return this.http.get<ProfessorResponse>(`${environment.apiBaseUrl}/professors/code/${code}`)
      .pipe(map(response => response.data));
  }

  getProfessorsByDepartment(department: string): Observable<Professor[]> {
    return this.http.get<ProfessorsResponse>(`${environment.apiBaseUrl}/professors/department/${department}`)
      .pipe(map(response => response.data));
  }

  getActiveProfessors(): Observable<Professor[]> {
    return this.http.get<ProfessorsResponse>(`${environment.apiBaseUrl}/professors/active`)
      .pipe(map(response => response.data));
  }
}
