import { Component } from '@angular/core';
import { Router, RouterOutlet } from '@angular/router';
import { SidebarComponent } from '../sidebar/sidebar.component';

@Component({
    selector: 'app-layout',
    templateUrl: './layout.component.html',
    imports: [SidebarComponent, RouterOutlet],
    styleUrls: ['./layout.component.scss']
})
export class LayoutComponent {
    constructor(private router: Router) {}
}