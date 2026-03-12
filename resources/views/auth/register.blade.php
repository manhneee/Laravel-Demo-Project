<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register – {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <style>body { font-family: 'Figtree', sans-serif; }</style>
</head>
<body class="bg-slate-100 min-h-screen antialiased flex items-center justify-center p-4">
    <div class="w-full max-w-sm">
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <h1 class="text-xl font-bold text-slate-800 mb-1">Create account</h1>
            <p class="text-slate-600 text-sm mb-6">{{ config('app.name') }}</p>

            <div id="error" class="hidden mb-4 p-3 rounded-lg bg-red-50 border border-red-100 text-red-700 text-sm"></div>

            <form id="register-form" class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-slate-700 mb-1">Name</label>
                    <input type="text" name="name" id="name" required autofocus autocomplete="name"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                    <input type="email" name="email" id="email" required autocomplete="email"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Password</label>
                    <input type="password" name="password" id="password" required autocomplete="new-password" minlength="8"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder="At least 8 characters">
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-1">Confirm password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required autocomplete="new-password" minlength="8"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <button type="submit" id="btn-submit" class="w-full rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 focus:outline focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50">
                    Register
                </button>
            </form>

            <p class="mt-4 text-center text-sm text-slate-600">
                Already have an account? <a href="{{ url('/login') }}" class="text-indigo-600 hover:underline">Log in</a>
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

        const form = document.getElementById('register-form');
        const errorEl = document.getElementById('error');
        const btnSubmit = document.getElementById('btn-submit');

        function showError(msg) {
            errorEl.textContent = msg || 'Registration failed.';
            errorEl.classList.remove('hidden');
        }

        function hideError() {
            errorEl.classList.add('hidden');
        }

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            hideError();
            const name = document.getElementById('name').value.trim();
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            const password_confirmation = document.getElementById('password_confirmation').value;
            if (!name || !email || !password) {
                showError('Name, email and password are required.');
                return;
            }
            if (password.length < 8) {
                showError('Password must be at least 8 characters.');
                return;
            }
            if (password !== password_confirmation) {
                showError('Passwords do not match.');
                return;
            }
            btnSubmit.disabled = true;
            try {
                const r = await fetch(API + '/register', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        name,
                        email,
                        password,
                        password_confirmation,
                    }),
                });
                const data = await r.json().catch(() => ({}));
                if (r.ok && data.token) {
                    setToken(data.token);
                    // New users are always 'user' role; redirect to user dashboard
                    if (data.user && data.user.role === 'admin') {
                        window.location.replace('{{ route("dashboard.admin") }}');
                    } else {
                        window.location.replace('{{ route("dashboard.user") }}');
                    }
                    return;
                }
                const msg = data.message || (data.errors ? Object.values(data.errors).flat().join(' ') : 'Registration failed.');
                showError(msg);
            } catch (err) {
                showError('Network error. Please try again.');
            } finally {
                btnSubmit.disabled = false;
            }
        });
    </script>
</body>
</html>
