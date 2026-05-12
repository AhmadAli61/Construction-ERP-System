<div class="container mt-5" style="max-width:500px">
    <form wire:submit.prevent="save">

        <input type="text" wire:model="name" class="form-control mb-2" placeholder="Name">

        <input type="email" wire:model="email" class="form-control mb-2" placeholder="Email">

        <input type="password" wire:model="password" class="form-control mb-2" placeholder="Password">

        <select wire:model="role_id" class="form-control mb-2">
            <option value="">Select Role</option>
            @foreach($roles as $role)
                <option value="{{ $role->id }}">{{ $role->name }}</option>
            @endforeach
        </select>

        <button class="btn btn-success w-100">
            Create User
        </button>

    </form>
</div>
