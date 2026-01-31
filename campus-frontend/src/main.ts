import { bootstrapApplication } from '@angular/platform-browser';
import { provideZonelessChangeDetection } from '@angular/core';
import { provideHttpClient, withInterceptors } from '@angular/common/http';
import { provideRouter } from '@angular/router';

import { App } from './app/app';
import { routes } from './app/app.routes';
import { authInterceptor } from './app/auth/auth.interceptor';

bootstrapApplication(App, {
  providers: [
    provideZonelessChangeDetection(), // ✅ CORRECT for Angular 20
    provideHttpClient(withInterceptors([authInterceptor])),
    provideRouter(routes),
  ],
});
