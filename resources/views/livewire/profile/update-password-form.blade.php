<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Volt\Component;

new class extends Component
{
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Update the password for the currently authenticated user.
     */
    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                'current_password' => ['required', 'string', 'current_password'],
                'password' => ['required', 'string', Password::defaults(), 'confirmed'],
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');

            throw $e;
        }

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');

        $this->dispatch('password-updated');
        $this->dispatch('notify', message: 'Password berhasil diubah!', type: 'success');
    }
}; ?>

<section>
    <header class="flex items-center space-x-4 mb-8">
        <div class="p-3 bg-rose-100 dark:bg-rose-900/30 rounded-2xl">
            <svg class="w-6 h-6 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
            </svg>
        </div>
        <div>
            <h2 class="text-xl font-bold text-slate-800 dark:text-slate-100">
                {{ __('Ubah Password') }}
            </h2>
            <p class="text-sm text-slate-500 dark:text-slate-400">
                {{ __('Pastikan akun Anda menggunakan password yang panjang dan acak untuk keamanan.') }}
            </p>
        </div>
    </header>

    <form wire:submit="updatePassword" class="space-y-6">
        <div class="space-y-2">
            <x-input-label for="update_password_current_password" :value="__('Password Saat Ini')" class="text-xs font-bold uppercase tracking-wider text-slate-500" />
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-slate-400 group-focus-within:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                    </svg>
                </div>
                <input wire:model="current_password" id="update_password_current_password" name="current_password" type="password" class="block w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl ring-1 ring-slate-200 dark:ring-slate-800 focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-500 transition-all duration-300" autocomplete="current-password" />
            </div>
            <x-input-error :messages="$errors->get('current_password')" class="mt-2" />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2">
                <x-input-label for="update_password_password" :value="__('Password Baru')" class="text-xs font-bold uppercase tracking-wider text-slate-500" />
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-slate-400 group-focus-within:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                    <input wire:model="password" id="update_password_password" name="password" type="password" class="block w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl ring-1 ring-slate-200 dark:ring-slate-800 focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-500 transition-all duration-300" autocomplete="new-password" />
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="space-y-2">
                <x-input-label for="update_password_password_confirmation" :value="__('Konfirmasi Password Baru')" class="text-xs font-bold uppercase tracking-wider text-slate-500" />
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-slate-400 group-focus-within:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <input wire:model="password_confirmation" id="update_password_password_confirmation" name="password_confirmation" type="password" class="block w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl ring-1 ring-slate-200 dark:ring-slate-800 focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-500 transition-all duration-300" autocomplete="new-password" />
                </div>
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>
        </div>

        <div class="flex items-center justify-end pt-6 border-t border-slate-100 dark:border-slate-800">
            <button type="submit" class="relative overflow-hidden group px-8 py-3 bg-gradient-to-r from-rose-500 to-pink-500 text-white font-bold rounded-2xl hover:translate-y-[-2px] transition-all duration-300 shadow-lg shadow-rose-200 dark:shadow-rose-900/20">
                <span class="relative z-10 flex items-center">
                    <svg wire:loading wire:target="updatePassword" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ __('Perbarui Password') }}
                </span>
                <div class="absolute inset-0 bg-white/20 group-hover:translate-x-full transition-transform duration-700 -translate-x-full rotate-12"></div>
            </button>
        </div>
    </form>
</section>
