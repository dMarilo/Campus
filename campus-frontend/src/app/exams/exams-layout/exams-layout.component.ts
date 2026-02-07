import { Component } from '@angular/core';
import { RouterOutlet } from '@angular/router';
import { ExamsService } from '../exams.service';

@Component({
  selector: 'app-exams-layout',
  imports: [RouterOutlet],
  providers: [ExamsService],
  templateUrl: './exams-layout.component.html',
  styleUrl: './exams-layout.component.scss',
})
export class ExamsLayout {}
