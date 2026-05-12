{{-- resources/views/livewire/login.blade.php --}}
<div>
    <form wire:submit.prevent="login">
        <!-- Email Field -->
        <div class="input-group-custom">
            <div class="input-icon">
                <i class="fas fa-envelope"></i>
                <input 
                    type="email" 
                    wire:model="email" 
                    placeholder="Email Address"
                    autocomplete="email"
                    autofocus
                >
            </div>
            @error('email') 
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>{{ $message }}</span>
                </div>
            @enderror
        </div>

        <!-- Password Field -->
        <div class="input-group-custom">
            <div class="input-icon">
                <i class="fas fa-lock"></i>
                <input 
                    type="password" 
                    wire:model="password" 
                    placeholder="Password"
                    autocomplete="current-password"
                >
            </div>
            @error('password') 
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>{{ $message }}</span>
                </div>
            @enderror
        </div>

        <!-- Login Button with Loading State -->
        <button 
            type="submit" 
            class="btn-login"
            wire:loading.attr="disabled"
            wire:loading.class="loading"
        >
            <span wire:loading.remove>
                <i class="fas fa-sign-in-alt"></i>
                Login
            </span>
            <span wire:loading>
                <i class="fas fa-spinner"></i>
                Authenticating...
            </span>
        </button>
    </form>
</div>