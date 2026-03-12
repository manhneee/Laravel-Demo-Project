<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login – {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <style>body { font-family: 'Figtree', sans-serif; }</style>
</head>
<body class="bg-slate-100 min-h-screen antialiased flex items-center justify-center p-4">
    <div class="w-full max-w-sm">
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <h1 class="text-xl font-bold text-slate-800 mb-1">Sign in</h1>
            <p class="text-slate-600 text-sm mb-6">{{ config('app.name') }}</p>

            <div id="error" class="hidden mb-4 p-3 rounded-lg bg-red-50 border border-red-100 text-red-700 text-sm"></div>

            <form id="login-form" class="space-y-4">
                @csrf
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                    <input type="email" name="email" id="email" required autofocus autocomplete="email"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Password</label>
                    <input type="password" name="password" id="password" required autocomplete="current-password"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <button type="submit" id="btn-submit" class="w-full rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 focus:outline focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50">
                    Log in
                </button>
            </form>

            <p class="mt-4 text-center text-sm text-slate-600">
                Need an account? <a href="{{ url('/register') }}" class="text-indigo-600 hover:underline">Register</a>
            </p>
        </div>
        <p class="mt-4 text-center">
            <a href="{{ url('/') }}" class="text-sm text-slate-500 hover:text-slate-700">← Back to home</a>
        </p>
    </div>

    <script>
        const API = '{{ url("/api") }}';
        const TOKEN_KEY = 'auth_token';

        function setToken(token) {
            if (token) {
                localStorage.setItem(TOKEN_KEY, token);
            } else {
                localStorage.removeItem(TOKEN_KEY);
            }
        }

        const form = document.getElementById('login-form');
        const errorEl = document.getElementById('error');
        const btnSubmit = document.getElementById('btn-submit');

        function showError(msg) {
            errorEl.textContent = msg || 'Login failed.';
            errorEl.classList.remove('hidden');
        }

        function hideError() {
            errorEl.classList.add('hidden');
        }

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            hideError();
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            if (!email || !password) {
                showError('Email and password are required.');
                return;
            }
            btnSubmit.disabled = true;
            try {
                const r = await fetch(API + '/login', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ email, password }),
                });
                const data = await r.json().catch(() => ({}));
                if (r.ok && data.token) {
                    setToken(data.token);
                    // Redirect by role (no extra round-trip to home)
                    if (data.user && data.user.role === 'admin') {
                        window.location.replace('{{ route("dashboard.admin") }}');
                    } else {
                        window.location.replace('{{ route("dashboard.user") }}');
                    }
                    return;
                }
                showError(data.message || 'Invalid credentials.');
            } catch (err) {
                showError('Network error. Please try again.');
            } finally {
                btnSubmit.disabled = false;
            }
        });
    </script>
</body>
</html>
