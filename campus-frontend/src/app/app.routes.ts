import { Routes } from '@angular/router';

import { LoginComponent } from './auth/login/login.component';
import { HomeComponent } from './home/home.component';
import { ClassroomTerminalComponent } from './classrooms/terminal/classroom-terminal.component';

import { AuthGuard } from './auth/auth.guard';
import { GuestGuard } from './auth/guest.guard';
import { PageLayoutComponent } from './layouts/page-layout/page-layout.component';

export const routes: Routes = [

  {
    path: 'login',
    component: LoginComponent,
    canActivate: [GuestGuard],
  },

  {
    path: '',
    component: PageLayoutComponent,
    canActivate: [AuthGuard],
    children: [
      {
        path: 'home',
        component: HomeComponent,
      },

      // later:
      // { path: 'courses', component: CoursesComponent },
      // { path: 'classes', component: ClassesComponent },
      // { path: 'attendance', component: AttendanceComponent },

      {
        path: '',
        redirectTo: 'home',
        pathMatch: 'full',
      },
    ],
  },

  /**
   * 📟 Classroom door terminal
   * No auth, no guards, system-level interaction
   */
  {
    path: 'classrooms/:id/terminal',
    component: ClassroomTerminalComponent,
  },

  {
    path: '**',
    redirectTo: 'login',
  },
];
