import { Component, signal } from '@angular/core';
import { RouterOutlet } from '@angular/router';
import { ConfirmModalComponent } from './shared/confirm-modal/confirm-modal.component';

@Component({
  selector: 'app-root',
  imports: [RouterOutlet, ConfirmModalComponent],
  templateUrl: './app.html',
  styleUrl: './app.scss'
})
export class App {
  protected readonly title = signal('campus-frontend');
}
