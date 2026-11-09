<?php
Router::get('/login',     ['AuthController', 'showLogin'],    ['guest_only']);
Router::post('/login',    ['AuthController', 'login'],        ['guest_only']);
Router::get('/register',  ['AuthController', 'showRegister'], ['guest_only']);
Router::post('/register', ['AuthController', 'register'],     ['guest_only']);
Router::post('/logout',   ['AuthController', 'logout'],       ['auth_required']);
