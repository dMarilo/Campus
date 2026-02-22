import { Injectable, signal } from '@angular/core';

@Injectable({ providedIn: 'root' })
export class ThemeService {
  private readonly STORAGE_KEY = 'campus_theme';

  isDark = signal<boolean>(false);

  constructor() {
    const saved = localStorage.getItem(this.STORAGE_KEY);
    if (saved === 'dark') {
      this.apply(true);
    }
  }

  toggle() {
    this.apply(!this.isDark());
  }

  private apply(dark: boolean) {
    this.isDark.set(dark);
    document.documentElement.setAttribute('data-bs-theme', dark ? 'dark' : 'light');
    localStorage.setItem(this.STORAGE_KEY, dark ? 'dark' : 'light');
  }
}
