<div class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="w-full max-w-md p-8 bg-white rounded-lg shadow-lg">
        <h2 class="mb-6 text-2xl font-bold text-center text-gray-800">Login</h2>

        <form wire:submit.prevent="login">
            <!-- Username -->
            <div class="mb-4">
                <label for="username" class="block mb-2 font-medium text-gray-700">Username</label>
                <input type="text" wire:model.defer="username" id="username" autofocus
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                @error('username')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div class="mb-6">
                <label for="password" class="block mb-2 font-medium text-gray-700">Password</label>
                <input type="password" wire:model.defer="password" id="password"
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                @error('password')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Error login -->
            @error('login')
                <p class="mb-4 text-xs text-red-600">{{ $message }}</p>
            @enderror

            <!-- Submit -->
            <button type="submit"
                class="w-full px-4 py-2 font-semibold text-white transition duration-200 bg-indigo-600 rounded-lg hover:bg-indigo-700">
                Login
            </button>
        </form>

        <p class="mt-4 text-center text-gray-600">
            Belum punya akun? <a href="/register" class="text-indigo-600 hover:underline">Daftar</a>
        </p>
    </div>
</div>
