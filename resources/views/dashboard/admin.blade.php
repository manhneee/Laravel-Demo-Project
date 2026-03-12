<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard – {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <style>body { font-family: 'Figtree', sans-serif; }</style>
</head>
<body class="bg-slate-100 min-h-screen antialiased">
    <nav class="bg-white border-b border-slate-200 shadow-sm">
        <div class="max-w-6xl mx-auto px-4 py-3 flex justify-between items-center">
            <div class="flex items-center gap-6">
                <a href="{{ url('/') }}" class="text-slate-800 font-semibold text-lg">Ticket System</a>
                <a href="{{ route('dashboard.admin') }}" class="text-slate-600 hover:text-slate-900">Dashboard</a>
                <a href="{{ route('admin.manage') }}" class="text-indigo-600 font-medium">Manage</a>
            </div>
            <div id="nav-user" class="hidden items-center gap-3">
                <span class="text-sm text-slate-700"><strong id="nav-user-name"></strong> <span class="text-xs bg-indigo-100 text-indigo-800 px-2 py-0.5 rounded">Admin</span></span>
                <a href="{{ route('admin.manage') }}" class="text-sm text-slate-600 hover:text-slate-900">Manage</a>
                <button type="button" id="btn-logout" class="rounded bg-red-600 text-white px-3 py-1.5 text-sm hover:bg-red-700">Logout</button>
            </div>
            <div id="nav-guest" class="hidden">
                <a href="{{ url('/test-ui') }}" class="text-indigo-600 font-medium">Log in</a>
            </div>
        </div>
    </nav>

    <main class="max-w-6xl mx-auto px-4 py-8">
        <div id="need-login" class="hidden bg-amber-50 border border-amber-200 rounded-xl p-6 text-center">
            <p class="text-amber-800 mb-4">Please log in to access the admin dashboard.</p>
            <a href="{{ url('login') }}" class="inline-block rounded bg-indigo-600 text-white px-4 py-2 text-sm font-medium hover:bg-indigo-700">Go to Login</a>
        </div>

        <div id="access-denied" class="hidden bg-red-50 border border-red-200 rounded-xl p-6 text-center">
            <p class="text-red-800 mb-4">Access denied. Admin role required.</p>
            <a href="{{ url('/dashboard/user') }}" class="inline-block rounded bg-slate-600 text-white px-4 py-2 text-sm font-medium hover:bg-slate-700">Go to User Dashboard</a>
        </div>

        <div id="dashboard-content" class="hidden">
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-slate-800">Admin Dashboard</h1>
                <p class="text-slate-600 mt-1">System-wide overview</p>
            </div>

            <div id="stats-cards" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
                <!-- Filled by JS -->
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
                    <h2 class="text-lg font-semibold text-slate-800 mb-4">Tickets by status</h2>
                    <ul id="by-status-list" class="space-y-2">
                        <!-- Filled by JS -->
                    </ul>
                </div>
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
                    <h2 class="text-lg font-semibold text-slate-800 mb-4">Quick links</h2>
                    <p class="text-slate-600 text-sm">Manage tickets, users, logs, categories, and labels from <a href="{{ route('admin.manage') }}" class="text-indigo-600 hover:underline">Manage</a>.</p>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200">
                    <h2 class="text-lg font-semibold text-slate-800">Recent tickets (all)</h2>
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

        function showNeedLogin() {
            document.getElementById('loading').classList.add('hidden');
            document.getElementById('need-login').classList.remove('hidden');
            document.getElementById('dashboard-content').classList.add('hidden');
            document.getElementById('access-denied').classList.add('hidden');
            document.getElementById('nav-user').classList.add('hidden');
            document.getElementById('nav-guest').classList.remove('hidden');
        }

        function showAccessDenied() {
            document.getElementById('loading').classList.add('hidden');
            document.getElementById('need-login').classList.add('hidden');
            document.getElementById('dashboard-content').classList.add('hidden');
            document.getElementById('access-denied').classList.remove('hidden');
            document.getElementById('nav-user').classList.add('hidden');
            document.getElementById('nav-guest').classList.remove('hidden');
        }

        function showContent(userName) {
            document.getElementById('loading').classList.add('hidden');
            document.getElementById('need-login').classList.add('hidden');
            document.getElementById('access-denied').classList.add('hidden');
            document.getElementById('dashboard-content').classList.remove('hidden');
            document.getElementById('nav-user').classList.remove('hidden');
            document.getElementById('nav-user').classList.add('flex');
            document.getElementById('nav-user-name').textContent = userName;
            document.getElementById('nav-guest').classList.add('hidden');
        }

        function renderDashboard(data) {
            const totalTickets = data.total_tickets ?? 0;
            const totalUsers = data.total_users ?? 0;
            const totalCategories = data.total_categories ?? 0;
            const totalLabels = data.total_labels ?? 0;
            const byStatus = data.tickets_by_status || {};
            const recent = data.recent_tickets || [];

            document.getElementById('stats-cards').innerHTML = [
                { label: 'Total tickets', value: totalTickets },
                { label: 'Users', value: totalUsers },
                { label: 'Categories', value: totalCategories },
                { label: 'Labels', value: totalLabels },
                { label: 'Role', value: 'Admin', extra: 'bg-indigo-50 border-indigo-100' }
            ].map((c, i) => `<div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 ${c.extra || ''}">
                <div class="text-sm font-medium text-slate-500 uppercase tracking-wide">${c.label}</div>
                <div class="mt-1 text-3xl font-bold text-slate-800">${c.value}</div>
            </div>`).join('');

            const statusOrder = ['open', 'in_progress', 'pending', 'resolved', 'closed'];
            const statusList = statusOrder.filter(s => byStatus[s] !== undefined).length
                ? statusOrder.filter(s => byStatus[s] !== undefined).map(s => `<li class="flex justify-between text-sm"><span class="text-slate-600 capitalize">${s.replace('_', ' ')}</span><span class="font-medium text-slate-800">${byStatus[s]}</span></li>`).join('')
                : '<li class="text-slate-500 text-sm">No tickets yet.</li>';
            document.getElementById('by-status-list').innerHTML = statusList;

            const recentEl = document.getElementById('recent-tickets');
            if (recent.length === 0) {
                recentEl.innerHTML = '<p class="p-5 text-slate-500 text-sm">No tickets yet.</p>';
            } else {
                recentEl.innerHTML = `<table class="w-full text-sm">
                    <thead class="bg-slate-50 text-left"><tr>
                        <th class="py-3 px-4 font-medium text-slate-600">#</th>
                        <th class="py-3 px-4 font-medium text-slate-600">Title</th>
                        <th class="py-3 px-4 font-medium text-slate-600">Status</th>
                        <th class="py-3 px-4 font-medium text-slate-600">Created by</th>
                        <th class="py-3 px-4 font-medium text-slate-600">Assigned to</th>
                        <th class="py-3 px-4 font-medium text-slate-600">Updated</th>
                    </tr></thead>
                    <tbody>${recent.map(t => {
                        const createdBy = t.created_by || t.createdBy;
                        const assignedTo = t.assigned_user || t.assignedUser;
                        return `<tr class="border-t border-slate-100 hover:bg-slate-50">
                            <td class="py-3 px-4">${t.id}</td>
                            <td class="py-3 px-4 font-medium text-slate-800">${t.title || '—'}</td>
                            <td class="py-3 px-4">${t.status || '—'}</td>
                            <td class="py-3 px-4 text-slate-600">${createdBy ? (createdBy.name || '—') : '—'}</td>
                            <td class="py-3 px-4 text-slate-600">${assignedTo ? (assignedTo.name || '—') : '—'}</td>
                            <td class="py-3 px-4 text-slate-500">${t.updated_at ? new Date(t.updated_at).toLocaleDateString() : '—'}</td>
                        </tr>`;
                    }).join('')}</tbody>
                </table>`;
            }
        }

        async function load() {
            const token = getToken();
            if (!token) {
                showNeedLogin();
                return;
            }
            const { ok: okUser, data: dataUser } = await api('/user');
            if (!okUser || !dataUser.user) {
                setToken(null);
                showNeedLogin();
                return;
            }
            if (dataUser.user.role !== 'admin') {
                showAccessDenied();
                return;
            }

            const res = await api('/admin/dashboard');
            if (!res.ok) {
                if (res.status === 403) showAccessDenied();
                else showNeedLogin();
                return;
            }
            const data = res.data;
            renderDashboard(data);
            showContent(dataUser.user.name);
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
