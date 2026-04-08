<x-mail::message>
# Halo {{ $user->name }},

Anda telah diundang untuk bergabung dalam **Tim Akreditasi Program Studi {{ $prodiName }}**. 

Sistem kami telah membuatkan akun untuk Anda agar dapat mulai berkolaborasi dalam pengisian dokumen akreditasi (LED & LKPS).

Berikut adalah informasi login Anda:

- **Email:** {{ $user->email }}
- **Password Sementara:** `{{ $password }}`

<x-mail::button :url="$url">
Login ke Dashboard
</x-mail::button>

*Catatan: Demi keamanan, silakan segera ganti password Anda setelah login pertama kali melalui menu Profile.*

Terima kasih,<br>
Tim Penjaminan Mutu Internal
</x-mail::message>
