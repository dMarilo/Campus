import { Component, EventEmitter, Output } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Router } from '@angular/router';

import { AuthService } from '../../auth/auth.service';
import { UserService } from '../../services/user.service';

@Component({
  selector: 'app-layout-header',
  standalone: true,
  imports: [
    CommonModule,
  ],
  templateUrl: './layout-header.component.html',
  styleUrls: ['./layout-header.component.scss'],
})
export class LayoutHeaderComponent {

  @Output() toggleSidebar = new EventEmitter<void>();
  userMenuOpen = false;

  constructor(
    private authService: AuthService,
    private userService: UserService,
    private router: Router,
  ) {}

  get user() {
    return this.userService.me();
  }

  onToggleSidebar(): void {
    this.toggleSidebar.emit();
  }

  toggleUserMenu(): void {
    this.userMenuOpen = !this.userMenuOpen;
  }

  closeUserMenu(): void {
    this.userMenuOpen = false;
  }

  logout(): void {
    this.authService.logout();
    this.router.navigateByUrl('/login');
  }

}

