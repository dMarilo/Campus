import { Component, inject } from '@angular/core';
import { SessionsService, Session } from '../sessions.service';
import { ActivatedRoute, RouterLink } from '@angular/router';
import { DatePipe, TitleCasePipe } from '@angular/common';
import { toSignal } from '@angular/core/rxjs-interop';

@Component({
  selector: 'app-sessions-preview',
  imports: [RouterLink, DatePipe, TitleCasePipe],
  templateUrl: './sessions-preview.component.html',
  styleUrl: './sessions-preview.component.scss',
})
export class SessionsPreview {
  sessionsService = inject(SessionsService);
  activatedRoute = inject(ActivatedRoute);

  session = toSignal(
    this.sessionsService.getSession(
      Number(this.activatedRoute.snapshot.params['id'])
    )
  );

  get presentCount(): number {
    return this.session()?.students?.filter((s) => s.checked_in).length ?? 0;
  }

  get absentCount(): number {
    return (this.session()?.students?.length ?? 0) - this.presentCount;
  }
}
