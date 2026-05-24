<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Login extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $remember = false;
    public bool $failed = false;

    public function login(): void
    {
        $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $this->failed = false;

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            session()->regenerate();
            $this->redirectIntended(route('gallery'));
            return;
        }

        $this->failed = true;
        $this->password = '';
    }

    public function render()
    {
        return view('livewire.auth.login')->title('Sign In — Flashback Gallery');
    }
}
