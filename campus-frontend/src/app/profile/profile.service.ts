import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { map } from 'rxjs/operators';
import { environment } from '../../environments/environment';

export interface UserProfile {
  id: number;
  email: string;
  type: 'admin' | 'student' | 'professor';
  status: string;
  name: string;
  avatar: string;
  profile?: StudentProfile | ProfessorProfile;
}

export interface StudentProfile {
  id: number;
  user_id: number;
  email: string;
  first_name: string;
  last_name: string;
  phone: string | null;
  date_of_birth: string | null;
  gender: string | null;
  student_index: string | null;
  code: string | null;
  year_of_study: number;
  department: string | null;
  gpa: string | null;
  status: string;
  created_at: string;
  updated_at: string;
}

export interface ProfessorProfile {
  id: number;
  user_id: number;
  email: string;
  code: string | null;
  first_name: string;
  last_name: string;
  phone: string | null;
  academic_title: string | null;
  department: string | null;
  employment_type: string | null;
  status: string;
  office_location: string | null;
  office_hours: string | null;
  created_at: string;
  updated_at: string;
}

@Injectable({
  providedIn: 'root'
})
export class ProfileService {
  constructor(private http: HttpClient) {}

  getProfile(): Observable<UserProfile> {
    return this.http.get<{data: UserProfile}>(`${environment.apiBaseUrl}/profile`)
      .pipe(map(response => response.data));
  }

  updateProfile(data: Partial<StudentProfile> | Partial<ProfessorProfile>): Observable<StudentProfile | ProfessorProfile> {
    return this.http.put<{message: string; data: StudentProfile | ProfessorProfile}>(`${environment.apiBaseUrl}/profile`, data)
      .pipe(map(response => response.data));
  }
}
