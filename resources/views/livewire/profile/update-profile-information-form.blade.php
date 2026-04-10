<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;

new class extends Component
{
    use WithFileUploads;

    public string $name = '';
    public string $email = '';
    public $avatar;
    public $existingAvatar;

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->existingAvatar = $user->avatar;
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            'avatar' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($this->avatar) {
            // Delete old avatar if exists
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $path = $this->avatar->store('avatars', 'public');
            $user->avatar = $path;
        }

        $user->name = $this->name;
        $user->email = $this->email;

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->existingAvatar = $user->avatar;
        $this->avatar = null;

        $this->dispatch('profile-updated', name: $user->name);
        $this->dispatch('notify', message: 'Profil berhasil diperbarui!', type: 'success');
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function sendVerification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
        $this->dispatch('notify', message: 'Link verifikasi telah dikirim!', type: 'info');
    }
}; ?>

<section>
    <header class="flex items-center space-x-4 mb-8">
        <div class="p-3 bg-indigo-100 dark:bg-indigo-900/30 rounded-2xl">
            <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
        </div>
        <div>
            <h2 class="text-xl font-bold text-slate-800 dark:text-slate-100">
                {{ __('Informasi Profil') }}
            </h2>
            <p class="text-sm text-slate-500 dark:text-slate-400">
                {{ __("Perbarui informasi akun dan alamat email Anda.") }}
            </p>
        </div>
    </header>

    <form wire:submit="updateProfileInformation" class="space-y-8">
        <!-- Avatar Section -->
        <div class="flex flex-col sm:flex-row items-center space-y-4 sm:space-y-0 sm:space-x-8 pb-8 border-b border-slate-100 dark:border-slate-800">
            <div class="relative group">
                <div class="w-32 h-32 rounded-3xl overflow-hidden ring-4 ring-white dark:ring-slate-800 shadow-xl transition-all duration-300 group-hover:ring-indigo-500/30">
                    @if ($avatar)
                        <img src="{{ $avatar->temporaryUrl() }}" class="w-full h-full object-cover">
                    @elseif ($existingAvatar)
                        <img src="{{ Storage::url($existingAvatar) }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-4xl font-bold">
                            {{ substr($name, 0, 1) }}
                        </div>
                    @endif
                    
                    <div wire:loading wire:target="avatar" class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm flex items-center justify-center">
                        <svg class="animate-spin h-8 w-8 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                </div>
                
                <label for="avatar" class="absolute -bottom-2 -right-2 p-2 bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-slate-100 dark:border-slate-700 cursor-pointer text-slate-600 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0017.07 7H18a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <input id="avatar" type="file" wire:model="avatar" class="hidden" accept="image/*">
                </label>
            </div>
            
            <div class="flex-1 text-center sm:text-left">
                <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100">Foto Profil</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 mb-3">Format JPG, PNG atau GIF. Maksimal 2MB.</p>
                <div class="flex flex-wrap justify-center sm:justify-start gap-2">
                    <button type="button" @click="$refs.avatar.click()" class="px-4 py-2 text-xs font-bold bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-900/50 transition-colors">
                        Ganti Foto
                    </button>
                    @if($existingAvatar || $avatar)
                    <button type="button" wire:click="$set('avatar', null); $set('existingAvatar', null)" class="px-4 py-2 text-xs font-bold bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 rounded-lg hover:bg-rose-100 dark:hover:bg-rose-900/50 transition-colors">
                        Hapus
                    </button>
                    @endif
                </div>
                <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2">
                <x-input-label for="name" :value="__('Nama Lengkap')" class="text-xs font-bold uppercase tracking-wider text-slate-500" />
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-slate-400 group-focus-within:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <input wire:model="name" id="name" name="name" type="text" class="block w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl ring-1 ring-slate-200 dark:ring-slate-800 focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-500 transition-all duration-300" required autofocus autocomplete="name" />
                </div>
                <x-input-error class="mt-2" :messages="$errors->get('name')" />
            </div>

            <div class="space-y-2">
                <x-input-label for="email" :value="__('Alamat Email')" class="text-xs font-bold uppercase tracking-wider text-slate-500" />
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-slate-400 group-focus-within:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <input wire:model="email" id="email" name="email" type="email" class="block w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl ring-1 ring-slate-200 dark:ring-slate-800 focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-500 transition-all duration-300" required autocomplete="username" />
                </div>
                <x-input-error class="mt-2" :messages="$errors->get('email')" />

                @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
                    <div class="mt-4 p-4 bg-amber-50 dark:bg-amber-900/20 rounded-2xl border border-amber-100 dark:border-amber-900/30">
                        <p class="text-sm text-amber-800 dark:text-amber-300 mb-2">
                            {{ __('Email Anda belum terverifikasi.') }}
                        </p>

                        <button wire:click.prevent="sendVerification" class="text-sm font-bold text-amber-600 dark:text-amber-400 hover:text-amber-700 dark:hover:text-amber-200 underline">
                            {{ __('Klik di sini untuk kirim ulang email verifikasi.') }}
                        </button>

                        @if (session('status') === 'verification-link-sent')
                            <p class="mt-2 font-medium text-xs text-emerald-600 dark:text-emerald-400">
                                {{ __('Link verifikasi baru telah dikirim.') }}
                            </p>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <div class="flex items-center justify-end pt-6 border-t border-slate-100 dark:border-slate-800">
            <button type="submit" class="relative overflow-hidden group px-8 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold rounded-2xl hover:translate-y-[-2px] transition-all duration-300 shadow-lg shadow-indigo-200 dark:shadow-indigo-900/20">
                <span class="relative z-10 flex items-center">
                    <svg wire:loading wire:target="updateProfileInformation" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ __('Simpan Perubahan') }}
                </span>
                <div class="absolute inset-0 bg-white/20 group-hover:translate-x-full transition-transform duration-700 -translate-x-full rotate-12"></div>
            </button>
        </div>
    </form>
</section>
