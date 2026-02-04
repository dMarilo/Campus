import { Component, OnInit, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ActivatedRoute, Router, RouterModule } from '@angular/router';
import { HttpClient } from '@angular/common/http';
import { environment } from '../../../environments/environment';

@Component({
  selector: 'app-verify-email',
  standalone: true,
  imports: [CommonModule, RouterModule],
  templateUrl: './verify-email.component.html',
  styleUrls: ['./verify-email.component.scss']
})
export class VerifyEmailComponent implements OnInit {
  private http = inject(HttpClient);
  private route = inject(ActivatedRoute);
  private router = inject(Router);

  isVerifying = true;
  isSuccess = false;
  errorMessage = '';
  userEmail = '';

  ngOnInit() {
    const token = this.route.snapshot.queryParams['token'];

    if (!token) {
      this.isVerifying = false;
      this.errorMessage = 'No verification token provided.';
      return;
    }

    this.verifyEmail(token);
  }

  verifyEmail(token: string) {
  console.log('Verifying with token:', token); // Debug

  this.http.post(`${environment.apiBaseUrl}/auth/verify-email`, { token })
    .subscribe({
      next: (response: any) => {
        console.log('Verification response:', response); // Debug
        this.isVerifying = false;
        this.isSuccess = true;
        this.userEmail = response.data.email;

        // Redirect to set password page after 2 seconds
        setTimeout(() => {
          this.router.navigate(['/set-password'], {
            queryParams: { email: this.userEmail }
          });
        }, 2000);
      },
      error: (error) => {
        console.error('Verification error:', error); // Debug
        console.error('Verification error response:', error.error); // Debug
        this.isVerifying = false;
        this.isSuccess = false;
        this.errorMessage = error.error?.message || 'Email verification failed. The link may be invalid or expired.';
      }
    });
  }

  resendVerification() {
    if (!this.userEmail) {
      this.errorMessage = 'Cannot resend verification. Email not found.';
      return;
    }

    this.http.post(`${environment.apiBaseUrl}/auth/resend-verification`, {
      email: this.userEmail
    }).subscribe({
      next: () => {
        alert('Verification email sent! Please check your inbox.');
      },
      error: (error) => {
        alert('Failed to resend email: ' + (error.error?.message || 'Unknown error'));
      }
    });
  }
}
