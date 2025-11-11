<?php
// Wrapper untuk Departemen Internal, menggunakan template generik department.php
require_once dirname(__DIR__, 2) . '/includes/functions.php';

$slug = 'internal'; // pastikan slug 'internal' ada di tabel departments
$page_title     = 'Departemen Internal';
$additional_css = ['internal.css','departemen.css'];       // pakai style umum departemen
$additional_js  = ['department.js','medkom-init.js']; // JS filter/toggle

// Template generik akan menggunakan variabel $slug di atas
include dirname(__DIR__, 2) . '/pages/department.php';