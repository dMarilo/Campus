import { Component, inject } from '@angular/core';
import { RouterLink, RouterOutlet } from '@angular/router';
import { CoursesService } from '../courses.service';
import { AuthService } from '../../auth/auth.service';

@Component({
  selector: 'app-courses-layout',
  imports: [RouterLink, RouterOutlet],
  providers: [CoursesService],
  templateUrl: './courses-layout.component.html',
  styleUrl: './courses-layout.component.scss',
})
export class CoursesLayout {
  authService = inject(AuthService);
}
