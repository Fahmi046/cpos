<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="w-full max-w-md p-8 bg-white rounded-lg shadow-lg">
        <h2 class="mb-6 text-2xl font-bold text-center text-gray-800">Login</h2>

        <form method="POST" action="{{ route('login.post') }}">
            @csrf

            <!-- Email -->
            <div class="mb-4">
                <label for="email" class="block mb-2 font-medium text-gray-700">Email</label>
                <input type="email" name="email" id="email" required
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <!-- Password -->
            <div class="mb-6">
                <label for="password" class="block mb-2 font-medium text-gray-700">Password</label>
                <input type="password" name="password" id="password" required
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <!-- Submit -->
            <button type="submit"
                class="w-full px-4 py-2 font-semibold text-white transition duration-200 bg-indigo-600 rounded-lg hover:bg-indigo-700">
                Login
            </button>
        </form>

        <!-- Optional: Register link -->
        <p class="mt-4 text-center text-gray-600">
            Belum punya akun? <a href="/register" class="text-indigo-600 hover:underline">Daftar</a>
        </p>
    </div>
</body>

</html>
