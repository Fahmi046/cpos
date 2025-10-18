<!-- Include this script tag or install `@tailwindplus/elements` via npm: -->
<!-- <script src="https://cdn.jsdelivr.net/npm/@tailwindplus/elements@1" type="module"></script> -->
<nav class="relative bg-gray-800">
    <div class="px-2 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="relative flex items-center justify-between h-16">

            <!-- Mobile menu button-->
            <div class="absolute inset-y-0 left-0 flex items-center sm:hidden">
                <button type="button" command="--toggle" commandfor="mobile-menu"
                    class="relative inline-flex items-center justify-center p-2 text-gray-400 rounded-md hover:bg-white/5 hover:text-white focus:outline-2 focus:-outline-offset-1 focus:outline-indigo-500">
                    <span class="absolute -inset-0.5"></span>
                    <span class="sr-only">Open main menu</span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon"
                        aria-hidden="true" class="size-6 in-aria-expanded:hidden">
                        <path d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon"
                        aria-hidden="true" class="size-6 not-in-aria-expanded:hidden">
                        <path d="M6 18 18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
            </div>

            <div class="flex items-center justify-center flex-1 sm:items-stretch sm:justify-start">
                <div class="flex items-center shrink-0">
                    <img src="https://tailwindcss.com/plus-assets/img/logos/mark.svg?color=indigo&shade=500"
                        alt="Your Company" class="w-auto h-8" />
                </div>

                <!-- Menu utama -->
                <div class="hidden sm:ml-6 sm:block">
                    <div class="flex space-x-4">
                        {{-- Hanya tampil untuk user bukan outlet --}}
                        @if (Auth::user()->role !== 'outlet')
                            <!-- Master Data Dropdown -->
                            <div x-data="{ open: false }" class="relative">
                                <button @click="open = !open"
                                    class="flex items-center gap-1 px-3 py-2 text-sm font-medium text-gray-300 rounded-md hover:bg-white/5 hover:text-white">
                                    Master Data
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 9l6 6 6-6" />
                                    </svg>
                                </button>

                                <div x-show="open" @click.outside="open = false"
                                    class="absolute z-50 w-48 py-1 mt-2 bg-white rounded-md shadow-lg">
                                    <a href="/obat"
                                        class="{{ request()->is('obat') ? 'block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 font-bold' : 'block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100' }}">Obat</a>
                                    <a href="/kategori-obat"
                                        class="{{ request()->is('kategori-obat') ? 'block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 font-bold' : 'block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100' }}">Kategori
                                        Obat</a>
                                    <a href="/satuan-obat"
                                        class="{{ request()->is('satuan-obat') ? 'block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 font-bold' : 'block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100' }}">Satuan
                                        Obat</a>
                                    <a href="/bentuk-sediaan"
                                        class="{{ request()->is('bentuk-sediaan') ? 'block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 font-bold' : 'block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100' }}">Bentuk
                                        Sediaan</a>
                                    <a href="/pabrik"
                                        class="{{ request()->is('pabrik') ? 'block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 font-bold' : 'block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100' }}">Pabrik</a>
                                    <a href="/komposisi"
                                        class="{{ request()->is('komposisi') ? 'block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 font-bold' : 'block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100' }}">Komposisi</a>
                                    <a href="/kreditur"
                                        class="{{ request()->is('kreditur') ? 'block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 font-bold' : 'block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100' }}">Kreditur</a>
                                    <a href="/outlet"
                                        class="{{ request()->is('outlet') ? 'block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 font-bold' : 'block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100' }}">Outlet</a>
                                    <a href="/users"
                                        class="{{ request()->is('users') ? 'block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 font-bold' : 'block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100' }}">Akun
                                        User</a>
                                </div>
                            </div>

                            <!-- Penyediaan Dropdown -->
                            <div x-data="{ open: false }" class="relative">
                                <button @click="open = !open"
                                    class="flex items-center gap-1 px-3 py-2 text-sm font-medium text-gray-300 rounded-md hover:bg-white/5 hover:text-white">
                                    Penyediaan
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 9l6 6 6-6" />
                                    </svg>
                                </button>

                                <div x-show="open" @click.outside="open = false"
                                    class="absolute z-50 w-48 py-1 mt-2 bg-white rounded-md shadow-lg">
                                    <a href="/pesanan"
                                        class="{{ request()->is('pesanan') ? 'block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 font-bold' : 'block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100' }}">Surat
                                        Pesanan</a>
                                    <a href="/penerimaan"
                                        class="{{ request()->is('penerimaan') ? 'block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 font-bold' : 'block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100' }}">Penerimaan</a>
                                    <a href="/kartu-stok"
                                        class="{{ request()->is('kartu-stok') ? 'block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 font-bold' : 'block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100' }}">Kartu
                                        Stok</a>
                                    <a href="/permintaan"
                                        class="{{ request()->is('permintaan') ? 'block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 font-bold' : 'block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100' }}">Permintaan
                                        Outlet</a>
                                    <a href="/mutasi"
                                        class="{{ request()->is('mutasi') ? 'block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 font-bold' : 'block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100' }}">Mutasi
                                        Stok</a>
                                </div>
                            </div>
                        @endif
                        <!-- Pengadaan Dropdown (hanya untuk user role outlet) -->
                        @if (Auth::user()->role === 'outlet')
                            <div x-data="{ open: false }" class="relative">
                                <button @click="open = !open"
                                    class="flex items-center gap-1 px-3 py-2 text-sm font-medium text-gray-300 rounded-md hover:bg-white/5 hover:text-white">
                                    Pengadaan
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 9l6 6 6-6" />
                                    </svg>
                                </button>

                                <div x-show="open" @click.outside="open = false"
                                    class="absolute z-50 w-48 py-1 mt-2 bg-white rounded-md shadow-lg">
                                    <a href="/po"
                                        class="{{ request()->is('po') ? 'block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 font-bold' : 'block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100' }}">
                                        Permintaan
                                    </a>
                                    <a href="/stok-outlet"
                                        class="{{ request()->is('stok-outlet') ? 'block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 font-bold' : 'block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100' }}">
                                        Stok Outlet
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Tombol Logout + Nama User -->
            <div class="absolute inset-y-0 right-0 flex items-center pr-2 sm:static sm:inset-auto sm:ml-6 sm:pr-0">
                @auth
                    <!-- Nama User -->
                    <span class="mr-3 font-medium text-white">
                        {{ Auth::user()->name }}
                    </span>

                    <!-- Tombol Logout -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="relative p-1 text-gray-400 rounded-full hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            title="Logout">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6A2.25 2.25 0 0 0 5.25 5.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3-3H9m9 0-3-3m3 3-3 3" />
                            </svg>
                        </button>
                    </form>
                @endauth
            </div>

        </div>
    </div>
</nav>
