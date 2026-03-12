<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Home: redirects to dashboard (or login) via JS using API token
Route::get('/', function () {
    return view('home');
})->name('home');

// Dashboards (API-driven: token in localStorage, data from /api/dashboard and /api/admin/dashboard)
Route::get('/dashboard/user', function () {
    return view('dashboard.user');
})->name('dashboard.user');

// User manage tickets: list, view, create, edit (agents only)
Route::get('/dashboard/user/manage', function () {
    return view('dashboard.user-manage');
})->name('user.manage');

Route::get('/dashboard/admin', function () {
    return view('dashboard.admin');
})->name('dashboard.admin');

// Admin manage: Tickets, Users, Logs, Categories, Labels (API-driven, tabbed UI)
Route::get('/dashboard/admin/manage', function () {
    return view('dashboard.admin-manage');
})->name('admin.manage');

// Ticket detail page: view ticket + comments + add comment form (API-driven)
Route::get('/ticket/{id}', function ($id) {
    return view('ticket.show', ['ticketId' => $id]);
})->name('ticket.show')->where('id', '[0-9]+');

// Admin: edit ticket (opens manage page with edit form for this ticket)
Route::get('/ticket/{id}/edit', function ($id) {
    return view('dashboard.admin-manage', ['openEditTicketId' => (int) $id]);
})->name('ticket.edit')->where('id', '[0-9]+');

// Login form (API-driven: POST /api/login, then redirect to home)
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

// Register form (API-driven: POST /api/register, then redirect to home)
Route::get('/register', function () {
    return view('auth.register');
})->name('register');