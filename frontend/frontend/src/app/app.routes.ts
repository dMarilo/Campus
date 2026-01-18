import { Routes } from '@angular/router';
import { LoginComponent } from './auth/login/login.component';
import { HomeComponent } from './home/home.component';
import { AuthGuard } from './auth/auth.guard';
import { GuestGuard } from './auth/guest.guard';

export const routes: Routes = [
  {
    path: '',
    redirectTo: 'login',
    pathMatch: 'full',
  },

  {
    path: 'login',
    component: LoginComponent,
    canActivate: [GuestGuard], // 👈 BLOCK when logged in
  },

  {
    path: 'home',
    component: HomeComponent,
    canActivate: [AuthGuard], // 👈 PROTECTED
  },

  {
    path: '**',
    redirectTo: 'login',
  },
];
