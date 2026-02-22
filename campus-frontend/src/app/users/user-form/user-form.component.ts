import { Component, OnInit, inject, signal } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { UsersService } from '../users.service';

@Component({
  selector: 'app-user-form',
  imports: [FormsModule, RouterLink],
  templateUrl: './user-form.component.html',
  styleUrl: './user-form.component.scss',
})
export class UserForm implements OnInit {
  private route = inject(ActivatedRoute);
  private router = inject(Router);
  private usersService = inject(UsersService);

  isEditMode = false;
  userId: number | null = null;
  isLoading = signal<boolean>(false);
  isSaving = signal<boolean>(false);
  errorMessage = '';
  successMessage = '';

  formData: any = {
    email: '',
    password: '',
    type: '',
    first_name: '',
    last_name: '',
    status: 'active',
  };

  ngOnInit() {
    const id = this.route.snapshot.paramMap.get('id');
    if (id) {
      this.isEditMode = true;
      this.userId = +id;
      this.loadUser(this.userId);
    }
  }

  loadUser(id: number) {
    this.isLoading.set(true);
    this.usersService.getUser(id).subscribe({
      next: (user) => {
        this.formData.email = user.email;
        this.formData.type = user.type;
        this.formData.status = user.status;
        this.isLoading.set(false);
      },
      error: (err) => {
        console.error('Error loading user', err);
        this.errorMessage = 'Failed to load user.';
        this.isLoading.set(false);
      },
    });
  }

  onSubmit() {
    this.errorMessage = '';
    this.successMessage = '';
    this.isSaving.set(true);

    if (this.isEditMode && this.userId) {
      const payload: any = {
        email: this.formData.email,
        type: this.formData.type,
        status: this.formData.status,
      };
      this.usersService.updateUser(this.userId, payload).subscribe({
        next: () => {
          this.successMessage = 'User updated successfully.';
          this.isSaving.set(false);
          setTimeout(() => this.router.navigate(['/users']), 1200);
        },
        error: (err) => {
          this.errorMessage = err?.error?.message || 'Failed to update user.';
          this.isSaving.set(false);
        },
      });
    } else {
      const payload = {
        email: this.formData.email,
        password: this.formData.password,
        type: this.formData.type,
        first_name: this.formData.first_name,
        last_name: this.formData.last_name,
      };
      this.usersService.createUser(payload).subscribe({
        next: () => {
          this.successMessage = 'User created. A verification email has been sent.';
          this.isSaving.set(false);
          setTimeout(() => this.router.navigate(['/users']), 1500);
        },
        error: (err) => {
          this.errorMessage = err?.error?.message || 'Failed to create user.';
          this.isSaving.set(false);
        },
      });
    }
  }
}
