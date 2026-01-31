import { Component } from '@angular/core';

import { RouterLink, RouterOutlet } from '@angular/router';
import {LibraryService} from '../library.service'

@Component({
  selector: 'app-labriray-layout',
  imports: [RouterLink, RouterOutlet],
  providers:[LibraryService],
  templateUrl: './labriray-layout.html',
  styleUrl: './labriray-layout.scss',
})
export class LabrirayLayout {

}
