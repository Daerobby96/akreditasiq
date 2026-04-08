<?php

namespace App\Livewire;

use App\Models\User;
use App\Models\Prodi;
use App\Mail\TeamInvitation;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class TeamManagement extends Component
{
    public $members;
    public $prodi;
    public $newEmail = '';
    public $newName = '';
    public $newRole = 'member';
    public $sendEmail = true;
    public $generatedPassword = null;
    public $roles = [
        'admin' => 'Administrator',
        'editor' => 'Editor / Penulis',
        'auditor' => 'Auditor Internal',
        'viewer' => 'Viewer'
    ];

    public function mount()
    {
        $prodiId = session('selected_prodi_id');
        $this->prodi = Prodi::find($prodiId) ?? auth()->user()->prodi ?? Prodi::first();
        $this->loadMembers();
    }

    public function loadMembers()
    {
        $this->members = User::where('prodi_id', $this->prodi->id)->get();
    }

    public function invite()
    {
        $this->validate([
            'newEmail' => 'required|email'
        ]);

        $user = User::where('email', $this->newEmail)->first();

        if (!$user) {
            // User not found, try to create new user
            $this->validate([
                'newName' => 'required|min:3'
            ]);

            // Generate random password
            $randomPassword = \Illuminate\Support\Str::random(10);
            
            $user = User::create([
                'name' => $this->newName,
                'email' => $this->newEmail,
                'password' => \Illuminate\Support\Facades\Hash::make($randomPassword),
                'role' => $this->newRole,
                'prodi_id' => $this->prodi->id
            ]);

            // Store password to display to admin
            $this->generatedPassword = $randomPassword;

            // Send Email Invitation
            if ($this->sendEmail) {
                Mail::to($user->email)->send(new TeamInvitation($user, $randomPassword, $this->prodi->nama));
            }

            $this->newName = '';
            $this->newEmail = '';
            $this->loadMembers();
            $this->dispatch('notify', message: "User baru berhasil dibuat untuk {$user->name}!", type: 'success');
            return;
        }

        // if user exists
        if ($user->prodi_id == $this->prodi->id) {
            $this->dispatch('notify', message: 'User sudah menjadi anggota tim ini.', type: 'info');
            return;
        }

        $user->update([
            'prodi_id' => $this->prodi->id,
            'role' => $this->newRole
        ]);

        $this->newEmail = '';
        $this->newName = '';
        $this->generatedPassword = null;
        $this->loadMembers();
        $this->dispatch('notify', message: "{$user->name} berhasil ditambahkan ke tim!", type: 'success');
    }

    public function closePasswordAlert()
    {
        $this->generatedPassword = null;
    }

    public function updateRole($userId, $role)
    {
        User::find($userId)->update(['role' => $role]);
        $this->loadMembers();
        $this->dispatch('notify', message: 'Role berhasil diperbarui!', type: 'success');
    }

    public function remove($userId)
    {
        $user = User::find($userId);
        
        if ($user->id == auth()->id()) {
            $this->dispatch('notify', message: 'Anda tidak bisa menghapus diri sendiri dari tim.', type: 'error');
            return;
        }

        $user->update(['prodi_id' => null]);
        $this->loadMembers();
        $this->dispatch('notify', message: 'Anggota berhasil dihapus dari tim.', type: 'success');
    }

    public function render()
    {
        return view('livewire.team-management')->layout('layouts.app');
    }
}
