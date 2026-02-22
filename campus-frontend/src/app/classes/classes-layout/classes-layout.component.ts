import { Component, inject } from '@angular/core';
import { RouterLink, RouterOutlet } from '@angular/router';
import { ClassesService } from '../classes.service';
import { AuthService } from '../../auth/auth.service';

@Component({
  selector: 'app-classes-layout',
  imports: [RouterLink, RouterOutlet],
  providers: [ClassesService],
  templateUrl: './classes-layout.component.html',
  styleUrl: './classes-layout.component.scss',
})
export class ClassesLayout {
  authService = inject(AuthService);
}
