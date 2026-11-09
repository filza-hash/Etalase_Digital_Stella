<?php
// Cek sudah login (session)
function auth_required(): void
{
    if (!auth_user()) {
        flash('error', 'Silakan login terlebih dahulu.');
        redirect('/login');
    }
}

// Halaman login/register hanya untuk yang belum login
function guest_only(): void
{
    if (auth_user()) redirect('/projects');
}
