import { Component, inject } from '@angular/core';
import { ProfessorsService, Professor } from '../professors.service';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { DatePipe, TitleCasePipe } from '@angular/common';
import { toSignal } from '@angular/core/rxjs-interop';

@Component({
  selector: 'app-professor-preview',
  imports: [RouterLink, DatePipe, TitleCasePipe],
  templateUrl: './professors-preview.component.html',
  styleUrl: './professors-preview.component.scss',
})
export class ProfessorsPreview {
  professorsService = inject(ProfessorsService);
  activatedRoute = inject(ActivatedRoute);
  router = inject(Router);

  professor = toSignal(
    this.professorsService.getProfessor(
      Number(this.activatedRoute.snapshot.params['id'])
    )
  );

  formatEmploymentType(type: string): string {
    return type.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
  }
}
