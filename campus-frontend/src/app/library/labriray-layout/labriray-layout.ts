import { Component, inject } from '@angular/core';

import { RouterLink, RouterOutlet } from '@angular/router';
import { LibraryService } from '../library.service';
import { AuthService } from '../../auth/auth.service';

@Component({
  selector: 'app-labriray-layout',
  imports: [RouterLink, RouterOutlet],
  providers: [LibraryService],
  templateUrl: './labriray-layout.html',
  styleUrl: './labriray-layout.scss',
})
export class LabrirayLayout {
  authService = inject(AuthService);
  isAdmin = this.authService.isAdmin();
}
