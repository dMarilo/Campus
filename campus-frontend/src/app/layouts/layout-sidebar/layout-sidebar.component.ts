import { Component, Input, Output, EventEmitter } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';

@Component({
  selector: 'app-layout-sidebar',
  standalone: true,
  imports: [
    CommonModule,
    RouterModule,
  ],
  templateUrl: './layout-sidebar.component.html',
  styleUrls: ['./layout-sidebar.component.scss'],
})
export class LayoutSidebarComponent {
  @Input() isOpen = false;
  @Output() toggleSidebar = new EventEmitter<void>();
}
