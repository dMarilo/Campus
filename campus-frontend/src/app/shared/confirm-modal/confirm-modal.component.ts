import { Component, inject } from '@angular/core';
import { ConfirmModalService } from './confirm-modal.service';

@Component({
  selector: 'app-confirm-modal',
  imports: [],
  templateUrl: './confirm-modal.component.html',
  styleUrl: './confirm-modal.component.scss',
})
export class ConfirmModalComponent {
  modal = inject(ConfirmModalService);
}
