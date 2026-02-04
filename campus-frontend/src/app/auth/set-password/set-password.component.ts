import { Component, OnInit, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, Validators, ReactiveFormsModule } from '@angular/forms';
import { Router, ActivatedRoute, RouterModule } from '@angular/router';
import { HttpClient } from '@angular/common/http';
import { environment } from '../../../environments/environment';

@Component({
  selector: 'app-set-password',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, RouterModule],
  templateUrl: './set-password.component.html',
  styleUrls: ['./set-password.component.scss']
})
export class SetPasswordComponent implements OnInit {
  private fb = inject(FormBuilder);
  private http = inject(HttpClient);
  private router = inject(Router);
  private route = inject(ActivatedRoute);

  formGroup = this.fb.group({
    email: ['', [Validators.required, Validators.email]],
    password: ['', [Validators.required, Validators.minLength(8)]],
    password_confirmation: ['', [Validators.required]]
  });

  isLoading = false;
  errorMessage = '';
  showPassword = false;
  showConfirmPassword = false;

  ngOnInit() {
    const email = this.route.snapshot.queryParams['email'];
    if (email) {
      this.formGroup.patchValue({ email });
    }
  }

  get passwordsMatch(): boolean {
    const password = this.formGroup.get('password')?.value;
    const confirmation = this.formGroup.get('password_confirmation')?.value;
    return password === confirmation;
  }

  setPassword() {
  if (this.formGroup.invalid) {
    this.formGroup.markAllAsTouched();
    return;
  }

  if (!this.passwordsMatch) {
    this.errorMessage = 'Passwords do not match';
    return;
  }

  this.isLoading = true;
  this.errorMessage = '';

  console.log('Sending data:', this.formGroup.value); // Debug log

  this.http.post(`${environment.apiBaseUrl}/auth/set-password`, this.formGroup.value)
    .subscribe({
      next: () => {
        alert('Password set successfully! You can now log in with your new password.');
        this.router.navigate(['/login']);
      },
      error: (error) => {
        console.error('Full error:', error); // Debug log
        console.error('Error response:', error.error); // Debug log
        this.errorMessage = error.error?.message || 'Failed to set password. Please try again.';
        this.isLoading = false;
      }
    });
  }
}
