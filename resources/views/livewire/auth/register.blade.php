<div class="min-h-[60vh] flex items-center justify-center">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <h1 style="font-family: 'DM Serif Display', serif; font-size: 2.5rem; color: #2C1810; letter-spacing: -0.01em;">
                Create account
            </h1>
            <p class="mt-2 text-sm" style="color: #8B7355;">
                Already have an account?
                <a href="{{ route('login') }}" style="color: #E85D04;" onmouseenter="this.style.textDecoration='underline'" onmouseleave="this.style.textDecoration='none'">Sign in</a>
            </p>
        </div>

        <form wire:submit="register" class="space-y-5">
            <div>
                <label class="block text-sm font-medium mb-1.5" style="color: #2C1810;">Name</label>
                <input
                    wire:model="name"
                    type="text"
                    autocomplete="name"
                    class="w-full px-3.5 py-2.5 rounded-sm text-sm outline-none transition-all"
                    style="border: 1px solid #DDD5C5; background: #FEFCF9; color: #2C1810;"
                    onfocus="this.style.borderColor='#E85D04'; this.style.boxShadow='0 0 0 3px rgba(232,93,4,0.1)'"
                    onblur="this.style.borderColor='#DDD5C5'; this.style.boxShadow='none'"
                >
                @error('name') <p class="mt-1 text-xs" style="color: #C84B00;">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1.5" style="color: #2C1810;">Email</label>
                <input
                    wire:model="email"
                    type="email"
                    autocomplete="email"
                    class="w-full px-3.5 py-2.5 rounded-sm text-sm outline-none transition-all"
                    style="border: 1px solid #DDD5C5; background: #FEFCF9; color: #2C1810;"
                    onfocus="this.style.borderColor='#E85D04'; this.style.boxShadow='0 0 0 3px rgba(232,93,4,0.1)'"
                    onblur="this.style.borderColor='#DDD5C5'; this.style.boxShadow='none'"
                >
                @error('email') <p class="mt-1 text-xs" style="color: #C84B00;">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1.5" style="color: #2C1810;">Password</label>
                <input
                    wire:model="password"
                    type="password"
                    autocomplete="new-password"
                    class="w-full px-3.5 py-2.5 rounded-sm text-sm outline-none transition-all"
                    style="border: 1px solid #DDD5C5; background: #FEFCF9; color: #2C1810;"
                    onfocus="this.style.borderColor='#E85D04'; this.style.boxShadow='0 0 0 3px rgba(232,93,4,0.1)'"
                    onblur="this.style.borderColor='#DDD5C5'; this.style.boxShadow='none'"
                >
                @error('password') <p class="mt-1 text-xs" style="color: #C84B00;">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1.5" style="color: #2C1810;">Confirm Password</label>
                <input
                    wire:model="password_confirmation"
                    type="password"
                    autocomplete="new-password"
                    class="w-full px-3.5 py-2.5 rounded-sm text-sm outline-none transition-all"
                    style="border: 1px solid #DDD5C5; background: #FEFCF9; color: #2C1810;"
                    onfocus="this.style.borderColor='#E85D04'; this.style.boxShadow='0 0 0 3px rgba(232,93,4,0.1)'"
                    onblur="this.style.borderColor='#DDD5C5'; this.style.boxShadow='none'"
                >
            </div>

            <button
                type="submit"
                class="w-full py-2.5 px-4 rounded-sm text-sm font-medium transition-all"
                style="background: #E85D04; color: #ffffff;"
                onmouseenter="this.style.background='#C84B00'"
                onmouseleave="this.style.background='#E85D04'"
            >
                <span wire:loading.remove>Create account</span>
                <span wire:loading>Creating…</span>
            </button>
        </form>
    </div>
</div>
