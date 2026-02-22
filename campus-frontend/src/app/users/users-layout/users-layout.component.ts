import { Component } from '@angular/core';
import { RouterLink, RouterOutlet } from '@angular/router';
import { UsersService } from '../users.service';

@Component({
  selector: 'app-users-layout',
  imports: [RouterLink, RouterOutlet],
  providers: [UsersService],
  templateUrl: './users-layout.component.html',
  styleUrl: './users-layout.component.scss',
})
export class UsersLayout {}
