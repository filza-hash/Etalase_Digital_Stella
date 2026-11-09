<?php

$passwords = [
    'Super Admin Stella' => 'Stella@123',
    'Admin Stella' => 'Admin@123',
    'Siswa Contoh' => 'Siswa@123',
];

foreach ($passwords as $user => $password) {
    echo $user . " : " . password_hash($password, PASSWORD_BCRYPT) . "<br>";
}