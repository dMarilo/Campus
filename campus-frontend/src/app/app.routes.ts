import { Routes } from '@angular/router';

import { LoginComponent } from './auth/login/login.component';
import { VerifyEmailComponent } from './auth/verify-email/verify-email.component';
import { SetPasswordComponent } from './auth/set-password/set-password.component';
import { ClassroomTerminalComponent } from './classrooms/terminal/classroom-terminal.component';
import { BorrowingTerminalComponent } from './library/borrowing-terminal/borrowing-terminal.component';

import { authGuard } from './auth/auth.guard';
import { loginGuard } from './auth/login.guard';
import { adminGuard } from './auth/admin.guard';
import { LayoutComponent } from './main-layout/layout/layout.component';
import { DashboardComponent } from './dashboard/dashboard.component';
import { LabrirayLayout } from './library/labriray-layout/labriray-layout';
import { AddBook } from './library/add-book/add-book';
import { BookPreview } from './library/book-preview/book-preview';
import { LabrirayTable } from './library/labriray-table/labriray-table';
import { StudentsLayout } from './students/students-layout/students-layout.component';
import { StudentsTable } from './students/students-table/students-table.component';
import { StudentPreview } from './students/student-preview/student-preview.component';
import { CoursesLayout } from './courses/courses-layout/courses-layout.component';
import { CoursesPreview } from './courses/courses-preview/courses-preview.component';
import { CoursesTable } from './courses/courses-table/courses-table.component';
import { CourseForm } from './courses/course-form/course-form.component';
import { ProfessorsLayout } from './professors/professors-layout/professors-layout.component';
import { ProfessorsTable } from './professors/professors-table/professors-table.component';
import { ProfessorsPreview } from './professors/professors-preview/professors-preview.component';
import { ClassesLayout } from './classes/classes-layout/classes-layout.component';
import { ClassesTable } from './classes/classes-table/classes-table.component';
import { ClassesPreview } from './classes/classes-preview/classes-preview.component';
import { ClassForm } from './classes/class-form/class-form.component';
import { ExamsLayout } from './exams/exams-layout/exams-layout.component';
import { ExamsTable } from './exams/exams-table/exams-table.component';
import { ExamsPreview } from './exams/exams-preview/exams-preview.component';
import { ProfileLayout } from './profile/profile-layout/profile-layout.component';
import { ProfilePreview } from './profile/profile-preview/profile-preview.component';
import { SessionsLayout } from './sessions/sessions-layout/sessions-layout.component';
import { SessionsTable } from './sessions/sessions-table/sessions-table.component';
import { SessionsPreview } from './sessions/sessions-preview/sessions-preview.component';
import { UsersLayout } from './users/users-layout/users-layout.component';
import { UsersTable } from './users/users-table/users-table.component';
import { UserForm } from './users/user-form/user-form.component';
import { GuideComponent } from './guide/guide.component';

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
          { path: 'add-book', component: AddBook, canActivate: [adminGuard] },
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
      {
        path: 'courses',
        component: CoursesLayout,
        children: [
          { path: '', component: CoursesTable },
          { path: 'add', component: CourseForm, canActivate: [adminGuard] },
          { path: 'edit/:id', component: CourseForm, canActivate: [adminGuard] },
          { path: ':id', component: CoursesPreview },
        ]
      },
      { path: 'dorm', component: LabrirayLayout },
      {
        path: 'classes',
        component: ClassesLayout,
        children: [
          { path: '', component: ClassesTable },
          { path: 'add', component: ClassForm, canActivate: [adminGuard] },
          { path: ':id', component: ClassesPreview },
        ],
      },
      { path: 'students', component: LabrirayLayout },
            {
        path: 'professors',
        component: ProfessorsLayout,
        children: [
          { path: '', component: ProfessorsTable },
          { path: ':id', component: ProfessorsPreview },
        ],
      },
      {
        path: 'exams',
        component: ExamsLayout,
        children: [
          { path: '', component: ExamsTable },
          { path: ':id', component: ExamsPreview },
        ],
      },
      {
        path: 'sessions',
        component: SessionsLayout,
        canActivate: [adminGuard],
        children: [
          { path: '', component: SessionsTable },
          { path: ':id', component: SessionsPreview },
        ],
      },
      {
        path: 'users',
        component: UsersLayout,
        canActivate: [adminGuard],
        children: [
          { path: '', component: UsersTable },
          { path: 'add', component: UserForm },
          { path: 'edit/:id', component: UserForm },
        ],
      },
      {
        path: 'profile',
        component: ProfileLayout,
        children: [
          { path: '', component: ProfilePreview },
        ],
      },
      {
        path: 'guide',
        component: GuideComponent,
      },
    ],
  },

  // Classroom terminal (protected)
  {
    path: 'classrooms/:id/terminal',
    component: ClassroomTerminalComponent,
    canActivate: [authGuard],
  },

  // Borrowing terminal (public - no authentication required)
  {
    path: 'library/terminal',
    component: BorrowingTerminalComponent,
  },

  // Wildcard - redirect to login
  {
    path: '**',
    redirectTo: 'login',
  },
];
