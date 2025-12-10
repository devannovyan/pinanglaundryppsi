/*!
* Start Bootstrap - SB Admin v7.0.7 (https://startbootstrap.com/template/sb-admin)
* Copyright 2013-2023 Start Bootstrap
* Licensed under MIT (https://github.com/StartBootstrap/startbootstrap-sb-admin/blob/master/LICENSE)
*/

/**
 * File ini berisi skrip untuk fungsionalitas template admin.
 * Fungsi utamanya adalah untuk mengontrol sidebar.
 */

window.addEventListener('DOMContentLoaded', event => {

    // Mengambil elemen tombol untuk toggle sidebar
    const sidebarToggle = document.body.querySelector('#sidebarToggle');

    if (sidebarToggle) {
        // Listener ini akan dijalankan saat tombol sidebar diklik.
        sidebarToggle.addEventListener('click', event => {
            // Mencegah perilaku default dari link/tombol
            event.preventDefault();
            
            // Menambah atau menghapus kelas 'sb-sidenav-toggled' pada body.
            // Kelas ini akan menggeser sidebar masuk atau keluar dari layar,
            // sesuai dengan yang diatur di file styles.css.
            document.body.classList.toggle('sb-sidenav-toggled');
            
            // Menyimpan status sidebar (terbuka/tertutup) di localStorage.
            // Ini membuat pilihan pengguna tetap tersimpan bahkan setelah halaman dimuat ulang.
            localStorage.setItem('sb|sidebar-toggle', document.body.classList.contains('sb-sidenav-toggled'));
        });
    }

});
