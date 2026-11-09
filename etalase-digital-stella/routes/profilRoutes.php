<?php
// Profil siswa. '/profil/edit' harus di atas '/profil/{id}' agar tidak tertukar.
Router::get('/profil/edit', ['ProfilController', 'showEdit'], ['auth_required']);
Router::post('/profil',     ['ProfilController', 'update'],   ['auth_required']);
Router::get('/profil/{id}', ['ProfilController', 'show'],     ['auth_required']);
