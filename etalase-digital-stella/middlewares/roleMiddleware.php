<?php
// Cek hak akses. Contoh: role('super_admin') atau role('admin', 'super_admin')
function role(string ...$roles): callable
{
    return function () use ($roles) {
        $u = auth_user();
        if (!$u || !in_array($u['role'], $roles, true)) {
            abort(403);
        }
    };
}
