import { Component } from '@angular/core';
import { RouterLink, RouterOutlet } from '@angular/router';
import { ClassesService } from '../classes.service';

@Component({
  selector: 'app-classes-layout',
  imports: [RouterOutlet],
  providers: [ClassesService],
  templateUrl: './classes-layout.component.html',
  styleUrl: './classes-layout.component.scss',
})
export class ClassesLayout {}
