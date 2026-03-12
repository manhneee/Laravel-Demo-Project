<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Test UI – {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <style>body { font-family: 'Figtree', sans-serif; }</style>
</head>
<body class="bg-slate-100 min-h-screen antialiased">
    <div class="max-w-6xl mx-auto py-6 px-4">
        <h1 class="text-2xl font-bold text-slate-800 mb-2">API Test UI</h1>
        <p class="text-slate-600 mb-6">Test auth, tickets, labels, categories, and admin users. Token is stored in localStorage.</p>

        <div id="toast" class="fixed top-4 right-4 rounded-lg px-4 py-2 shadow-lg z-50 hidden"></div>

        <!-- Auth bar -->
        <div id="auth-bar" class="bg-white rounded-xl shadow border border-slate-200 p-4 mb-6 flex flex-wrap items-center gap-4">
            <div id="auth-forms" class="flex flex-wrap gap-4 items-end">
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">Email</label>
                    <input type="email" id="login-email" placeholder="Email" class="rounded border border-slate-300 px-3 py-1.5 text-sm w-40">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">Password</label>
                    <input type="password" id="login-password" placeholder="Password" class="rounded border border-slate-300 px-3 py-1.5 text-sm w-32">
                </div>
                <button type="button" id="btn-login" class="rounded bg-indigo-600 text-white px-3 py-1.5 text-sm hover:bg-indigo-700">Login</button>
                <span class="text-slate-400">|</span>
                <button type="button" id="btn-show-register" class="rounded bg-slate-600 text-white px-3 py-1.5 text-sm hover:bg-slate-700">Register</button>
            </div>
            <div id="user-info" class="hidden items-center gap-3">
                <span class="text-sm text-slate-700"><strong id="user-name"></strong> <span id="user-role-badge" class="text-slate-500"></span></span>
                <button type="button" id="btn-logout" class="rounded bg-red-600 text-white px-3 py-1.5 text-sm hover:bg-red-700">Logout</button>
            </div>
        </div>

        <!-- Register modal -->
        <div id="register-modal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-40">
            <div class="bg-white rounded-xl shadow-xl p-6 max-w-sm w-full mx-4">
                <h2 class="text-lg font-semibold mb-4">Register</h2>
                <div class="space-y-3">
                    <input type="text" id="reg-name" placeholder="Name" class="w-full rounded border border-slate-300 px-3 py-2 text-sm">
                    <input type="email" id="reg-email" placeholder="Email" class="w-full rounded border border-slate-300 px-3 py-2 text-sm">
                    <input type="password" id="reg-password" placeholder="Password" class="w-full rounded border border-slate-300 px-3 py-2 text-sm">
                    <input type="password" id="reg-password-confirm" placeholder="Confirm password" class="w-full rounded border border-slate-300 px-3 py-2 text-sm">
                </div>
                <div class="mt-4 flex gap-2">
                    <button type="button" id="btn-register" class="rounded bg-indigo-600 text-white px-4 py-2 text-sm hover:bg-indigo-700">Register</button>
                    <button type="button" id="btn-close-register" class="rounded bg-slate-200 text-slate-700 px-4 py-2 text-sm hover:bg-slate-300">Cancel</button>
                </div>
            </div>
        </div>

        <!-- Tabs (Labels, Categories, Admin require admin credential; visibility set by JS after login) -->
        <div class="flex gap-2 mb-4 border-b border-slate-200">
            <button type="button" data-tab="tickets" class="tab-btn rounded-t px-4 py-2 text-sm font-medium bg-slate-200 text-slate-700">Tickets</button>
            <button type="button" data-tab="labels" class="tab-btn admin-only-tab rounded-t px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 hidden">Labels</button>
            <button type="button" data-tab="categories" class="tab-btn admin-only-tab rounded-t px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 hidden">Categories</button>
            <button type="button" data-tab="admin" class="tab-btn admin-only-tab rounded-t px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 hidden">Admin Users</button>
        </div>

        <!-- Tickets -->
        <div id="panel-tickets" class="tab-panel bg-white rounded-xl shadow border border-slate-200 p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold">Tickets</h2>
                <button type="button" id="btn-new-ticket" class="rounded bg-green-600 text-white px-3 py-1.5 text-sm hover:bg-green-700">New Ticket</button>
            </div>
            <div id="tickets-list" class="text-slate-500 text-sm">Load tickets (login first).</div>
            <div id="ticket-form" class="hidden mt-4 p-4 bg-slate-50 rounded-lg border border-slate-200 space-y-4">
                <h3 class="font-medium text-slate-800" id="ticket-form-title">New Ticket</h3>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Title <span class="text-red-500">*</span></label>
                    <input type="text" id="ticket-title" placeholder="Ticket title" class="w-full rounded border border-slate-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Description <span class="text-red-500">*</span></label>
                    <textarea id="ticket-description" placeholder="Describe the issue or request" rows="3" class="w-full rounded border border-slate-300 px-3 py-2 text-sm"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Priority <span class="text-red-500">*</span></label>
                    <select id="ticket-priority" class="w-full max-w-xs rounded border border-slate-300 px-3 py-2 text-sm">
                        <option value="">— Select priority —</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Status</label>
                    <select id="ticket-status" class="w-full max-w-xs rounded border border-slate-300 px-3 py-2 text-sm">
                        <option value="open">Open</option>
                        <option value="in_progress">In progress</option>
                        <option value="pending">Pending</option>
                        <option value="resolved">Resolved</option>
                        <option value="closed">Closed</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Labels</label>
                    <div id="ticket-labels-checkboxes" class="flex flex-wrap gap-3 mt-1"></div>
                    <p class="text-xs text-slate-500 mt-1">Add labels in the Labels tab if none appear.</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Categories</label>
                    <div id="ticket-categories-checkboxes" class="flex flex-wrap gap-3 mt-1"></div>
                    <p class="text-xs text-slate-500 mt-1">Add categories in the Categories tab if none appear.</p>
                </div>
                <div id="ticket-assign-wrap" class="hidden">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Assign to (agent)</label>
                    <select id="ticket-assigned-user" class="rounded border border-slate-300 px-3 py-2 text-sm ml-0"></select>
                </div>
                <div class="flex gap-2 pt-2">
                    <button type="button" id="btn-save-ticket" class="rounded bg-indigo-600 text-white px-4 py-2 text-sm hover:bg-indigo-700">Save</button>
                    <button type="button" id="btn-cancel-ticket" class="rounded bg-slate-200 px-4 py-2 text-sm hover:bg-slate-300">Cancel</button>
                </div>
            </div>
            <div id="ticket-detail" class="hidden mt-4 p-4 bg-slate-50 rounded-lg"></div>
        </div>

        <!-- Labels -->
        <div id="panel-labels" class="tab-panel admin-only-panel hidden bg-white rounded-xl shadow border border-slate-200 p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold">Labels</h2>
                <div class="flex gap-2">
                    <input type="text" id="label-name" placeholder="Label name" class="rounded border border-slate-300 px-3 py-1.5 text-sm w-40">
                    <button type="button" id="btn-add-label" class="rounded bg-green-600 text-white px-3 py-1.5 text-sm hover:bg-green-700">Add</button>
                </div>
            </div>
            <div id="labels-list" class="text-slate-500 text-sm">Loading...</div>
        </div>

        <!-- Categories -->
        <div id="panel-categories" class="tab-panel admin-only-panel hidden bg-white rounded-xl shadow border border-slate-200 p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold">Categories</h2>
                <div class="flex gap-2">
                    <input type="text" id="category-name" placeholder="Name" class="rounded border border-slate-300 px-3 py-1.5 text-sm w-40">
                    <input type="text" id="category-desc" placeholder="Description" class="rounded border border-slate-300 px-3 py-1.5 text-sm w-48">
                    <button type="button" id="btn-add-category" class="rounded bg-green-600 text-white px-3 py-1.5 text-sm hover:bg-green-700">Add</button>
                </div>
            </div>
            <div id="categories-list" class="text-slate-500 text-sm">Loading...</div>
        </div>

        <!-- Admin Users -->
        <div id="panel-admin" class="tab-panel admin-only-panel hidden bg-white rounded-xl shadow border border-slate-200 p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold">Users (Admin)</h2>
                <div class="flex gap-2 flex-wrap">
                    <input type="text" id="user-name" placeholder="Name" class="rounded border border-slate-300 px-3 py-1.5 text-sm w-28">
                    <input type="email" id="user-email" placeholder="Email" class="rounded border border-slate-300 px-3 py-1.5 text-sm w-40">
                    <input type="password" id="user-password" placeholder="Password" class="rounded border border-slate-300 px-3 py-1.5 text-sm w-28">
                    <select id="user-role" class="rounded border border-slate-300 px-3 py-1.5 text-sm">
                        <option value="user">User</option>
                        <option value="agent">Agent</option>
                        <option value="admin">Admin</option>
                    </select>
                    <button type="button" id="btn-add-user" class="rounded bg-green-600 text-white px-3 py-1.5 text-sm hover:bg-green-700">Add User</button>
                </div>
            </div>
            <div id="admin-users-list" class="text-slate-500 text-sm">Loading... (admin only)</div>
        </div>
    </div>

    <script>
        const API = '{{ url("/api") }}';
        const TOKEN_KEY = 'auth_token';

        function getToken() { return localStorage.getItem(TOKEN_KEY); }
        function setToken(t) { t ? localStorage.setItem(TOKEN_KEY, t) : localStorage.removeItem(TOKEN_KEY); }

        function showToast(msg, type = 'info') {
            const el = document.getElementById('toast');
            el.textContent = msg;
            el.className = 'fixed top-4 right-4 rounded-lg px-4 py-2 shadow-lg z-50 ' +
                (type === 'error' ? 'bg-red-500 text-white' : type === 'success' ? 'bg-green-600 text-white' : 'bg-slate-700 text-white');
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

        let currentUser = null;
        let editingTicketId = null;
        let priorities = [], labels = [], categories = [], adminUsers = [];

        function setUser(user) {
            currentUser = user;
            const authForms = document.getElementById('auth-forms');
            const userInfo = document.getElementById('user-info');
            if (user) {
                authForms.classList.add('hidden');
                userInfo.classList.remove('hidden');
                document.getElementById('user-name').textContent = user.name;
                document.getElementById('user-role-badge').textContent = '(' + (user.role || 'user') + ')';
                // Show Labels, Categories, Admin tabs only for admin credential
                const isAdmin = user.role === 'admin';
                document.querySelectorAll('.admin-only-tab').forEach(el => el.classList.toggle('hidden', !isAdmin));
            } else {
                authForms.classList.remove('hidden');
                userInfo.classList.add('hidden');
                document.querySelectorAll('.admin-only-tab').forEach(el => el.classList.add('hidden'));
            }
        }

        async function loadUser() {
            if (!getToken()) { setUser(null); return; }
            const { ok, data } = await api('/user');
            if (ok && data.user) setUser(data.user);
            else setUser(null);
        }

        document.getElementById('btn-login').addEventListener('click', async () => {
            const email = document.getElementById('login-email').value.trim();
            const password = document.getElementById('login-password').value;
            if (!email || !password) { showToast('Email and password required', 'error'); return; }
            const { ok, data } = await api('/login', { method: 'POST', body: JSON.stringify({ email, password }) });
            if (ok && data.token) {
                setToken(data.token);
                setUser(data.user);
                showToast('Logged in', 'success');
                loadPriorities();
                loadTickets();
                loadLabels();
                loadCategories();
                if (data.user?.role === 'admin') loadAdminUsers();
            } else showToast(data.message || 'Login failed', 'error');
        });

        document.getElementById('btn-show-register').addEventListener('click', () => document.getElementById('register-modal').classList.remove('hidden'));
        document.getElementById('btn-close-register').addEventListener('click', () => document.getElementById('register-modal').classList.add('hidden'));
        document.getElementById('btn-register').addEventListener('click', async () => {
            const name = document.getElementById('reg-name').value.trim();
            const email = document.getElementById('reg-email').value.trim();
            const password = document.getElementById('reg-password').value;
            const confirm = document.getElementById('reg-password-confirm').value;
            if (!name || !email || !password) { showToast('Fill all fields', 'error'); return; }
            if (password !== confirm) { showToast('Passwords do not match', 'error'); return; }
            const { ok, data } = await api('/register', { method: 'POST', body: JSON.stringify({ name, email, password, password_confirmation: confirm }) });
            if (ok && data.token) {
                setToken(data.token);
                setUser(data.user);
                document.getElementById('register-modal').classList.add('hidden');
                showToast('Registered', 'success');
                loadTickets(); loadLabels(); loadCategories();
            } else showToast(data.message || (data.errors ? JSON.stringify(data.errors) : 'Register failed'), 'error');
        });

        document.getElementById('btn-logout').addEventListener('click', async () => {
            await api('/logout', { method: 'POST' });
            setToken(null);
            setUser(null);
            showToast('Logged out', 'success');
            // Refresh tickets table and panels to logged-out state
            document.getElementById('tickets-list').innerHTML = 'Load tickets (login first).';
            document.getElementById('ticket-detail').classList.add('hidden');
            document.getElementById('ticket-detail').innerHTML = '';
            document.getElementById('ticket-form').classList.add('hidden');
            editingTicketId = null;
            document.getElementById('labels-list').innerHTML = 'Login to see labels.';
            document.getElementById('categories-list').innerHTML = 'Login to see categories.';
            document.getElementById('admin-users-list').innerHTML = 'Admin only.';
        });

        async function loadPriorities() {
            const { ok, data } = await api('/priorities');
            priorities = ok ? data : [];
            const sel = document.getElementById('ticket-priority');
            sel.innerHTML = '<option value="">— Select priority —</option>' + (priorities.map(p => `<option value="${p.id}">${p.name}</option>`).join(''));
        }

        async function loadTickets() {
            if (!getToken()) return;
            const { ok, data } = await api('/tickets');
            const el = document.getElementById('tickets-list');
            if (!ok) { el.innerHTML = data.message || 'Failed to load'; return; }
            if (!data.length) { el.innerHTML = 'No tickets.'; return; }
            const desc = (s) => (s && s.length > 40) ? s.substring(0, 40) + '…' : (s || '-');
            const names = (arr) => (arr && arr.length) ? arr.map(x => x.name).join(', ') : '-';
            el.innerHTML = `<div class="overflow-x-auto"><table class="w-full text-sm"><thead><tr class="border-b bg-slate-50">
                <th class="text-left py-2 px-2">#</th>
                <th class="text-left py-2 px-2">Title</th>
                <th class="text-left py-2 px-2 max-w-[120px]">Description</th>
                <th class="text-left py-2 px-2">Status</th>
                <th class="text-left py-2 px-2">Priority</th>
                <th class="text-left py-2 px-2">Created by</th>
                <th class="text-left py-2 px-2">Assigned to</th>
                <th class="text-left py-2 px-2">Labels</th>
                <th class="text-left py-2 px-2">Categories</th>
                <th class="text-left py-2 px-2">Updated</th>
                <th class="text-left py-2 px-2"></th>
            </tr></thead><tbody>${
                data.map(t => `<tr class="border-b border-slate-100 hover:bg-slate-50">
                    <td class="py-2 px-2">${t.id}</td>
                    <td class="py-2 px-2"><a href="#" class="text-indigo-600 ticket-view font-medium" data-id="${t.id}">${(t.title || '-')}</a></td>
                    <td class="py-2 px-2 max-w-[120px] text-slate-600" title="${(t.description || '').replace(/"/g, '&quot;')}">${desc(t.description)}</td>
                    <td class="py-2 px-2">${t.status || '-'}</td>
                    <td class="py-2 px-2">${(t.priority && t.priority.name) ? t.priority.name : (t.priority_name ? '#' + t.priority_name : '-')}</td>                    <td class="py-2 px-2">${t.created_by ? t.created_by.name + ' (' + (t.created_by_id ?? t.created_by.id) + ')' : (t.created_by_id ? '#' + t.created_by_id : '-')}</td>
                    <td class="py-2 px-2">${t.assigned_user ? t.assigned_user.name + ' (' + (t.assigned_user_id ?? t.assigned_user.id) + ')' : (t.assigned_user_id ? '#' + t.assigned_user_id : '-')}</td>
                    <td class="py-2 px-2 text-slate-600">${names(t.labels)}</td>
                    <td class="py-2 px-2 text-slate-600">${names(t.categories)}</td>
                    <td class="py-2 px-2 text-slate-500">${t.updated_at ? new Date(t.updated_at).toLocaleDateString() : '-'}</td>
                    <td class="py-2 px-2 whitespace-nowrap">
                        <button type="button" class="ticket-edit text-indigo-600 mr-2" data-id="${t.id}">Edit</button>
                        <button type="button" class="ticket-delete text-red-600" data-id="${t.id}">Delete</button>
                    </td>
                </tr>`).join('')
            }</tbody></table></div>`;
            el.querySelectorAll('.ticket-view').forEach(btn => btn.addEventListener('click', e => { e.preventDefault(); showTicket(Number(e.currentTarget.dataset.id)); }));
            el.querySelectorAll('.ticket-edit').forEach(btn => btn.addEventListener('click', e => editTicket(Number(e.currentTarget.dataset.id))));
            el.querySelectorAll('.ticket-delete').forEach(btn => btn.addEventListener('click', e => deleteTicket(Number(e.currentTarget.dataset.id))));
        }

        function showTicket(id) {
            api('/tickets/' + id).then(({ ok, data }) => {
                const panel = document.getElementById('ticket-detail');
                const list = document.getElementById('tickets-list');
                if (!ok) { showToast(data.message || 'Not found', 'error'); return; }
                panel.classList.remove('hidden');
                const lbls = (data.labels && data.labels.length) ? data.labels.map(x => x.name).join(', ') : '-';
                const cats = (data.categories && data.categories.length) ? data.categories.map(x => x.name).join(', ') : '-';
                const esc = (s) => (s || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
                panel.innerHTML = `
                    <p class="font-semibold text-lg">#${data.id} ${esc(data.title)}</p>
                    <dl class="mt-3 space-y-1 text-sm">
                        <dt class="text-slate-500">Description</dt><dd class="text-slate-700 whitespace-pre-wrap mb-2">${esc(data.description) || '-'}</dd>
                        <dt class="text-slate-500">Status</dt><dd>${data.status || '-'}</dd>
                        <dt class="text-slate-500">Priority</dt><dd>${data.priority?.name || '-'}</dd>
                        <dt class="text-slate-500">Created by</dt><dd>${data.created_by?.name || '-'}</dd>
                        <dt class="text-slate-500">Assigned to</dt><dd>${data.assigned_user?.name || '-'}</dd>
                        <dt class="text-slate-500">Labels</dt><dd>${lbls}</dd>
                        <dt class="text-slate-500">Categories</dt><dd>${cats}</dd>
                        <dt class="text-slate-500">Created at</dt><dd>${data.created_at ? new Date(data.created_at).toLocaleString() : '-'}</dd>
                        <dt class="text-slate-500">Updated at</dt><dd>${data.updated_at ? new Date(data.updated_at).toLocaleString() : '-'}</dd>
                    </dl>
                    <button type="button" id="btn-close-detail" class="mt-3 rounded bg-slate-200 px-3 py-1 text-sm hover:bg-slate-300">Close</button>`;
                document.getElementById('btn-close-detail').onclick = () => { panel.classList.add('hidden'); };
            });
        }

        document.getElementById('btn-new-ticket').addEventListener('click', async () => {
            editingTicketId = null;
            document.getElementById('ticket-form-title').textContent = 'New Ticket';
            document.getElementById('ticket-title').value = '';
            document.getElementById('ticket-description').value = '';
            document.getElementById('ticket-status').value = 'open';
            document.getElementById('ticket-priority').value = '';
            document.getElementById('ticket-form').classList.remove('hidden');
            document.getElementById('ticket-detail').classList.add('hidden');
            document.getElementById('ticket-assign-wrap').classList.toggle('hidden', currentUser?.role !== 'admin');
            if (currentUser?.role === 'admin') {
                const sel = document.getElementById('ticket-assigned-user');
                sel.innerHTML = '<option value="">— Unassigned —</option>' + (adminUsers || []).map(u => `<option value="${u.id}">${u.name} (${u.role})</option>`).join('');
            }
            if (!priorities.length) await loadPriorities();
            if (!labels.length) await loadLabels();
            if (!categories.length) await loadCategories();
            renderTicketLabelCategoryChecks();
        });

        document.getElementById('btn-cancel-ticket').addEventListener('click', () => {
            document.getElementById('ticket-form').classList.add('hidden');
            editingTicketId = null;
        });

        function renderTicketLabelCategoryChecks() {
            const lc = document.getElementById('ticket-labels-checkboxes');
            const cc = document.getElementById('ticket-categories-checkboxes');
            lc.innerHTML = (labels && labels.length)
                ? labels.map(l => `<label class="inline-flex items-center gap-1.5"><input type="checkbox" class="ticket-label-cb" value="${l.id}"><span>${l.name}</span></label>`).join('')
                : '<span class="text-slate-500 text-sm">No labels yet. Add some in the Labels tab.</span>';
            cc.innerHTML = (categories && categories.length)
                ? categories.map(c => `<label class="inline-flex items-center gap-1.5"><input type="checkbox" class="ticket-category-cb" value="${c.id}"><span>${c.name}</span></label>`).join('')
                : '<span class="text-slate-500 text-sm">No categories yet. Add some in the Categories tab.</span>';
        }

        async function editTicket(id) {
            const { ok, data } = await api('/tickets/' + id);
            if (!ok) { showToast(data.message || 'Forbidden', 'error'); return; }
            editingTicketId = id;
            document.getElementById('ticket-form-title').textContent = 'Edit Ticket';
            document.getElementById('ticket-title').value = data.title;
            document.getElementById('ticket-description').value = data.description;
            document.getElementById('ticket-status').value = data.status;
            document.getElementById('ticket-priority').value = data.priority_id || data.priority?.id || '';
            document.getElementById('ticket-form').classList.remove('hidden');
            document.getElementById('ticket-detail').classList.add('hidden');
            document.getElementById('ticket-assign-wrap').classList.toggle('hidden', currentUser?.role !== 'admin');
            if (currentUser?.role === 'admin') {
                const sel = document.getElementById('ticket-assigned-user');
                sel.innerHTML = '<option value="">— Unassigned —</option>' + adminUsers.map(u => `<option value="${u.id}" ${u.id === data.assigned_user_id ? 'selected' : ''}>${u.name} (${u.role})</option>`).join('');
            }
            renderTicketLabelCategoryChecks();
            setTimeout(() => {
                (data.labels || []).forEach(l => { const cb = document.querySelector('.ticket-label-cb[value="' + (l.id || l) + '"]'); if (cb) cb.checked = true; });
                (data.categories || []).forEach(c => { const cb = document.querySelector('.ticket-category-cb[value="' + (c.id || c) + '"]'); if (cb) cb.checked = true; });
            }, 0);
        }

        document.getElementById('btn-save-ticket').addEventListener('click', async () => {
            const title = document.getElementById('ticket-title').value.trim();
            const description = document.getElementById('ticket-description').value.trim();
            const priority_id = document.getElementById('ticket-priority').value;
            const status = document.getElementById('ticket-status').value;
            const labelIds = Array.from(document.querySelectorAll('.ticket-label-cb:checked')).map(cb => Number(cb.value));
            const categoryIds = Array.from(document.querySelectorAll('.ticket-category-cb:checked')).map(cb => Number(cb.value));
            if (!title || !description || !priority_id) { showToast('Title, description, priority required', 'error'); return; }
            const body = { title, description, priority_id: Number(priority_id), status, labels: labelIds, categories: categoryIds };
            if (currentUser?.role === 'admin') body.assigned_user_id = document.getElementById('ticket-assigned-user').value ? Number(document.getElementById('ticket-assigned-user').value) : null;
            const url = editingTicketId ? '/tickets/' + editingTicketId : '/tickets';
            const method = editingTicketId ? 'PUT' : 'POST';
            const { ok, data } = await api(url, { method, body: JSON.stringify(body) });
            if (ok) {
                showToast(editingTicketId ? 'Ticket updated' : 'Ticket created', 'success');
                document.getElementById('ticket-form').classList.add('hidden');
                editingTicketId = null;
                loadTickets();
            } else showToast(data.message || (data.errors ? JSON.stringify(data.errors) : 'Failed'), 'error');
        });

        async function deleteTicket(id) {
            if (!confirm('Delete this ticket?')) return;
            const { ok, data } = await api('/tickets/' + id, { method: 'DELETE' });
            if (ok) { showToast('Deleted', 'success'); loadTickets(); }
            else showToast(data.message || 'Forbidden', 'error');
        }

        async function loadLabels() {
            if (!getToken()) return;
            const { ok, data } = await api('/labels');
            labels = ok ? data : [];
            const el = document.getElementById('labels-list');
            if (!ok) { el.innerHTML = 'Failed'; return; }
            el.innerHTML = labels.length ? labels.map(l => `<div class="flex items-center justify-between py-1 border-b border-slate-100"><span>${l.name}</span><button type="button" class="label-delete text-red-600 text-sm" data-id="${l.id}">Delete</button></div>`).join('') : 'No labels.';
            el.querySelectorAll('.label-delete').forEach(btn => {
                btn.addEventListener('click', async (e) => {
                    const id = e.currentTarget.dataset.id;
                    const { ok } = await api('/labels/' + id, { method: 'DELETE' });
                    if (ok) { showToast('Label deleted'); loadLabels(); loadTickets(); } else showToast('Failed', 'error');
                });
            });
        }

        document.getElementById('btn-add-label').addEventListener('click', async () => {
            const name = document.getElementById('label-name').value.trim();
            if (!name) { showToast('Name required', 'error'); return; }
            const { ok } = await api('/labels', { method: 'POST', body: JSON.stringify({ name }) });
            if (ok) { showToast('Label added'); document.getElementById('label-name').value = ''; loadLabels(); }
            else showToast('Failed', 'error');
        });

        async function loadCategories() {
            if (!getToken()) return;
            const { ok, data } = await api('/categories');
            categories = ok ? data : [];
            const el = document.getElementById('categories-list');
            if (!ok) { el.innerHTML = 'Failed'; return; }
            el.innerHTML = categories.length ? categories.map(c => `<div class="flex items-center justify-between py-1 border-b border-slate-100"><span>${c.name} ${c.description ? '— ' + c.description : ''}</span><button type="button" class="category-delete text-red-600 text-sm" data-id="${c.id}">Delete</button></div>`).join('') : 'No categories.';
            el.querySelectorAll('.category-delete').forEach(btn => {
                btn.addEventListener('click', async (e) => {
                    const id = e.currentTarget.dataset.id;
                    const { ok } = await api('/categories/' + id, { method: 'DELETE' });
                    if (ok) { showToast('Category deleted'); loadCategories(); loadTickets(); } else showToast('Failed', 'error');
                });
            });
        }

        document.getElementById('btn-add-category').addEventListener('click', async () => {
            const name = document.getElementById('category-name').value.trim();
            const description = document.getElementById('category-desc').value.trim();
            if (!name) { showToast('Name required', 'error'); return; }
            const { ok } = await api('/categories', { method: 'POST', body: JSON.stringify({ name, description }) });
            if (ok) { showToast('Category added'); document.getElementById('category-name').value = ''; document.getElementById('category-desc').value = ''; loadCategories(); }
            else showToast('Failed', 'error');
        });

        async function loadAdminUsers() {
            if (!getToken() || currentUser?.role !== 'admin') return;
            const { ok, data } = await api('/admin/users');
            adminUsers = ok ? data : [];
            const el = document.getElementById('admin-users-list');
            if (!ok) { el.innerHTML = data.message || 'Forbidden'; return; }
            el.innerHTML = adminUsers.length ? data.map(u => {
                const role = u.role || 'user';
                return `<div class="flex items-center justify-between py-2 border-b border-slate-100 gap-2">
                    <span class="min-w-0">${u.name} (${u.email})</span>
                    <select class="admin-user-role rounded border border-slate-300 px-2 py-1 text-sm w-24 shrink-0" data-id="${u.id}" data-email="${(u.email || '').replace(/"/g, '&quot;')}">
                        <option value="user" ${role === 'user' ? 'selected' : ''}>User</option>
                        <option value="agent" ${role === 'agent' ? 'selected' : ''}>Agent</option>
                        <option value="admin" ${role === 'admin' ? 'selected' : ''}>Admin</option>
                    </select>
                    <button type="button" class="admin-user-delete text-red-600 text-sm shrink-0" data-id="${u.id}">Delete</button>
                </div>`;
            }).join('') : 'No users.';
            el.querySelectorAll('.admin-user-role').forEach(sel => {
                sel.addEventListener('change', async (e) => {
                    const id = e.currentTarget.dataset.id;
                    const role = e.currentTarget.value;
                    const { ok, data } = await api('/admin/users/' + id, { method: 'PUT', body: JSON.stringify({ role }) });
                    if (ok) { showToast('Role updated'); loadAdminUsers(); } else showToast(data.message || 'Failed', 'error'); 
                });
            });
            el.querySelectorAll('.admin-user-delete').forEach(btn => {
                btn.addEventListener('click', async (e) => {
                    const id = e.currentTarget.dataset.id;
                    const { ok } = await api('/admin/users/' + id, { method: 'DELETE' });
                    if (ok) { showToast('User deleted'); loadAdminUsers(); } else showToast('Failed', 'error');
                });
            });
        }

        document.getElementById('btn-add-user').addEventListener('click', async () => {
            const name = document.getElementById('user-name').value.trim();
            const email = document.getElementById('user-email').value.trim();
            const password = document.getElementById('user-password').value;
            const role = document.getElementById('user-role').value;
            if (!name || !email || !password) { showToast('Name, email, password required', 'error'); return; }
            const { ok, data } = await api('/admin/users', { method: 'POST', body: JSON.stringify({ name, email, password, role }) });
            if (ok) { showToast('User added'); document.getElementById('user-name').value = ''; document.getElementById('user-email').value = ''; document.getElementById('user-password').value = ''; loadAdminUsers(); }
            else showToast(data.message || (data.errors ? JSON.stringify(data.errors) : 'Failed'), 'error');
        });

        document.querySelectorAll('.tab-btn').forEach(tabBtn => {
            tabBtn.addEventListener('click', () => {
                document.querySelectorAll('.tab-btn').forEach(t => { t.classList.remove('bg-slate-200', 'text-slate-700'); t.classList.add('text-slate-600'); });
                tabBtn.classList.remove('text-slate-600');
                tabBtn.classList.add('bg-slate-200', 'text-slate-700');
                document.querySelectorAll('.tab-panel').forEach(p => p.classList.add('hidden'));
                const panel = document.getElementById('panel-' + tabBtn.dataset.tab);
                if (panel) panel.classList.remove('hidden');
                if (tabBtn.dataset.tab === 'admin' && currentUser?.role === 'admin') loadAdminUsers();
            });
        });

        if (getToken()) {
            loadUser().then(() => {
                loadPriorities();
                loadTickets();
                loadLabels();
                loadCategories();
                if (currentUser?.role === 'admin') loadAdminUsers();
            });
        } else {
            document.getElementById('tickets-list').innerHTML = 'Login to see tickets.';
            document.getElementById('labels-list').innerHTML = 'Login to see labels.';
            document.getElementById('categories-list').innerHTML = 'Login to see categories.';
            document.getElementById('admin-users-list').innerHTML = 'Admin only.';
        }
    </script>
</body>
</html>
