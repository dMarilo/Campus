import { Routes } from '@angular/router';

import { LoginComponent } from './auth/login/login.component';
import { ClassroomTerminalComponent } from './classrooms/terminal/classroom-terminal.component';

import { AuthGuard } from './auth/auth.guard';
import { GuestGuard } from './auth/guest.guard';

export const routes: Routes = [

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
