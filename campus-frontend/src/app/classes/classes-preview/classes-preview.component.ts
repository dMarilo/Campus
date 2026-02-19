import { Component, computed, inject, OnInit, signal } from '@angular/core';
import { ClassesService, ClassProfessor, ClassStudent } from '../classes.service';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { DatePipe, TitleCasePipe } from '@angular/common';
import { toSignal } from '@angular/core/rxjs-interop';
import { AuthService } from '../../auth/auth.service';

@Component({
  selector: 'app-classes-preview',
  imports: [RouterLink, DatePipe, TitleCasePipe],
  templateUrl: './classes-preview.component.html',
  styleUrl: './classes-preview.component.scss',
})
export class ClassesPreview implements OnInit {
  classesService = inject(ClassesService);
  authService = inject(AuthService);
  activatedRoute = inject(ActivatedRoute);
  router = inject(Router);
  isStudent = this.authService.getUser()?.type === 'student';

  courseClass = toSignal(
    this.classesService.getClass(
      Number(this.activatedRoute.snapshot.params['id'])
    )
  );

  // Professors
  professors = signal<ClassProfessor[]>([]);
  loadingProfessors = signal<boolean>(false);

  // Students
  students = signal<ClassStudent[]>([]);
  loadingStudents = signal<boolean>(false);

  // Active tab
  activeTab = signal<'professors' | 'students'>('professors');

  // Selected professor for detail view
  selectedProfessor = signal<ClassProfessor | null>(null);

  ngOnInit() {
    this.loadProfessors();
    if (!this.isStudent) {
      this.loadStudents();
    }
  }

  loadProfessors() {
    const classId = Number(this.activatedRoute.snapshot.params['id']);
    this.loadingProfessors.set(true);

    this.classesService.getClassProfessors(classId).subscribe({
      next: (professors) => {
        this.professors.set(professors);
        this.loadingProfessors.set(false);
      },
      error: (error) => {
        console.error('Error loading professors:', error);
        this.professors.set([]);
        this.loadingProfessors.set(false);
      }
    });
  }

  loadStudents() {
    const classId = Number(this.activatedRoute.snapshot.params['id']);
    this.loadingStudents.set(true);

    this.classesService.getClassStudents(classId).subscribe({
      next: (students) => {
        this.students.set(students);
        this.loadingStudents.set(false);
      },
      error: (error) => {
        console.error('Error loading students:', error);
        this.students.set([]);
        this.loadingStudents.set(false);
      }
    });
  }

  switchTab(tab: 'professors' | 'students') {
    this.activeTab.set(tab);
    this.selectedProfessor.set(null);
  }

  selectProfessor(professor: ClassProfessor) {
    this.selectedProfessor.set(professor);
  }

  closeProfessorDetail() {
    this.selectedProfessor.set(null);
  }

  formatEmploymentType(type: string): string {
    if (!type) return 'N/A';
    return type.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
  }
}
