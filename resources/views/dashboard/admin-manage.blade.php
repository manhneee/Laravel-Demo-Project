<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin – Manage – {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <style>body { font-family: 'Figtree', sans-serif; }</style>
</head>
<body class="bg-slate-100 min-h-screen antialiased">
    <nav class="bg-white border-b border-slate-200 shadow-sm">
        <div class="max-w-6xl mx-auto px-4 py-3 flex justify-between items-center">
            <div class="flex items-center gap-4 flex-wrap">
                <a href="{{ url('/') }}" class="text-slate-800 font-semibold text-lg">Ticket System</a>
                <a href="{{ route('dashboard.admin') }}" class="text-slate-600 hover:text-slate-900">Dashboard</a>
                <a href="{{ route('admin.manage') }}" class="text-indigo-600 font-medium">Manage</a>
            </div>
            <div id="nav-user" class="hidden items-center gap-3">
                <span class="text-sm text-slate-700"><strong id="nav-user-name"></strong> <span class="text-xs bg-indigo-100 text-indigo-800 px-2 py-0.5 rounded">Admin</span></span>
                <button type="button" id="btn-logout" class="rounded bg-red-600 text-white px-3 py-1.5 text-sm hover:bg-red-700">Logout</button>
            </div>
            <div id="nav-guest" class="hidden">
                <a href="{{ url('/login') }}" class="text-indigo-600 font-medium">Log in</a>
            </div>
        </div>
    </nav>

    <main class="max-w-6xl mx-auto px-4 py-6">
        <div id="need-login" class="hidden bg-amber-50 border border-amber-200 rounded-xl p-6 text-center">
            <p class="text-amber-800 mb-4">Please log in to access admin.</p>
            <a href="{{ url('/login') }}" class="inline-block rounded bg-indigo-600 text-white px-4 py-2 text-sm font-medium hover:bg-indigo-700">Go to Login</a>
        </div>
        <div id="access-denied" class="hidden bg-red-50 border border-red-200 rounded-xl p-6 text-center">
            <p class="text-red-800 mb-4">Access denied. Admin only.</p>
            <a href="{{ route('dashboard.user') }}" class="inline-block rounded bg-slate-600 text-white px-4 py-2 text-sm">User Dashboard</a>
        </div>

        <div id="manage-content" class="hidden">
            <h1 class="text-2xl font-bold text-slate-800 mb-4">Manage</h1>
            <div class="flex gap-2 mb-4 border-b border-slate-200 flex-wrap">
                <button type="button" data-tab="tickets" class="admin-tab rounded-t px-4 py-2 text-sm font-medium bg-slate-200 text-slate-700">Tickets</button>
                <button type="button" data-tab="users" class="admin-tab rounded-t px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100">Users</button>
                <button type="button" data-tab="logs" class="admin-tab rounded-t px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100">Ticket Logs</button>
                <button type="button" data-tab="categories" class="admin-tab rounded-t px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100">Categories</button>
                <button type="button" data-tab="labels" class="admin-tab rounded-t px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100">Labels</button>
            </div>

            <div id="panel-tickets" class="admin-panel bg-white rounded-xl border border-slate-200 p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold">Tickets</h2>
                    <button type="button" id="btn-new-ticket" class="rounded bg-green-600 text-white px-3 py-1.5 text-sm font-medium hover:bg-green-700">New Ticket</button>
                </div>
                <div id="tickets-list" class="text-slate-500 text-sm">Loading...</div>
                <div id="ticket-detail" class="hidden mt-4 p-4 bg-slate-50 rounded-lg border border-slate-200"></div>
                <div id="ticket-form" class="hidden mt-4 p-4 bg-slate-50 rounded-lg border border-slate-200 space-y-4">
                    <h3 class="font-medium text-slate-800" id="ticket-form-title">Edit Ticket</h3>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Title <span class="text-red-500">*</span></label>
                        <input type="text" id="ticket-title" class="w-full rounded border border-slate-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Description <span class="text-red-500">*</span></label>
                        <textarea id="ticket-description" rows="3" class="w-full rounded border border-slate-300 px-3 py-2 text-sm"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Status</label>
                        <select id="ticket-status" class="rounded border border-slate-300 px-3 py-2 text-sm">
                            <option value="open">Open</option>
                            <option value="in_progress">In progress</option>
                            <option value="pending">Pending</option>
                            <option value="resolved">Resolved</option>
                            <option value="closed">Closed</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Priority</label>
                        <select id="ticket-priority" class="rounded border border-slate-300 px-3 py-2 text-sm w-full max-w-xs"></select>
                    </div>
                    <div id="ticket-assign-wrap">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Assign to (agent)</label>
                        <select id="ticket-assigned-user" class="rounded border border-slate-300 px-3 py-2 text-sm"></select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Labels</label>
                        <div id="ticket-labels-checkboxes" class="flex flex-wrap gap-3 mt-1"></div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Categories</label>
                        <div id="ticket-categories-checkboxes" class="flex flex-wrap gap-3 mt-1"></div>
                    </div>
                    <div class="flex gap-2">
                        <button type="button" id="btn-save-ticket" class="rounded bg-indigo-600 text-white px-4 py-2 text-sm hover:bg-indigo-700">Save</button>
                        <button type="button" id="btn-cancel-ticket" class="rounded bg-slate-200 px-4 py-2 text-sm hover:bg-slate-300">Cancel</button>
                    </div>
                </div>
            </div>
            <div id="panel-users" class="admin-panel hidden bg-white rounded-xl border border-slate-200 p-6">
                <div class="flex justify-between items-center mb-4 flex-wrap gap-2">
                    <h2 class="text-lg font-semibold">Users</h2>
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
                <div id="users-list" class="text-slate-500 text-sm">Loading...</div>
            </div>
            <div id="panel-logs" class="admin-panel hidden bg-white rounded-xl border border-slate-200 p-6">
                <h2 class="text-lg font-semibold mb-4">Ticket Logs</h2>
                <div id="logs-list" class="text-slate-500 text-sm overflow-x-auto">Loading...</div>
            </div>
            <div id="panel-categories" class="admin-panel hidden bg-white rounded-xl border border-slate-200 p-6">
                <div class="flex justify-between items-center mb-4 flex-wrap gap-2">
                    <h2 class="text-lg font-semibold">Categories</h2>
                    <div class="flex gap-2">
                        <input type="text" id="category-name" placeholder="Name" class="rounded border border-slate-300 px-3 py-1.5 text-sm w-40">
                        <input type="text" id="category-desc" placeholder="Description" class="rounded border border-slate-300 px-3 py-1.5 text-sm w-48">
                        <button type="button" id="btn-add-category" class="rounded bg-green-600 text-white px-3 py-1.5 text-sm hover:bg-green-700">Add</button>
                    </div>
                </div>
                <div id="categories-list" class="text-slate-500 text-sm">Loading...</div>
            </div>
            <div id="panel-labels" class="admin-panel hidden bg-white rounded-xl border border-slate-200 p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold">Labels</h2>
                    <div class="flex gap-2">
                        <input type="text" id="label-name" placeholder="Label name" class="rounded border border-slate-300 px-3 py-1.5 text-sm w-40">
                        <button type="button" id="btn-add-label" class="rounded bg-green-600 text-white px-3 py-1.5 text-sm hover:bg-green-700">Add</button>
                    </div>
                </div>
                <div id="labels-list" class="text-slate-500 text-sm">Loading...</div>
            </div>
        </div>
        <div id="loading" class="text-center py-12 text-slate-500">Loading...</div>
    </main>

    <div id="toast" class="fixed top-4 right-4 rounded-lg px-4 py-2 shadow-lg z-50 hidden bg-slate-800 text-white text-sm"></div>

    <script>
        const API = '{{ url("/api") }}';
        const TICKET_SHOW_URL = '{{ url("/ticket") }}';
        const TICKET_EDIT_URL = '{{ url("/ticket") }}';
        const OPEN_EDIT_TICKET_ID = {{ isset($openEditTicketId) ? (int) $openEditTicketId : 'null' }};
        const TOKEN_KEY = 'auth_token';
        function getToken() { return localStorage.getItem(TOKEN_KEY); }
        function setToken(t) { t ? localStorage.setItem(TOKEN_KEY, t) : localStorage.removeItem(TOKEN_KEY); }
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

        function showNeedLogin() {
            document.getElementById('loading').classList.add('hidden');
            document.getElementById('manage-content').classList.add('hidden');
            document.getElementById('need-login').classList.remove('hidden');
            document.getElementById('access-denied').classList.add('hidden');
            document.getElementById('nav-user').classList.add('hidden');
            document.getElementById('nav-guest').classList.remove('hidden');
        }
        function showAccessDenied() {
            document.getElementById('loading').classList.add('hidden');
            document.getElementById('manage-content').classList.add('hidden');
            document.getElementById('need-login').classList.add('hidden');
            document.getElementById('access-denied').classList.remove('hidden');
            document.getElementById('nav-user').classList.add('hidden');
            document.getElementById('nav-guest').classList.remove('hidden');
        }
        function showContent(userName) {
            document.getElementById('loading').classList.add('hidden');
            document.getElementById('need-login').classList.add('hidden');
            document.getElementById('access-denied').classList.add('hidden');
            document.getElementById('manage-content').classList.remove('hidden');
            document.getElementById('nav-user').classList.remove('hidden');
            document.getElementById('nav-user').classList.add('flex');
            document.getElementById('nav-user-name').textContent = userName;
            document.getElementById('nav-guest').classList.add('hidden');
        }

        let editingTicketId = null;
        let currentUser = null;
        let priorities = [], labels = [], categories = [], adminUsers = [];

        async function loadPriorities() {
            const { ok, data } = await api('/priorities');
            priorities = ok ? data : [];
            const sel = document.getElementById('ticket-priority');
            if (!sel) return;
            sel.innerHTML = '<option value="">— Select —</option>' + (priorities.map(p => `<option value="${p.id}">${p.name}</option>`).join(''));
        }
        function renderTicketLabelCategoryChecks() {
            const lc = document.getElementById('ticket-labels-checkboxes');
            const cc = document.getElementById('ticket-categories-checkboxes');
            if (!lc || !cc) return;
            lc.innerHTML = (labels && labels.length) ? labels.map(l => `<label class="inline-flex items-center gap-1.5"><input type="checkbox" class="ticket-label-cb" value="${l.id}"><span>${l.name}</span></label>`).join('') : '<span class="text-slate-500 text-sm">No labels.</span>';
            cc.innerHTML = (categories && categories.length) ? categories.map(c => `<label class="inline-flex items-center gap-1.5"><input type="checkbox" class="ticket-category-cb" value="${c.id}"><span>${c.name}</span></label>`).join('') : '<span class="text-slate-500 text-sm">No categories.</span>';
        }

        function showTicket(id) {
            api('/tickets/' + id).then(({ ok, data }) => {
                const panel = document.getElementById('ticket-detail');
                if (!ok) { showToast(data.message || 'Not found'); return; }
                panel.classList.remove('hidden');
                document.getElementById('ticket-form').classList.add('hidden');
                const lbls = (data.labels && data.labels.length) ? data.labels.map(x => x.name).join(', ') : '—';
                const cats = (data.categories && data.categories.length) ? data.categories.map(x => x.name).join(', ') : '—';
                const esc = (s) => (s || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
                const cb = data.created_by || data.createdBy;
                const au = data.assigned_user || data.assignedUser;
                panel.innerHTML = `
                    <p class="font-semibold text-lg">#${data.id} ${esc(data.title)}</p>
                    <dl class="mt-3 space-y-1 text-sm">
                        <dt class="text-slate-500">Description</dt><dd class="text-slate-700 whitespace-pre-wrap mb-2">${esc(data.description) || '—'}</dd>
                        <dt class="text-slate-500">Status</dt><dd>${data.status || '—'}</dd>
                        <dt class="text-slate-500">Priority</dt><dd>${(data.priority && data.priority.name) || '—'}</dd>
                        <dt class="text-slate-500">Created by</dt><dd>${cb ? (cb.name + ' (#' + (data.created_by_id || cb.id) + ')') : (data.created_by_id ? '#' + data.created_by_id : '—')}</dd>
                        <dt class="text-slate-500">Assigned to</dt><dd>${au ? (au.name + ' (#' + (data.assigned_user_id || au.id) + ')') : (data.assigned_user_id ? '#' + data.assigned_user_id : '—')}</dd>
                        <dt class="text-slate-500">Labels</dt><dd>${lbls}</dd>
                        <dt class="text-slate-500">Categories</dt><dd>${cats}</dd>
                        <dt class="text-slate-500">Created at</dt><dd>${data.created_at ? new Date(data.created_at).toLocaleString() : '—'}</dd>
                        <dt class="text-slate-500">Updated at</dt><dd>${data.updated_at ? new Date(data.updated_at).toLocaleString() : '—'}</dd>
                    </dl>
                    <div class="mt-3 flex gap-2">
                        <button type="button" class="ticket-edit-btn rounded bg-indigo-600 text-white px-3 py-1 text-sm hover:bg-indigo-700" data-id="${data.id}">Edit</button>
                        <button type="button" class="ticket-delete-btn rounded bg-red-600 text-white px-3 py-1 text-sm hover:bg-red-700" data-id="${data.id}">Delete</button>
                        <button type="button" id="btn-close-detail" class="rounded bg-slate-200 px-3 py-1 text-sm hover:bg-slate-300">Close</button>
                    </div>`;
                document.getElementById('btn-close-detail').onclick = () => { panel.classList.add('hidden'); };
                panel.querySelector('.ticket-edit-btn').onclick = (e) => { panel.classList.add('hidden'); editTicket(Number(e.currentTarget.dataset.id)); };
                panel.querySelector('.ticket-delete-btn').onclick = (e) => deleteTicket(Number(e.currentTarget.dataset.id));
            });
        }
        async function openNewTicketForm() {
            editingTicketId = null;
            document.getElementById('ticket-form-title').textContent = 'New Ticket';
            document.getElementById('ticket-title').value = '';
            document.getElementById('ticket-description').value = '';
            document.getElementById('ticket-status').value = 'open';
            document.getElementById('ticket-priority').value = '';
            document.getElementById('ticket-detail').classList.add('hidden');
            document.getElementById('ticket-form').classList.remove('hidden');
            if (!priorities.length) await loadPriorities();
            if (!labels.length) { const r = await api('/labels'); labels = r.ok ? r.data : []; }
            if (!categories.length) { const r = await api('/categories'); categories = r.ok ? r.data : []; }
            if (!adminUsers.length) { const r = await api('/admin/users'); adminUsers = r.ok ? r.data : []; }
            renderTicketLabelCategoryChecks();
            const sel = document.getElementById('ticket-assigned-user');
            sel.innerHTML = '<option value="">— Unassigned —</option>' +  adminUsers.filter(u => u.role == 'agent').map(u => `<option value="${u.id}">${u.name} (id: ${u.id})</option>`).join('');
            document.querySelectorAll('.ticket-label-cb').forEach(cb => { cb.checked = false; });
            document.querySelectorAll('.ticket-category-cb').forEach(cb => { cb.checked = false; });
        }
        document.getElementById('btn-new-ticket').addEventListener('click', openNewTicketForm);

        async function editTicket(id) {
            const { ok, data } = await api('/tickets/' + id);
            if (!ok) { showToast(data.message || 'Failed'); return; }
            editingTicketId = id;
            document.getElementById('ticket-form-title').textContent = 'Edit Ticket #' + id;
            document.getElementById('ticket-detail').classList.add('hidden');
            document.getElementById('ticket-form').classList.remove('hidden');
            if (!priorities.length) { const r = await api('/priorities'); priorities = r.ok ? r.data : []; }
            if (!labels.length) { const r = await api('/labels'); labels = r.ok ? r.data : []; }
            if (!categories.length) { const r = await api('/categories'); categories = r.ok ? r.data : []; }
            if (!adminUsers.length) { const r = await api('/admin/users'); adminUsers = r.ok ? r.data : []; }
            await loadPriorities();
            renderTicketLabelCategoryChecks();
            const priorityId = data.priority_id || (data.priority && data.priority.id) || '';
            document.getElementById('ticket-title').value = data.title || '';
            document.getElementById('ticket-description').value = data.description || '';
            document.getElementById('ticket-status').value = data.status || 'open';
            document.getElementById('ticket-priority').value = String(priorityId);
            const createdById = data.created_by_id || (data.created_by && data.created_by.id);
            const isCreator = currentUser && Number(createdById) === Number(currentUser.id);
            const assignWrap = document.getElementById('ticket-assign-wrap');
            if (isCreator) assignWrap.classList.add('hidden');
            else assignWrap.classList.remove('hidden');
            const sel = document.getElementById('ticket-assigned-user');
            sel.innerHTML = '<option value="">— Unassigned —</option>' + adminUsers.filter(u => u.role == 'agent').map(u => `<option value="${u.id}" ${u.id === data.assigned_user_id ? 'selected' : ''}>${u.name} (${u.role} #${u.id})</option>`).join('');
            setTimeout(() => {
                (data.labels || []).forEach(l => { const cb = document.querySelector('.ticket-label-cb[value="' + (l.id || l) + '"]'); if (cb) cb.checked = true; });
                (data.categories || []).forEach(c => { const cb = document.querySelector('.ticket-category-cb[value="' + (c.id || c) + '"]'); if (cb) cb.checked = true; });
            }, 0);
        }
        async function deleteTicket(id) {
            if (!confirm('Delete this ticket?')) return;
            const { ok, data } = await api('/tickets/' + id, { method: 'DELETE' });
            if (ok) { showToast('Ticket deleted'); document.getElementById('ticket-detail').classList.add('hidden'); document.getElementById('ticket-form').classList.add('hidden'); editingTicketId = null; loadTickets(); }
            else showToast(data.message || 'Failed');
        }

        document.getElementById('btn-save-ticket').addEventListener('click', async () => {
            const title = document.getElementById('ticket-title').value.trim();
            const description = document.getElementById('ticket-description').value.trim();
            const priority_id = document.getElementById('ticket-priority').value;
            const status = document.getElementById('ticket-status').value;
            const labelIds = Array.from(document.querySelectorAll('.ticket-label-cb:checked')).map(cb => Number(cb.value));
            const categoryIds = Array.from(document.querySelectorAll('.ticket-category-cb:checked')).map(cb => Number(cb.value));
            if (!title || !description || !priority_id) { showToast('Title, description, priority required'); return; }
            const body = { title, description, priority_id: Number(priority_id), status, labels: labelIds, categories: categoryIds };
            const assignWrap = document.getElementById('ticket-assign-wrap');
            body.assigned_user_id = assignWrap.classList.contains('hidden') ? null : (document.getElementById('ticket-assigned-user').value ? Number(document.getElementById('ticket-assigned-user').value) : null);
            const isCreate = editingTicketId === null;
            const url = isCreate ? '/tickets' : '/tickets/' + editingTicketId;
            const method = isCreate ? 'POST' : 'PUT';
            const { ok, data } = await api(url, { method, body: JSON.stringify(body) });
            if (ok) { showToast(isCreate ? 'Ticket created' : 'Ticket updated'); document.getElementById('ticket-form').classList.add('hidden'); editingTicketId = null; loadTickets(); }
            else showToast(data.message || 'Failed');
        });
        document.getElementById('btn-cancel-ticket').addEventListener('click', () => {
            document.getElementById('ticket-form').classList.add('hidden');
            editingTicketId = null;
        });

        async function loadTickets() {
            const { ok, data } = await api('/tickets');
            const el = document.getElementById('tickets-list');
            if (!ok) { el.innerHTML = data.message || 'Failed'; return; }
            if (!data.length) { el.innerHTML = 'No tickets.'; return; }
            const desc = (s) => (s && s.length > 40) ? s.substring(0, 40) + '…' : (s || '-');
            const names = (arr) => (arr && arr.length) ? arr.map(x => x.name).join(', ') : '-';
            el.innerHTML = `<div class="overflow-x-auto"><table class="w-full text-sm"><thead><tr class="border-b bg-slate-50">
                <th class="text-left py-2 px-2">#</th><th class="text-left py-2 px-2">Title</th><th class="text-left py-2 px-2 max-w-[120px]">Description</th>
                <th class="text-left py-2 px-2">Status</th><th class="text-left py-2 px-2">Priority</th><th class="text-left py-2 px-2">Created by</th><th class="text-left py-2 px-2">Assigned to</th>
                <th class="text-left py-2 px-2">Labels</th><th class="text-left py-2 px-2">Categories</th><th class="text-left py-2 px-2">Updated</th><th class="text-left py-2 px-2">Actions</th>
            </tr></thead><tbody>${data.map(t => {
                const cb = t.created_by || t.createdBy; const au = t.assigned_user || t.assignedUser;
                return `<tr class="border-b border-slate-100 hover:bg-slate-50">
                    <td class="py-2 px-2">${t.id}</td>
                    <td class="py-2 px-2"><a href="${TICKET_SHOW_URL}/${t.id}" class="text-indigo-600 font-medium hover:underline">${(t.title || '-')}</a></td>
                    <td class="py-2 px-2 max-w-[120px] text-slate-600" title="${(t.description || '').replace(/"/g, '&quot;')}">${desc(t.description)}</td>
                    <td class="py-2 px-2">${t.status || '-'}</td>
                    <td class="py-2 px-2">${(t.priority && t.priority.name) || '-'}</td>
                    <td class="py-2 px-2">${cb ? cb.name : (t.created_by_id ? '#' + t.created_by_id : '-')}</td>
                    <td class="py-2 px-2">${au ? au.name : '-'}</td>
                    <td class="py-2 px-2 text-slate-600">${names(t.labels)}</td>
                    <td class="py-2 px-2 text-slate-600">${names(t.categories)}</td>
                    <td class="py-2 px-2 text-slate-500">${t.updated_at ? new Date(t.updated_at).toLocaleDateString() : '-'}</td>
                    <td class="py-2 px-2 whitespace-nowrap">
                        <a href="${TICKET_SHOW_URL}/${t.id}" class="text-indigo-600 mr-2 hover:underline">View</a>
                        <a href="${TICKET_EDIT_URL}/${t.id}/edit" class="text-indigo-600 mr-2 hover:underline">Edit</a>
                        <button type="button" class="ticket-delete text-red-600" data-id="${t.id}">Delete</button>
                    </td>
                </tr>`;
            }).join('')}</tbody></table></div>`;
            el.querySelectorAll('.ticket-delete').forEach(btn => btn.addEventListener('click', e => deleteTicket(Number(e.currentTarget.dataset.id))));
        }

        async function loadUsers() {
            const { ok, data } = await api('/admin/users');
            const el = document.getElementById('users-list');
            if (!ok) { el.innerHTML = data.message || 'Failed'; return; }
            el.innerHTML = data.length ? data.map(u => {
                const role = u.role || 'user';
                return `<div class="flex items-center justify-between py-2 border-b border-slate-100 gap-2">
                    <span class="min-w-0">${u.name} (${u.email})</span>
                    <select class="user-role-select rounded border border-slate-300 px-2 py-1 text-sm w-24 shrink-0" data-id="${u.id}">
                        <option value="user" ${role === 'user' ? 'selected' : ''}>User</option>
                        <option value="agent" ${role === 'agent' ? 'selected' : ''}>Agent</option>
                        <option value="admin" ${role === 'admin' ? 'selected' : ''}>Admin</option>
                    </select>
                    <button type="button" class="user-delete text-red-600 text-sm shrink-0" data-id="${u.id}">Delete</button>
                </div>`;
            }).join('') : 'No users.';
            el.querySelectorAll('.user-role-select').forEach(sel => {
                sel.addEventListener('change', async (e) => {
                    const id = e.currentTarget.dataset.id;
                    const role = e.currentTarget.value;
                    const { ok } = await api('/admin/users/' + id, { method: 'PUT', body: JSON.stringify({ role }) });
                    if (ok) { showToast('Role updated'); loadUsers(); } else showToast('Failed');
                });
            });
            el.querySelectorAll('.user-delete').forEach(btn => {
                
                btn.addEventListener('click', async (e) => {
                    if (confirm('Delete this user?')) {
                        const id = e.currentTarget.dataset.id;
                        const { ok } = await api('/admin/users/' + id, { method: 'DELETE' });
                        if (ok) { showToast('User deleted'); loadUsers(); } else showToast('Failed');
                    }
                });
            });
        }
        document.getElementById('btn-add-user').addEventListener('click', async () => {
            const name = document.getElementById('user-name').value.trim();
            const email = document.getElementById('user-email').value.trim();
            const password = document.getElementById('user-password').value;
            const role = document.getElementById('user-role').value;
            if (!name || !email || !password) { showToast('Name, email, password required'); return; }
            const { ok, data } = await api('/admin/users', { method: 'POST', body: JSON.stringify({ name, email, password, role }) });
            if (ok) { showToast('User added'); document.getElementById('user-name').value = ''; document.getElementById('user-email').value = ''; document.getElementById('user-password').value = ''; loadUsers(); }
            else showToast(data.message || 'Failed');
        });

        async function loadLogs() {
            const { ok, data } = await api('/admin/logs');
            const el = document.getElementById('logs-list');
            if (!ok) { el.innerHTML = data.message || 'Failed'; return; }
            if (!data.length) { el.innerHTML = 'No logs.'; return; }
            el.innerHTML = `<table class="w-full text-sm"><thead><tr class="border-b bg-slate-50">
                <th class="text-left py-2 px-2">ID</th><th class="text-left py-2 px-2">Ticket</th><th class="text-left py-2 px-2">User</th><th class="text-left py-2 px-2">Action</th><th class="text-left py-2 px-2">Description</th><th class="text-left py-2 px-2">At</th>
            </tr></thead><tbody>${data.map(l => `
                <tr class="border-b border-slate-100 hover:bg-slate-50">
                    <td class="py-2 px-2">${l.id}</td>
                    <td class="py-2 px-2">${l.ticket ? ('#' + l.ticket.id + ' ' + (l.ticket.title || '')) : '-'}</td>
                    <td class="py-2 px-2">${l.user ? l.user.name : '-'}</td>
                    <td class="py-2 px-2">${l.action || '-'}</td>
                    <td class="py-2 px-2 max-w-xs truncate">${l.description || '-'}</td>
                    <td class="py-2 px-2 text-slate-500">${l.created_at ? new Date(l.created_at).toLocaleString() : '-'}</td>
                </tr>`
            ).join('')}</tbody></table>`;
        }

        async function loadCategories() {
            const { ok, data } = await api('/categories');
            const el = document.getElementById('categories-list');
            if (!ok) { el.innerHTML = 'Failed'; return; }
            el.innerHTML = data.length ? data.map(c => `<div class="flex items-center justify-between py-1 border-b border-slate-100">
                <span>${c.name} ${c.description ? '— ' + c.description : ''}</span>
                <button type="button" class="category-delete text-red-600 text-sm" data-id="${c.id}">Delete</button>
            </div>`).join('') : 'No categories.';
            el.querySelectorAll('.category-delete').forEach(btn => {
                btn.addEventListener('click', async (e) => {
                    const id = e.currentTarget.dataset.id;
                    const { ok } = await api('/categories/' + id, { method: 'DELETE' });
                    if (ok) { showToast('Category deleted'); loadCategories(); loadTickets(); } else showToast('Failed');
                });
            });
        }
        document.getElementById('btn-add-category').addEventListener('click', async () => {
            const name = document.getElementById('category-name').value.trim();
            const description = document.getElementById('category-desc').value.trim();
            if (!name) { showToast('Name required'); return; }
            const { ok } = await api('/categories', { method: 'POST', body: JSON.stringify({ name, description }) });
            if (ok) { showToast('Category added'); document.getElementById('category-name').value = ''; document.getElementById('category-desc').value = ''; loadCategories(); } else showToast('Failed');
        });

        async function loadLabels() {
            const { ok, data } = await api('/labels');
            const el = document.getElementById('labels-list');
            if (!ok) { el.innerHTML = 'Failed'; return; }
            el.innerHTML = data.length ? data.map(l => `<div class="flex items-center justify-between py-1 border-b border-slate-100">
                <span>${l.name}</span>
                <button type="button" class="label-delete text-red-600 text-sm" data-id="${l.id}">Delete</button>
            </div>`).join('') : 'No labels.';
            el.querySelectorAll('.label-delete').forEach(btn => {
                btn.addEventListener('click', async (e) => {
                    const id = e.currentTarget.dataset.id;
                    const { ok } = await api('/labels/' + id, { method: 'DELETE' });
                    if (ok) { showToast('Label deleted'); loadLabels(); loadTickets(); } else showToast('Failed');
                });
            });
        }
        document.getElementById('btn-add-label').addEventListener('click', async () => {
            const name = document.getElementById('label-name').value.trim();
            if (!name) { showToast('Name required'); return; }
            const { ok } = await api('/labels', { method: 'POST', body: JSON.stringify({ name }) });
            if (ok) { showToast('Label added'); document.getElementById('label-name').value = ''; loadLabels(); } else showToast('Failed');
        });

        document.querySelectorAll('.admin-tab').forEach(tabBtn => {
            tabBtn.addEventListener('click', () => {
                document.querySelectorAll('.admin-tab').forEach(t => { t.classList.remove('bg-slate-200', 'text-slate-700'); t.classList.add('text-slate-600'); });
                tabBtn.classList.add('bg-slate-200', 'text-slate-700');
                tabBtn.classList.remove('text-slate-600');
                document.querySelectorAll('.admin-panel').forEach(p => p.classList.add('hidden'));
                const panel = document.getElementById('panel-' + tabBtn.dataset.tab);
                if (panel) panel.classList.remove('hidden');
                if (tabBtn.dataset.tab === 'tickets') loadTickets();
                if (tabBtn.dataset.tab === 'users') loadUsers();
                if (tabBtn.dataset.tab === 'logs') loadLogs();
                if (tabBtn.dataset.tab === 'categories') loadCategories();
                if (tabBtn.dataset.tab === 'labels') loadLabels();
            });
        });

        async function init() {
            const token = getToken();
            if (!token) { showNeedLogin(); return; }
            const { ok, data } = await api('/user');
            if (!ok || !data.user) { setToken(null); showNeedLogin(); return; }
            if (data.user.role !== 'admin') { showAccessDenied(); return; }
            currentUser = data.user;
            showContent(currentUser.name);
            await loadTickets();
            const editId = OPEN_EDIT_TICKET_ID ?? new URLSearchParams(window.location.search).get('edit');
            if (editId) { editTicket(parseInt(editId, 10)); }
        }
        document.getElementById('btn-logout').addEventListener('click', async () => {
            await api('/logout', { method: 'POST' });
            setToken(null);
            window.location.reload();
        });
        init();
    </script>
</body>
</html>
