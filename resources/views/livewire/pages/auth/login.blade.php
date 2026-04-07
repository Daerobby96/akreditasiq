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
            <p class="text-xs text-slate-400">Belum memiliki akun? <a href="{{ route('register') }}" wire:navigate class="text-indigo-500 font-bold hover:underline">Halaman Registrasi</a></p>
        </div>
    </form>
</div>
