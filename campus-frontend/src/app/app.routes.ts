import { Routes } from '@angular/router';

import { LoginComponent } from './auth/login/login.component';
import { VerifyEmailComponent } from './auth/verify-email/verify-email.component';
import { SetPasswordComponent } from './auth/set-password/set-password.component';
import { ClassroomTerminalComponent } from './classrooms/terminal/classroom-terminal.component';

import { authGuard } from './auth/auth.guard';
import { loginGuard } from './auth/login.guard';
import { LayoutComponent } from './main-layout/layout/layout.component';
import { DashboardComponent } from './dashboard/dashboard.component';
import { LabrirayLayout } from './library/labriray-layout/labriray-layout';
import { AddBook } from './library/add-book/add-book';
import { BookPreview } from './library/book-preview/book-preview';
import { LabrirayTable } from './library/labriray-table/labriray-table';
import { StudentsLayout } from './students/students-layout/students-layout.component';
import { StudentsTable } from './students/students-table/students-table.component';
import { StudentPreview } from './students/student-preview/student-preview.component';

export const routes: Routes = [
  // Public routes (NO authentication required)
  {
    path: 'login',
    component: LoginComponent,
    canActivate: [loginGuard],
  },
  {
    path: 'verify-email',
    component: VerifyEmailComponent,
    // NO guard - anyone can access
  },
  {
    path: 'set-password',
    component: SetPasswordComponent,
    // NO guard - anyone can access
  },

  // Protected routes (require authentication)
  {
    path: '',
    component: LayoutComponent,
    canActivate: [authGuard],
    children: [
      {
        path: '',
        redirectTo: 'dashboard',
        pathMatch: 'full'
      },
      {
        path: 'dashboard',
        component: DashboardComponent
      },
      {
        path: 'library',
        component: LabrirayLayout,
        children: [
          { path: '', component: LabrirayTable },
          { path: 'add-book', component: AddBook },
          { path: 'book/:id', component: BookPreview },
        ],
      },
      {
        path: 'students',
        component: StudentsLayout,
        children: [
          { path: '', component: StudentsTable },
           { path: ':id', component: StudentPreview },
        ],
      },
      { path: 'dorm', component: LabrirayLayout },
      { path: 'classes', component: LabrirayLayout },
      { path: 'students', component: LabrirayLayout },
      { path: 'professors', component: LabrirayLayout },
      { path: 'exams', component: LabrirayLayout },
    ],
  },

  // Classroom terminal (protected)
  {
    path: 'classrooms/:id/terminal',
    component: ClassroomTerminalComponent,
    canActivate: [authGuard],
  },

  // Wildcard - redirect to login
  {
    path: '**',
    redirectTo: 'login',
  },
];
