<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Logout extends Component
{
    public function mount()
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        redirect()->route('login');
    }

    public function render()
    {
        return view('livewire.auth.logout');
    }
}
