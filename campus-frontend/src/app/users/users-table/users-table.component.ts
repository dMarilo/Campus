import { Component, computed, inject, signal, effect } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { RouterLink } from '@angular/router';
import { UsersService, User } from '../users.service';

@Component({
  selector: 'app-users-table',
  imports: [FormsModule, RouterLink],
  templateUrl: './users-table.component.html',
  styleUrl: './users-table.component.scss',
})
export class UsersTable {
  usersService = inject(UsersService);

  users = computed(() => this.usersService.usersSignal() || []);
  searchTerm = '';
  filterType = '';
  isLoading = signal<boolean>(true);
  userToDelete: User | null = null;
  isDeleting = signal<boolean>(false);

  filteredUsers = computed(() => {
    let filtered = this.users();

    if (!filtered || !Array.isArray(filtered)) {
      return [];
    }

    if (this.searchTerm) {
      const term = this.searchTerm.toLowerCase();
      filtered = filtered.filter((u) => u.email.toLowerCase().includes(term));
    }

    if (this.filterType) {
      filtered = filtered.filter((u) => u.type === this.filterType);
    }

    return filtered;
  });

  constructor() {
    this.usersService.getUsers();

    effect(() => {
      const users = this.users();
      if (users) {
        this.isLoading.set(false);
      }
    });
  }

  applyFilters() {}

  deleteUser(user: User) {
    this.userToDelete = user;
  }

  cancelDelete() {
    this.userToDelete = null;
  }

  confirmDelete() {
    if (!this.userToDelete) return;
    this.isDeleting.set(true);
    this.usersService.deleteUser(this.userToDelete.id).subscribe({
      next: () => {
        this.usersService.getUsers();
        this.userToDelete = null;
        this.isDeleting.set(false);
      },
      error: (err) => {
        console.error('Error deleting user', err);
        this.isDeleting.set(false);
      },
    });
  }
}
