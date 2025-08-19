<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="font-[Inter] bg-white">
    <div class="min-h-screen flex">
        <!-- Left Panel -->
        <div class="w-full md:w-1/2 flex items-center justify-center px-6 py-12">
            <div class="w-full max-w-md">
                <h1 class="text-4xl font-bold text-red-600 mb-2">Welcome!</h1>
                <p class="mb-8 text-gray-600">Login to your account to continue</p>

                <!-- Form -->
                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="email" class="text-sm text-gray-700 font-semibold">Email Address</label>
                        <div class="relative mt-1">
                            <input type="email" name="email" id="email" required autofocus
                                   class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-red-500">
                        </div>
                    </div>

                    <div>
                        <label for="password" class="text-sm text-gray-700 font-semibold">Password</label>
                        <div class="relative mt-1">
                            <input type="password" name="password" id="password" required
                                   class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-red-500">
                        </div>
                    </div>

                    <div class="text-right">
                        <a href="{{ route('password.request') }}" class="text-sm text-red-600 hover:underline">Forget password?</a>
                    </div>

                    <button type="submit"
                            class="w-full bg-red-600 hover:bg-red-700 text-white py-2 rounded font-semibold transition duration-200">
                        Login
                    </button>
                </form>

                <!-- Link to Register Page -->
                <div class="mt-4 text-center">
                    <span class="text-sm text-gray-600">Don't have an account? </span>
                    <a href="{{ route('register') }}" class="text-sm text-blue-600 hover:underline">Register here</a>
                </div>
            </div>
        </div>

        <!-- Right Illustration -->
        <div class="hidden md:flex w-1/2 bg-red-700 items-center justify-center">
            <img src="{{ asset('images/gambarsatu.png') }}" alt="Login Illustration" class="w-[85%] h-auto">
        </div>
    </div>
</body>
</html>
