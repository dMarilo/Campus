import { Injectable, signal } from '@angular/core';

export interface ConfirmModalOptions {
  title: string;
  message: string;
  itemName: string;
  confirmLabel?: string;
}

interface ModalState extends ConfirmModalOptions {
  open: boolean;
  resolve: ((result: boolean) => void) | null;
}

@Injectable({ providedIn: 'root' })
export class ConfirmModalService {
  private _state = signal<ModalState>({
    open: false,
    title: '',
    message: '',
    itemName: '',
    confirmLabel: 'Delete',
    resolve: null,
  });

  state = this._state.asReadonly();

  confirm(options: ConfirmModalOptions): Promise<boolean> {
    return new Promise((resolve) => {
      this._state.set({
        open: true,
        title: options.title,
        message: options.message,
        itemName: options.itemName,
        confirmLabel: options.confirmLabel ?? 'Delete',
        resolve,
      });
    });
  }

  respond(result: boolean) {
    const { resolve } = this._state();
    this._state.update((s) => ({ ...s, open: false, resolve: null }));
    resolve?.(result);
  }
}
