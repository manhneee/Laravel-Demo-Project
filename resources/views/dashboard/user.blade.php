<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Dashboard – {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <style>body { font-family: 'Figtree', sans-serif; }</style>
</head>
<body class="bg-slate-100 min-h-screen antialiased">
    <nav class="bg-white border-b border-slate-200 shadow-sm">
        <div class="max-w-6xl mx-auto px-4 py-3 flex justify-between items-center">
            <div class="flex items-center gap-6">
                <a href="{{ url('/') }}" class="text-slate-800 font-semibold text-lg">Ticket System</a>
                <a href="{{ route('dashboard.user') }}" class="text-slate-600 hover:text-slate-900">Dashboard</a>
                <a href="{{ route('user.manage') }}" class="text-indigo-600 font-medium">Manage Tickets</a>
            </div>
            <div id="nav-user" class="hidden items-center gap-3">
                <span class="text-sm text-slate-700"><strong id="nav-user-name"></strong> <span id="nav-user-role" class="text-slate-500 text-xs capitalize"></span></span>
                <a href="{{ route('user.manage') }}" class="text-sm text-slate-600 hover:text-slate-900">Manage Tickets</a>
                <button type="button" id="btn-logout" class="rounded bg-red-600 text-white px-3 py-1.5 text-sm hover:bg-red-700">Logout</button>
            </div>
            <div id="nav-guest" class="hidden">
                <a href="{{ url('/test-ui') }}" class="text-indigo-600 font-medium">Log in</a>
            </div>
        </div>
    </nav>

    <main class="max-w-6xl mx-auto px-4 py-8">
        <div id="need-login" class="hidden bg-amber-50 border border-amber-200 rounded-xl p-6 text-center">
            <p class="text-amber-800 mb-4">Please log in to see your dashboard.</p>
            <a href="{{ route('login') }}" class="inline-block rounded bg-indigo-600 text-white px-4 py-2 text-sm font-medium hover:bg-indigo-700">Go to Login</a>
        </div>

        <div id="dashboard-content" class="hidden">
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-slate-800">My Dashboard</h1>
                <p class="text-slate-600 mt-1">Overview of your tickets</p>
            </div>

            <div id="stats-cards" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <!-- Filled by JS -->
            </div>

            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200">
                    <h2 class="text-lg font-semibold text-slate-800">Recent tickets</h2>
                </div>
                <div id="recent-tickets" class="overflow-x-auto">
                    <!-- Filled by JS -->
                </div>
            </div>
        </div>

        <div id="loading" class="text-center py-12 text-slate-500">Loading...</div>
    </main>

    <script>
        const API = '{{ url("/api") }}';
        const TICKET_SHOW_URL = '{{ url("/ticket") }}';
        const TOKEN_KEY = 'auth_token';
        function getToken() { return localStorage.getItem(TOKEN_KEY); }
        function setToken(t) { t ? localStorage.setItem(TOKEN_KEY, t) : localStorage.removeItem(TOKEN_KEY); }

        async function api(path, opts = {}) {
            const headers = { 'Accept': 'application/json', 'Content-Type': 'application/json', ...opts.headers };
            const token = getToken();
            if (token) headers['Authorization'] = 'Bearer ' + token;
            const r = await fetch(API + path, { ...opts, headers });
            const data = await r.json().catch(() => ({}));
            return { ok: r.ok, status: r.status, data };
        }

        function showContent(show) {
            document.getElementById('loading').classList.toggle('hidden', true);
            document.getElementById('need-login').classList.toggle('hidden', show);
            document.getElementById('dashboard-content').classList.toggle('hidden', !show);
            document.getElementById('nav-user').classList.toggle('hidden', !show);
            document.getElementById('nav-user').classList.toggle('flex', show);
            document.getElementById('nav-guest').classList.toggle('hidden', show);
        }

        function renderDashboard(data) {
            const byStatus = data.tickets_by_status || {};
            const total = data.total ?? 0;
            const recent = data.recent_tickets || [];
            const statusOrder = ['open', 'in_progress', 'pending', 'resolved', 'closed'];

            const cards = [
                { label: 'Total tickets', value: total, class: 'bg-white' },
                ...statusOrder.filter(s => byStatus[s] !== undefined).map(s => ({
                    label: s.replace('_', ' '),
                    value: byStatus[s],
                    class: 'bg-white'
                }))
            ];
            if (cards.length === 1) cards.push({ label: 'No tickets yet', value: '—', class: 'bg-slate-50' });

            document.getElementById('stats-cards').innerHTML = cards.map(c =>
                `<div class="${c.class} rounded-xl border border-slate-200 shadow-sm p-5">
                    <div class="text-sm font-medium text-slate-500 uppercase tracking-wide">${c.label}</div>
                    <div class="mt-1 text-3xl font-bold text-slate-800">${c.value}</div>
                </div>`
            ).join('');

            const recentEl = document.getElementById('recent-tickets');
            if (recent.length === 0) {
                recentEl.innerHTML = '<p class="p-5 text-slate-500 text-sm">No tickets yet.</p>';
            } else {
                const names = (arr) => (arr && arr.length) ? arr.map(x => x.name).join(', ') : '—';
                recentEl.innerHTML = `<table class="w-full text-sm">
                    <thead class="bg-slate-50 text-left"><tr>
                        <th class="py-3 px-4 font-medium text-slate-600">#</th>
                        <th class="py-3 px-4 font-medium text-slate-600">Title</th>
                        <th class="py-3 px-4 font-medium text-slate-600">Status</th>
                        <th class="py-3 px-4 font-medium text-slate-600">Priority</th>
                        <th class="py-3 px-4 font-medium text-slate-600">Updated</th>
                    </tr></thead>
                    <tbody>${recent.map(t => `
                        <tr class="border-t border-slate-100 hover:bg-slate-50">
                            <td class="py-3 px-4">${t.id}</td>
                            <td class="py-3 px-4"><a href="${TICKET_SHOW_URL}/${t.id}" class="font-medium text-indigo-600 hover:underline">${t.title || '—'}</a></td>
                            <td class="py-3 px-4">${t.status || '—'}</td>
                            <td class="py-3 px-4">${(t.priority && t.priority.name) ? t.priority.name : '—'}</td>
                            <td class="py-3 px-4 text-slate-500">${t.updated_at ? new Date(t.updated_at).toLocaleDateString() : '—'}</td>
                        </tr>
                    `).join('')}</tbody>
                </table>`;
            }
        }

        async function load() {
            const token = getToken();
            if (!token) {
                showContent(false);
                return;
            }
            const { ok: okUser, data: dataUser } = await api('/user');
            if (!okUser || !dataUser.user) {
                setToken(null);
                showContent(false);
                return;
            }
            document.getElementById('nav-user-name').textContent = dataUser.user.name;
            document.getElementById('nav-user-role').textContent = '(' + (dataUser.user.role || 'user') + ')';

            const { ok, data } = await api('/dashboard');
            if (!ok) {
                showContent(false);
                return;
            }
            renderDashboard(data);
            showContent(true);
        }

        document.getElementById('btn-logout').addEventListener('click', async () => {
            await api('/logout', { method: 'POST' });
            setToken(null);
            window.location.reload();
        });

        load();
    </script>
</body>
</html>
