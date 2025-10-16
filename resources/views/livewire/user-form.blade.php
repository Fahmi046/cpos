<div class="p-4 mb-4 bg-white rounded-md shadow">
    <h2 class="mb-3 text-base font-semibold text-gray-700">Master User</h2>

    <form wire:submit.prevent="save" class="space-y-3 text-sm">
        <!-- Semua input di 1 baris -->
        <div class="grid grid-cols-1 gap-3 md:grid-cols-4">
            <div>
                <label for="name" class="block mb-1">Nama</label>
                <input x-ref="name" @keydown.enter.prevent="$refs.username.focus()" type="text" id="name"
                    wire:model="name" autofocus
                    class="w-full px-2 py-1 border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500" />
                @error('name')
                    <p class="text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="username" class="block mb-1">Username</label>
                <input x-ref="username" @keydown.enter.prevent="$refs.email.focus()" type="text" id="username"
                    wire:model="username"
                    class="w-full px-2 py-1 border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500" />
                @error('username')
                    <p class="text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block mb-1">Email</label>
                <input x-ref="email" @keydown.enter.prevent="$refs.password.focus()" type="email" id="email"
                    wire:model="email"
                    class="w-full px-2 py-1 border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500" />
                @error('email')
                    <p class="text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block mb-1">Password</label>
                <input x-ref="password" @keydown.enter.prevent="$refs.role.focus()" type="password" id="password"
                    wire:model="password"
                    class="w-full px-2 py-1 border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500" />
                <small class="text-xs text-gray-500">Kosongkan jika tidak ingin mengubah password</small>
                @error('password')
                    <p class="text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Baris 2: Role dan Status -->
        <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
            <div>
                <label for="role" class="block mb-1">Role</label>
                <select x-ref="role" wire:model="role"
                    class="w-full px-2 py-1 border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                    <option value="outlet">Outlet</option>
                    <option value="gudang">Gudang</option>
                    <option value="admin">Admin</option>
                </select>
                @error('role')
                    <p class="text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block mb-1">Status</label>
                <select x-ref="aktif" wire:model="aktif"
                    class="w-full px-2 py-1 border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                    <option value="1">Aktif</option>
                    <option value="0">Tidak Aktif</option>
                </select>
            </div>
        </div>

        <!-- Tombol Simpan -->
        <div class="mt-3">
            <button type="submit"
                class="w-full px-4 py-2 text-sm text-white bg-blue-600 rounded hover:bg-blue-700 md:w-auto">
                Simpan
            </button>
        </div>
    </form>
</div>
