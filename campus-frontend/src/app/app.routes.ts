import { Routes } from '@angular/router';

import { LoginComponent } from './auth/login/login.component';
import { ClassroomTerminalComponent } from './classrooms/terminal/classroom-terminal.component';

import { AuthGuard } from './auth/auth.guard';
import { GuestGuard } from './auth/guest.guard';
import { LayoutComponent } from './main-layout/layout/layout.component';
import { DashboardComponent } from './dashboard/dashboard.component';
import { LabrirayLayout } from './library/labriray-layout/labriray-layout';
import { AddBook } from './library/add-book/add-book';
import { BookPreview } from './library/book-preview/book-preview';
import { LabrirayTable } from './library/labriray-table/labriray-table';

export const routes: Routes = [
  {
    path: '',
    component: LayoutComponent,
    children: [
      { path: 'dashboard', component: DashboardComponent },
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
  {
    path: 'login',
    component: LoginComponent,
    canActivate: [GuestGuard],
  },
  {
    path: 'classrooms/:id/terminal',
    component: ClassroomTerminalComponent,
  },

  {
    path: '**',
    redirectTo: 'login',
  },
];
