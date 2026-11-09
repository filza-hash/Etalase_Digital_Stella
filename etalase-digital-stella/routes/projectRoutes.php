<?php
Router::get('/',                   function () { redirect('/projects'); });
Router::get('/projects',           ['ProjectController', 'index'],      ['auth_required']);
Router::get('/projects/new',       ['ProjectController', 'showCreate'], ['auth_required']);
Router::post('/projects',          ['ProjectController', 'create'],     ['auth_required']);
Router::get('/projects/{id}',      ['ProjectController', 'show'],       ['auth_required']);
Router::post('/projects/{id}/apresiasi', ['ProjectController', 'toggleApresiasi'], ['auth_required']);
Router::get('/projects/{id}/edit', ['ProjectController', 'showEdit'],   ['auth_required']);
Router::put('/projects/{id}',      ['ProjectController', 'update'],     ['auth_required']);
Router::delete('/projects/{id}',   ['ProjectController', 'destroy'],    ['auth_required']);

// Hapus semua karya: hanya Admin & Super Admin (siswa -> 403)
Router::post('/admin/projects/delete-all', ['ProjectController', 'deleteAll'], ['auth_required', role('admin', 'super_admin')]);

// Tandai / cabut karya unggulan: hanya Admin & Super Admin
Router::post('/admin/projects/{id}/unggulan', ['ProjectController', 'toggleUnggulan'], ['auth_required', role('admin', 'super_admin')]);
