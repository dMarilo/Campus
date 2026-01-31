import { Component } from '@angular/core';

import { RouterModule } from '@angular/router';
import { NgbDropdown, NgbDropdownMenu, NgbDropdownItem, NgbDropdownToggle } from '@ng-bootstrap/ng-bootstrap';



@Component({
  selector: 'app-sidebar',
  imports: [RouterModule, NgbDropdown, NgbDropdownMenu, NgbDropdownItem, NgbDropdownToggle],
  templateUrl: './sidebar.component.html',
  styleUrls: ['./sidebar.component.scss'],
})
export class SidebarComponent {}
