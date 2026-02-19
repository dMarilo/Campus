import { Component, OnInit, inject } from '@angular/core';
import { Router, RouterModule } from '@angular/router';
import { NgbDropdown, NgbDropdownMenu, NgbDropdownItem, NgbDropdownToggle } from '@ng-bootstrap/ng-bootstrap';
import { AuthService } from '../../auth/auth.service';

@Component({
  selector: 'app-sidebar',
  imports: [RouterModule, NgbDropdown, NgbDropdownMenu, NgbDropdownItem, NgbDropdownToggle],
  templateUrl: './sidebar.component.html',
  styleUrls: ['./sidebar.component.scss'],
})
export class SidebarComponent implements OnInit {
  authService = inject(AuthService);
  router = inject(Router);
  user: any = null;

  ngOnInit() {
    this.user = this.authService.getUser();
  }

  getUserAvatar(): string {
    const type = this.user?.type;
    const avatarMap: Record<string, string> = {
      student: 'assets/images/student.jpg',
      professor: 'assets/images/profesor.jpg',
      admin: 'assets/images/admin.jpg',
    };
    return avatarMap[type] || 'assets/images/student.jpg';
  }

  logout() {
    this.authService.logout();
  }
}
