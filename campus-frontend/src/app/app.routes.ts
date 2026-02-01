import { Routes } from '@angular/router';

import { LoginComponent } from './auth/login/login.component';
import { ClassroomTerminalComponent } from './classrooms/terminal/classroom-terminal.component';

import { authGuard } from './auth/auth.guard';
import { loginGuard } from './auth/login.guard';
import { LayoutComponent } from './main-layout/layout/layout.component';
import { DashboardComponent } from './dashboard/dashboard.component';
import { LabrirayLayout } from './library/labriray-layout/labriray-layout';
import { AddBook } from './library/add-book/add-book';
import { BookPreview } from './library/book-preview/book-preview';
import { LabrirayTable } from './library/labriray-table/labriray-table';

export const routes: Routes = [
  // Protected routes (require authentication)
  {
    path: '',
    component: LayoutComponent,
    canActivate: [authGuard], // Protect all routes under LayoutComponent
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
      { path: 'dorm', component: LabrirayLayout },
      { path: 'classes', component: LabrirayLayout },
      { path: 'students', component: LabrirayLayout },
      { path: 'professors', component: LabrirayLayout },
      { path: 'exams', component: LabrirayLayout },
    ],
  },

  // Login route (only accessible when not authenticated)
  {
    path: 'login',
    component: LoginComponent,
    canActivate: [loginGuard], // Redirect to dashboard if already logged in
  },

  // Classroom terminal (protected)
  {
    path: 'classrooms/:id/terminal',
    component: ClassroomTerminalComponent,
    canActivate: [authGuard],
  },

  // Wildcard - redirect to login if not authenticated, dashboard if authenticated
  {
    path: '**',
    redirectTo: 'login',
  },
];
