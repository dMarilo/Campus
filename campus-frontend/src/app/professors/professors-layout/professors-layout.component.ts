import { Component } from '@angular/core';
import { RouterLink, RouterOutlet } from '@angular/router';
import { ProfessorsService } from '../professors.service';

@Component({
  selector: 'app-professors-layout',
  imports: [RouterLink, RouterOutlet],
  providers: [ProfessorsService],
  templateUrl: './professors-layout.component.html',
  styleUrl: './professors-layout.component.scss',
})
export class ProfessorsLayout {}
