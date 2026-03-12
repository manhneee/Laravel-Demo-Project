<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Home – {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <style>body { font-family: 'Figtree', sans-serif; }</style>
</head>
<body class="bg-slate-100 min-h-screen antialiased flex items-center justify-center">
    <div id="message" class="text-center text-slate-600">
        <div class="animate-pulse text-lg">Redirecting...</div>
        <p class="mt-2 text-sm">If nothing happens, <a href="{{ url('login') }}" class="text-indigo-600 hover:underline">log in here</a>.</p>
    </div>

    <script>
        const API = '{{ url("/api") }}';
        const TOKEN_KEY = 'auth_token';

        function getToken() {
            return localStorage.getItem(TOKEN_KEY);
        }

        async function redirectTo() {
            const token = getToken();
            if (!token) {
                window.location.replace('{{ route("login") }}');
                return;
            }
            try {
                const r = await fetch(API + '/user', {
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': 'Bearer ' + token,
                    },
                });
                const data = await r.json().catch(() => ({}));
                if (r.ok && data.user) {
                    if (data.user.role === 'admin') {
                        window.location.replace('{{ route("dashboard.admin") }}');
                        return;
                    }
                    window.location.replace('{{ route("dashboard.user") }}');
                    return;
                }
            } catch (e) {}
            window.location.replace('{{ route("login") }}');
        }

        redirectTo();
    </script>
</body>
</html>
