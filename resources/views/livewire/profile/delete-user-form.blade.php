<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component
{
    public string $password = '';

    /**
     * Delete the currently authenticated user.
     */
    public function deleteUser(Logout $logout): void
    {
        $this->validate([
            'password' => ['required', 'string', 'current_password'],
        ]);

        tap(Auth::user(), $logout(...))->delete();

        $this->redirect('/', navigate: true);
    }
}; ?>

<section class="space-y-6">
    <header class="flex items-center space-x-4 mb-8">
        <div class="p-3 bg-red-100 dark:bg-red-900/30 rounded-2xl">
            <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
            </svg>
        </div>
        <div>
            <h2 class="text-xl font-bold text-slate-800 dark:text-slate-100">
                {{ __('Hapus Akun') }}
            </h2>
            <p class="text-sm text-slate-500 dark:text-slate-400">
                {{ __('Setelah akun Anda dihapus, semua sumber daya dan datanya akan dihapus secara permanen.') }}
            </p>
        </div>
    </header>

    <div class="p-4 bg-red-50 dark:bg-red-900/20 rounded-2xl border border-red-100 dark:border-red-900/30">
        <p class="text-sm text-red-800 dark:text-red-300">
            {{ __('Sebelum menghapus akun Anda, harap unduh data atau informasi apa pun yang ingin Anda simpan.') }}
        </p>

        <x-danger-button
            class="mt-4 px-6 py-2.5 rounded-xl shadow-lg shadow-red-200 dark:shadow-red-900/20"
            x-data=""
            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        >{{ __('Hapus Akun Permanen') }}</x-danger-button>
    </div>

    <x-modal name="confirm-user-deletion" :show="$errors->isNotEmpty()" focusable>
        <form wire:submit="deleteUser" class="p-8">
            <h2 class="text-2xl font-bold text-slate-800 dark:text-slate-100 mb-2">
                {{ __('Apakah Anda yakin ingin menghapus akun?') }}
            </h2>

            <p class="text-slate-600 dark:text-slate-400 mb-6">
                {{ __('Setelah akun Anda dihapus, semua sumber daya dan datanya akan dihapus secara permanen. Harap masukkan kata sandi Anda untuk mengonfirmasi bahwa Anda ingin menghapus akun Anda secara permanen.') }}
            </p>

            <div class="space-y-2">
                <x-input-label for="password" value="{{ __('Password') }}" class="sr-only" />

                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-slate-400 group-focus-within:text-red-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                    <input
                        wire:model="password"
                        id="password"
                        name="password"
                        type="password"
                        class="block w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl ring-1 ring-slate-200 dark:ring-slate-800 focus:ring-2 focus:ring-red-500 dark:focus:ring-red-500 transition-all duration-300"
                        placeholder="{{ __('Masukkan Password Anda') }}"
                    />
                </div>

                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="mt-8 flex justify-end space-x-3">
                <button type="button" x-on:click="$dispatch('close')" class="px-6 py-3 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold rounded-2xl hover:bg-slate-200 dark:hover:bg-slate-700 transition-all duration-300">
                    {{ __('Batal') }}
                </button>

                <button type="submit" class="px-6 py-3 bg-red-600 text-white font-bold rounded-2xl hover:bg-red-700 transition-all duration-300 shadow-lg shadow-red-200 dark:shadow-red-900/20">
                    {{ __('Hapus Akun') }}
                </button>
            </div>
        </form>
    </x-modal>
</section>
