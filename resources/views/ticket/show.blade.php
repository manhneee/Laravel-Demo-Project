<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ticket #{{ $ticketId }} – {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <style>body { font-family: 'Figtree', sans-serif; }</style>
</head>
<body class="bg-slate-100 min-h-screen antialiased">
    <nav class="bg-white border-b border-slate-200 shadow-sm">
        <div class="max-w-4xl mx-auto px-4 py-3 flex justify-between items-center">
            <div class="flex items-center gap-4">
                <a href="{{ url('/') }}" class="text-slate-800 font-semibold text-lg">Ticket System</a>
                <a href="{{ route('dashboard.user') }}" id="nav-dashboard" class="text-slate-600 hover:text-slate-900">Dashboard</a>
                <a href="{{ route('user.manage') }}" id="nav-my-tickets" class="text-slate-600 hover:text-slate-900">My Tickets</a>
            </div>
            <div id="nav-user" class="hidden items-center gap-3">
                <span class="text-sm text-slate-700"><strong id="nav-user-name"></strong></span>
                <button type="button" id="btn-logout" class="rounded bg-red-600 text-white px-3 py-1.5 text-sm hover:bg-red-700">Logout</button>
            </div>
            <div id="nav-guest" class="hidden">
                <a href="{{ url('/login') }}" class="text-indigo-600 font-medium">Log in</a>
            </div>
        </div>
    </nav>

    <main class="max-w-4xl mx-auto px-4 py-8">
        <div id="need-login" class="hidden bg-amber-50 border border-amber-200 rounded-xl p-6 text-center">
            <p class="text-amber-800 mb-4">Please log in to view this ticket.</p>
            <a href="{{ url('/login') }}" class="inline-block rounded bg-indigo-600 text-white px-4 py-2 text-sm font-medium hover:bg-indigo-700">Log in</a>
        </div>
        <div id="access-denied" class="hidden bg-red-50 border border-red-200 rounded-xl p-6 text-center">
            <p class="text-red-800 mb-4">You do not have access to this ticket.</p>
            <a href="{{ route('user.manage') }}" id="back-link-denied" class="inline-block rounded bg-slate-600 text-white px-4 py-2 text-sm">Back to My Tickets</a>
        </div>

        <div id="ticket-page" class="hidden">
            <div class="mb-6">
                <a href="{{ route('user.manage') }}" id="back-link-ticket" class="text-sm text-slate-500 hover:text-slate-700">← Back to tickets</a>
            </div>
            <article class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-200">
                    <h1 id="ticket-title" class="text-2xl font-bold text-slate-800"></h1>
                    <div id="ticket-meta" class="mt-2 text-sm text-slate-500 flex flex-wrap gap-x-4 gap-y-1"></div>
                </div>
                <div class="p-6 border-b border-slate-200">
                    <h2 class="text-sm font-medium text-slate-500 uppercase tracking-wide mb-2">Description</h2>
                    <div id="ticket-description" class="text-slate-700 whitespace-pre-wrap"></div>
                </div>
                <div id="ticket-details" class="p-6 border-b border-slate-200 text-sm text-slate-600"></div>
            </article>

            <section class="mt-8 bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200">
                    <h2 class="text-lg font-semibold text-slate-800">Comments</h2>
                </div>
                <div id="comments-list" class="divide-y divide-slate-100">
                    <!-- comments rendered by JS -->
                </div>
                <div class="p-6 border-t border-slate-200 bg-slate-50">
                    <form id="comment-form" class="space-y-3">
                        <label class="block text-sm font-medium text-slate-700">Add a comment</label>
                        <textarea id="comment-body" name="body" rows="3" placeholder="Write your comment..." class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" maxlength="5000"></textarea>
                        <button type="submit" id="comment-submit" class="rounded-lg bg-indigo-600 text-white px-4 py-2 text-sm font-medium hover:bg-indigo-700">Post comment</button>
                    </form>
                </div>
            </section>
        </div>
        <div id="loading" class="text-center py-12 text-slate-500">Loading...</div>
    </main>

    <div id="toast" class="fixed top-4 right-4 rounded-lg px-4 py-2 shadow-lg z-50 hidden bg-slate-800 text-white text-sm"></div>

    <script>
        const API = '{{ url("/api") }}';
        const TOKEN_KEY = 'auth_token';
        const TICKET_ID = {{ (int) $ticketId }};
        function getToken() { return localStorage.getItem(TOKEN_KEY); }
        function showToast(msg) {
            const el = document.getElementById('toast');
            el.textContent = msg;
            el.classList.remove('hidden');
            setTimeout(() => el.classList.add('hidden'), 3000);
        }
        async function api(path, opts = {}) {
            const headers = { 'Accept': 'application/json', 'Content-Type': 'application/json', ...opts.headers };
            const token = getToken();
            if (token) headers['Authorization'] = 'Bearer ' + token;
            const r = await fetch(API + path, { ...opts, headers });
            const data = await r.json().catch(() => ({}));
            return { ok: r.ok, status: r.status, data };
        }
        function esc(s) {
            return (s || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }
        function renderTicket(ticket) {
            document.getElementById('ticket-title').textContent = '#' + ticket.id + ' ' + (ticket.title || '');
            const createdBy = ticket.created_by || ticket.createdBy;
            const assignedTo = ticket.assigned_user || ticket.assignedUser;
            const priority = ticket.priority;
            document.getElementById('ticket-meta').innerHTML = [
                'Status: ' + (ticket.status || '—'),
                'Priority: ' + (priority && priority.name ? priority.name : '—'),
                createdBy ? 'Created by ' + esc(createdBy.name) : '',
                assignedTo ? 'Assigned to ' + esc(assignedTo.name) : '',
                ticket.created_at ? 'Created ' + new Date(ticket.created_at).toLocaleString() : '',
            ].filter(Boolean).map(s => '<span>' + s + '</span>').join('');
            document.getElementById('ticket-description').innerHTML = esc(ticket.description || '—');
            const labels = (ticket.labels && ticket.labels.length) ? ticket.labels.map(l => l.name).join(', ') : '—';
            const categories = (ticket.categories && ticket.categories.length) ? ticket.categories.map(c => c.name).join(', ') : '—';
            document.getElementById('ticket-details').innerHTML = '<p><strong>Labels:</strong> ' + esc(labels) + '</p><p class="mt-1"><strong>Categories:</strong> ' + esc(categories) + '</p>';
        }
        function renderComments(comments) {
            const el = document.getElementById('comments-list');
            if (!comments || !comments.length) {
                el.innerHTML = '<p class="p-6 text-slate-500 text-sm comments-empty-placeholder">No comments yet. Be the first to comment.</p>';
                return;
            }
            el.innerHTML = comments.map(c => {
                const user = c.user || {};
                const name = user.name || 'Unknown';
                const date = c.created_at ? new Date(c.created_at).toLocaleString() : '';
                return '<div class="p-6"><p class="text-slate-700 whitespace-pre-wrap">' + esc(c.body) + '</p><p class="mt-2 text-sm text-slate-500">' + esc(name) + ' · ' + date + '</p></div>';
            }).join('');
        }
        async function loadTicket() {
            const token = getToken();
            if (!token) {
                document.getElementById('loading').classList.add('hidden');
                document.getElementById('need-login').classList.remove('hidden');
                document.getElementById('nav-guest').classList.remove('hidden');
                return;
            }
            document.getElementById('nav-user').classList.remove('hidden');
            document.getElementById('nav-user').classList.add('flex');
            const { ok, data } = await api('/user');
            if (ok && data.user) {
                document.getElementById('nav-user-name').textContent = data.user.name;
                if (data.user.role === 'admin') {
                    document.getElementById('nav-dashboard').href = '{{ route("dashboard.admin") }}';
                    document.getElementById('nav-my-tickets').href = '{{ route("admin.manage") }}';
                    document.getElementById('nav-my-tickets').textContent = 'Manage';
                    document.getElementById('back-link-denied').href = '{{ route("admin.manage") }}';
                    document.getElementById('back-link-denied').textContent = 'Back to Manage';
                    document.getElementById('back-link-ticket').href = '{{ route("admin.manage") }}';
                    document.getElementById('back-link-ticket').textContent = '← Back to Manage';
                }
            }
            const res = await api('/tickets/' + TICKET_ID);
            document.getElementById('loading').classList.add('hidden');
            if (!res.ok) {
                if (res.status === 403 || res.status === 404) {
                    document.getElementById('access-denied').classList.remove('hidden');
                } else {
                    document.getElementById('need-login').classList.remove('hidden');
                }
                return;
            }
            const ticket = res.data;
            document.getElementById('ticket-page').classList.remove('hidden');
            renderTicket(ticket);
            renderComments(ticket.comments || []);
        }
        document.getElementById('comment-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const body = document.getElementById('comment-body').value.trim();
            if (!body) { showToast('Comment cannot be empty'); return; }
            const btn = document.getElementById('comment-submit');
            btn.disabled = true;
            const { ok, data } = await api('/tickets/' + TICKET_ID + '/comments', { method: 'POST', body: JSON.stringify({ body }) });
            btn.disabled = false;
            if (ok) {
                document.getElementById('comment-body').value = '';
                showToast('Comment added');
                const comments = document.querySelectorAll('#comments-list > div');
                const list = document.getElementById('comments-list');
                if (list.querySelector('.comments-empty-placeholder')) {
                    list.innerHTML = '';
                }
                const user = data.user || {};
                const div = document.createElement('div');
                div.className = 'p-6';
                div.innerHTML = '<p class="text-slate-700 whitespace-pre-wrap">' + esc(data.body) + '</p><p class="mt-2 text-sm text-slate-500">' + esc(user.name || 'You') + ' · ' + (data.created_at ? new Date(data.created_at).toLocaleString() : '') + '</p>';
                list.appendChild(div);
            } else {
                showToast(data.message || 'Failed to add comment');
            }
        });
        document.getElementById('btn-logout').addEventListener('click', async () => {
            await api('/logout', { method: 'POST' });
            localStorage.removeItem(TOKEN_KEY);
            window.location.href = '{{ url("/login") }}';
        });
        loadTicket();
    </script>
</body>
</html>
