import { Component } from '@angular/core';
import { FormBuilder, Validators, ReactiveFormsModule } from '@angular/forms';
import { Router, RouterModule, ActivatedRoute } from '@angular/router';
import { CommonModule } from '@angular/common';
import { AuthService } from '../auth.service';

@Component({
  selector: 'app-login',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, RouterModule],
  templateUrl: './login.component.html',
  styleUrl: './login.component.scss',
})
export class LoginComponent {
  formGroup;
  isLoading = false;
  errorMessage: string | null = null;
  showPassword = false;
  returnUrl: string;

  constructor(
    private fb: FormBuilder,
    private auth: AuthService,
    private router: Router,
    private route: ActivatedRoute
  ) {
    this.formGroup = this.fb.group({
      email: ['', [Validators.required, Validators.email]],
      password: ['', [Validators.required]],
    });

    // Get return URL from route parameters or default to '/dashboard'
    this.returnUrl = this.route.snapshot.queryParams['returnUrl'] || '/dashboard';
  }

  authenticate(): void {
    if (this.formGroup.invalid) {
      this.formGroup.markAllAsTouched();
      return;
    }

    this.isLoading = true;
    this.errorMessage = null;

    const { email, password } = this.formGroup.value;

    this.auth.login(email!, password!).subscribe({
      next: (response) => {
        console.log('Login successful:', response); // ✅ Debug log
        this.isLoading = false;

        // Navigate immediately
        this.router.navigateByUrl(this.returnUrl, { replaceUrl: true }).then(() => {
          console.log('Navigation complete'); // ✅ Debug log
        });
      },
      error: (error) => {
        console.error('Login error:', error);
        this.errorMessage = error.error?.message || 'Invalid email or password';
        this.isLoading = false;
      },
    });
  }
}
