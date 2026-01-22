import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../environments/environment';

@Injectable({
  providedIn: 'root',
})
export class ClassroomSessionService {

  private readonly API_URL = environment.apiBaseUrl;

  constructor(private http: HttpClient) {}

  startSession(
    classroomId: number,
    professorCode: string,
    classPin: string
  ): Observable<any> {
    return this.http.post(
      `${this.API_URL}/classrooms/${classroomId}/start-session`,
      {
        professor_code: professorCode,
        class_pin: classPin,
      }
    );
  }
}
