<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Login extends Component
{
    public $email;
    public $password;

    protected $rules = [
        'email' => 'required|email',
        'password' => 'required'
    ];

    public function login()
    {
        $this->validate();

        if (!Auth::attempt([
            'email' => $this->email,
            'password' => $this->password,
            'status' => 'active'
        ])) {
            $this->addError('email', 'Invalid credentials or inactive user.');
            return;
        }

        // Regenerate session
        session()->regenerate();

        // Redirect based on role
        $user = Auth::user();

        if ($user->role->name === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role->name === 'hr') {
            return redirect()->route('hr.dashboard');
        }

        // fallback if role is missing or unknown
        Auth::logout();
        return redirect()->route('login')->with('error', 'Your account role is not recognized.');
    }

    public function render()
    {
        return view('livewire.auth.login')->layout('layouts.authdashboard'); // use a neutral login layout
    }
}
