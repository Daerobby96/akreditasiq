<?php

namespace App\Livewire;

use App\Models\Setting;
use Livewire\Component;
use Livewire\WithFileUploads;

class Settings extends Component
{
    use WithFileUploads;

    public $nama_institusi;
    public $alamat;
    public $kota;
    public $website;
    public $email;
    public $rektor_nama;
    public $rektor_nip;
    public $logo;
    public $logo_path;

    public function mount()
    {
        $setting = Setting::first();
        if ($setting) {
            $this->nama_institusi = $setting->nama_institusi;
            $this->alamat = $setting->alamat;
            $this->kota = $setting->kota;
            $this->website = $setting->website;
            $this->email = $setting->email;
            $this->rektor_nama = $setting->rektor_nama;
            $this->rektor_nip = $setting->rektor_nip;
            $this->logo_path = $setting->logo_path;
        }
    }

    public function save()
    {
        $this->validate([
            'nama_institusi' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'kota' => 'nullable|string',
            'website' => 'nullable|url',
            'email' => 'nullable|email',
            'logo' => 'nullable|image|max:1024', // 1MB Max
        ]);

        $setting = Setting::first() ?? new Setting();
        
        $data = [
            'nama_institusi' => $this->nama_institusi,
            'alamat' => $this->alamat,
            'kota' => $this->kota,
            'website' => $this->website,
            'email' => $this->email,
            'rektor_nama' => $this->rektor_nama,
            'rektor_nip' => $this->rektor_nip,
        ];

        if ($this->logo) {
            $data['logo_path'] = $this->logo->store('logos', 'public');
        }

        $setting->fill($data)->save();

        session()->flash('message', 'Pengaturan berhasil diperbarui.');
    }

    public function render()
    {
        return view('livewire.settings');
    }
}
