import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';

import { LayoutHeaderComponent } from '../layout-header/layout-header.component';
import { LayoutSidebarComponent } from '../layout-sidebar/layout-sidebar.component';

@Component({
  selector: 'app-page-layout',
  standalone: true,
  imports: [
    CommonModule,
    RouterModule,            // ✅ for <router-outlet>
    LayoutHeaderComponent,   // ✅ standalone import
    LayoutSidebarComponent,  // ✅ standalone import
  ],
  templateUrl: './page-layout.component.html',
  styleUrls: ['./page-layout.component.scss'],
})
export class PageLayoutComponent implements OnInit {

  sidebarOpen = false;

  toggleSidebar(): void {
    this.sidebarOpen = !this.sidebarOpen;
  }

  ngOnInit(): void {
    if (window.innerWidth >= 992) {
      this.sidebarOpen = true;
    }
  }
}
