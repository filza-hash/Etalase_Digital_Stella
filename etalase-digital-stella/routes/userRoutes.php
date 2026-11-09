<?php
// Semua route user: Super Admin only
$sa = ['auth_required', role('super_admin')];

Router::get('/users',           ['UserController', 'index'],      $sa);
Router::get('/users/new',       ['UserController', 'showCreate'], $sa);
Router::post('/users',          ['UserController', 'create'],     $sa);
Router::get('/users/{id}/edit', ['UserController', 'showEdit'],   $sa);
Router::put('/users/{id}',      ['UserController', 'update'],     $sa);
Router::delete('/users/{id}',   ['UserController', 'destroy'],    $sa);

// Alias sesuai LKPD: /admin/users
Router::get('/admin/users', function () { redirect('/users'); }, $sa);
