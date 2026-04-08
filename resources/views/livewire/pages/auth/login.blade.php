<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="login" class="space-y-6">
        <!-- Email Address -->
        <div>
            <x-input-label for="email" class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-1.5 ml-1" :value="__('EMAIL INSTITUSI')" />
            <x-text-input wire:model="form.email" id="email" class="block mt-1 w-full px-4 py-3 bg-white/50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all text-slate-900 dark:text-white" type="email" name="email" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <div class="flex items-center justify-between mb-1.5 ml-1">
                <x-input-label for="password" class="text-xs font-bold text-slate-500 uppercase tracking-widest" :value="__('KATA SANDI')" />
                @if (Route::has('password.request'))
                    <a class="text-[10px] font-bold text-indigo-500 uppercase tracking-widest hover:text-indigo-600 transition-all" href="{{ route('password.request') }}" wire:navigate>
                        {{ __('Lupa?') }}
                    </a>
                @endif
            </div>
            <x-text-input wire:model="form.password" id="password" class="block mt-1 w-full px-4 py-3 bg-white/50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all text-slate-900 dark:text-white"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember" class="inline-flex items-center group cursor-pointer">
                <input id="remember" type="checkbox" class="rounded-md border-slate-300 dark:border-slate-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-slate-900" name="remember">
                <span class="ms-3 text-sm text-slate-500 dark:text-slate-400 group-hover:text-slate-700 transition-all">Sesi selalu aktif</span>
            </label>
        </div>

        <div class="flex flex-col items-center justify-center space-y-4">
            <x-primary-button class="w-full py-4 smart-gradient text-white font-extrabold rounded-2xl shadow-xl shadow-indigo-500/20 hover:shadow-indigo-500/40 transform hover:-translate-y-0.5 transition-all flex items-center justify-center">
                {{ __('MASUK KE SISTEM') }}
            </x-primary-button>

            <div class="relative w-full flex items-center justify-center py-2">
                <div class="absolute inset-0 flex items-center" aria-hidden="true">
                    <div class="w-full border-t border-slate-200 dark:border-slate-800"></div>
                </div>
                <div class="relative px-4 bg-white dark:bg-slate-900 overflow-hidden">
                    <span class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">ATAU LOGIN DENGAN</span>
                </div>
            </div>

            <a href="{{ route('auth.google') }}" class="w-full py-3.5 bg-white dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-800 rounded-2xl flex items-center justify-center space-x-3 hover:bg-slate-50 dark:hover:bg-slate-700 transition-all group">
                <svg class="w-5 h-5 group-hover:scale-110 transition-transform" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c3.12 0 5.73-1.03 7.64-2.8l-3.57-2.77c-.98.66-2.23 1.06-4.07 1.06-3.13 0-5.78-2.11-6.73-4.94H1.61v2.86C3.51 20.24 7.46 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.27 13.55c-.25-.76-.39-1.57-.39-2.42s.14-1.66.39-2.42V5.87H1.61A11.97 11.97 0 000 12c0 2.29.65 4.43 1.78 6.24l3.49-2.69z"/>
                    <path fill="#EA4335" d="M12 4.75c1.69 0 3.21.58 4.41 1.71l3.3-3.3C17.72 1.19 15.09 0 12 0 7.46 0 3.51 2.76 1.61 6.74L5.27 9.59c.95-2.83 3.6-4.84 12-4.84z"/>
                </svg>
                <span class="text-sm font-black text-slate-700 dark:text-slate-200 tracking-tight uppercase">Login Akun Google</span>
            </a>

            <p class="text-xs text-slate-400">Belum memiliki akun? <a href="{{ route('register') }}" wire:navigate class="text-indigo-500 font-bold hover:underline">Halaman Registrasi</a></p>
        </div>
    </form>
</div>
