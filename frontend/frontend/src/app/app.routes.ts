import { Routes } from '@angular/router';
import { LoginComponent } from './auth/login/login.component';
import { HomeComponent } from './home/home.component';
import { AuthGuard } from './auth/auth.guard';
import { GuestGuard } from './auth/guest.guard';
import { ClassroomTerminalComponent } from './classrooms/terminal/classroom-terminal.component';

export const routes: Routes = [
  {
    path: '',
    redirectTo: 'login',
    pathMatch: 'full',
  },

  {
    path: 'login',
    component: LoginComponent,
    canActivate: [GuestGuard],
  },

  {
    path: 'home',
    component: HomeComponent,
    canActivate: [AuthGuard],
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
