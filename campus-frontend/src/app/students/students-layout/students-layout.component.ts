import { Component } from '@angular/core';
import { RouterLink, RouterOutlet } from '@angular/router';
import { StudentsService } from '../students.service';

@Component({
  selector: 'app-students-layout',
  imports: [RouterLink, RouterOutlet],
  providers: [StudentsService],
  templateUrl: './students-layout.component.html',
  styleUrl: './students-layout.component.scss',
})
export class StudentsLayout {}
