<div class="p-4 mb-4 bg-white rounded-md shadow">
    <h2 class="mb-3 text-base font-semibold text-gray-700">Master User</h2>

    <form wire:submit.prevent="save" class="space-y-4 text-sm">
        <!-- Baris 1 -->
        <div class="grid grid-cols-1 gap-3 md:grid-cols-4">
            <!-- Nama -->
            <div>
                <label for="name" class="block mb-1 text-gray-700">Nama</label>
                <input x-ref="name" @keydown.enter.prevent="$refs.username.focus()" type="text" id="name"
                    wire:model="name" autofocus
                    class="w-full px-3 py-2 text-sm transition duration-150 ease-in-out bg-white border border-gray-300 rounded-md outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                @error('name')
                    <p class="text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Username -->
            <div>
                <label for="username" class="block mb-1 text-gray-700">Username</label>
                <input x-ref="username" @keydown.enter.prevent="$refs.email.focus()" type="text" id="username"
                    wire:model="username"
                    class="w-full px-3 py-2 text-sm transition duration-150 ease-in-out bg-white border border-gray-300 rounded-md outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                @error('username')
                    <p class="text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block mb-1 text-gray-700">Email</label>
                <input x-ref="email" @keydown.enter.prevent="$refs.password.focus()" type="email" id="email"
                    wire:model="email"
                    class="w-full px-3 py-2 text-sm transition duration-150 ease-in-out bg-white border border-gray-300 rounded-md outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                @error('email')
                    <p class="text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block mb-1 text-gray-700">Password</label>
                <input x-ref="password" @keydown.enter.prevent="$refs.role.focus()" type="password" id="password"
                    wire:model="password"
                    class="w-full px-3 py-2 text-sm transition duration-150 ease-in-out bg-white border border-gray-300 rounded-md outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <small class="text-xs text-gray-500">Kosongkan jika tidak ingin mengubah password</small>
                @error('password')
                    <p class="text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Baris 2 -->
        <div class="grid grid-cols-1 gap-3 md:grid-cols-3">
            <!-- Role -->
            <div>
                <label for="role" class="block mb-1 text-gray-700">Role</label>
                <select x-ref="role" wire:model="role"
                    class="w-full px-3 py-2 text-sm transition duration-150 ease-in-out bg-white border border-gray-300 rounded-md outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="outlet">Outlet</option>
                    <option value="gudang">Gudang</option>
                    <option value="admin">Admin</option>
                </select>
                @error('role')
                    <p class="text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Outlet Autocomplete -->
            <div class="relative">
                <label class="block mb-1 text-gray-700">Nama Outlet</label>

                <!-- Input pencarian outlet -->
                <input type="text" x-ref="outlet" wire:model.live="searchOutlet"
                    wire:keydown.arrow-down.prevent="incrementHighlightOutlet"
                    wire:keydown.arrow-up.prevent="decrementHighlightOutlet"
                    wire:keydown.enter.prevent="selectHighlightedOutlet" placeholder="Ketik nama outlet..."
                    class="w-full px-3 py-2 text-sm transition duration-150 ease-in-out bg-white border border-gray-300 rounded-md outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">

                {{-- Dropdown hasil pencarian --}}
                @if (!empty($outletResults))
                    <div
                        class="absolute z-20 w-full mt-1 overflow-y-auto bg-white border border-gray-200 rounded-md shadow-lg max-h-56">
                        <ul class="text-sm text-gray-700 divide-y divide-gray-100">
                            @foreach ($outletResults as $i => $item)
                                <li wire:click="selectOutlet({{ $item['id'] }})"
                                    class="px-4 py-2 cursor-pointer
                               {{ $highlightIndexOutlet === $i ? 'bg-blue-100 text-blue-700' : 'hover:bg-gray-100' }}">
                                    {{ $item['nama_outlet'] }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if ($outlet_nama)
                    <p class="mt-1 text-xs text-green-600">Outlet dipilih: {{ $outlet_nama }}</p>
                @endif
            </div>


            <!-- Status -->
            <div>
                <label class="block mb-1 text-gray-700">Status</label>
                <select wire:model="aktif"
                    class="w-full px-3 py-2 text-sm transition duration-150 ease-in-out bg-white border border-gray-300 rounded-md outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="1">Aktif</option>
                    <option value="0">Tidak Aktif</option>
                </select>
            </div>
        </div>

        <!-- Tombol -->
        <div class="pt-2">
            <button type="submit"
                class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700
                       focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                Simpan
            </button>
        </div>
    </form>
</div>
