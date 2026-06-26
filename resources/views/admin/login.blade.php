<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Ecommerce</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        function togglePassword(fieldId, button) {
            const passwordInput = document.getElementById(fieldId);
            const eyeIcon = document.getElementById('eye-icon-' + fieldId);
            const eyeOffIcon = document.getElementById('eye-off-icon-' + fieldId);

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.add('hidden');
                eyeOffIcon.classList.remove('hidden');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('hidden');
                eyeOffIcon.classList.add('hidden');
            }
        }
    </script>
</head>
<body class="bg-gradient-to-br from-indigo-500 to-purple-600 min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-md">
        <h1 class="text-3xl font-bold text-center mb-8 text-gray-800">Welcome Back</h1>

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login.post') }}">
            @csrf
            <div class="mb-5">
                <label class="block text-gray-700 text-sm font-medium mb-2" for="email">Email</label>
                <input class="shadow-sm appearance-none border border-gray-200 rounded-lg w-full py-3 px-4 bg-blue-50 text-gray-700 leading-tight focus:outline-none focus:border-indigo-500 focus:bg-white"
                       id="email" type="email" name="email" value="rina@gmail.com" required autofocus>
            </div>
            <div class="mb-6 relative">
                <label class="block text-gray-700 text-sm font-medium mb-2" for="password">Password</label>
                <input class="shadow-sm appearance-none border border-gray-200 rounded-lg w-full py-3 px-4 bg-blue-50 text-gray-700 leading-tight focus:outline-none focus:border-indigo-500 focus:bg-white pr-12"
                       id="password" type="password" name="password" value="password" required>
                <button type="button" onclick="togglePassword('password', this)" class="absolute right-3 top-9 text-gray-500 hover:text-gray-700 focus:outline-none">
                    <svg id="eye-icon-password" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <svg id="eye-off-icon-password" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l-3.293-3.293m0 0a3 3 0 104.243-4.243l3.293 3.293m-3.293-3.293l3.293 3.293M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                    </svg>
                </button>
            </div>
            <div class="mb-4">
                <button class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-lg focus:outline-none focus:shadow-outline transition duration-200"
                        type="submit">Login</button>
            </div>
        </form>
        <div class="text-center mt-4">
            <span class="text-gray-500">No account?</span>
            <a href="{{ route('admin.register') }}" class="text-indigo-600 hover:text-indigo-800 font-medium ml-1">Register here</a>
        </div>
    </div>
</body>
</html>
