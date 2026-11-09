<?php
// Dasbor statistik: Admin & Super Admin
Router::get('/dashboard', ['DashboardController', 'index'], ['auth_required', role('admin', 'super_admin')]);
