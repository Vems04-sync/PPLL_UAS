// Jalankan script setelah seluruh elemen HTML selesai dimuat
window.addEventListener('DOMContentLoaded', event => {
    const wrapper = document.getElementById('wrapper');
    const toggleButtonMobile = document.getElementById('sidebarToggle');
    const toggleButtonDesktop = document.getElementById('sidebarToggleDesktop');
    
    // LOGIC TOGGLE SIDEBAR DI MOBILE
    if (toggleButtonMobile) {
        toggleButtonMobile.addEventListener('click', event => {
            event.preventDefault();
            // Tambah / hapus class "toggled" untuk buka/tutup sidebar
            wrapper.classList.toggle('toggled');
        });
    }

    // LOGIC TOGGLE SIDEBAR DI DESKTOP (Ini hanya jalan kalau tombol desktop ada)
    if (toggleButtonDesktop) {
        toggleButtonDesktop.addEventListener('click', event => {
            event.preventDefault();
            // Sama seperti mobile → buka/tutup sidebar
            wrapper.classList.toggle('toggled');
        });
    }

    // CLOSE SIDEBAR SAAT KLIK DI LUAR SIDEBAR (KHUSUS MOBILE)
    wrapper.addEventListener('click', (event) => {
        if (window.innerWidth <= 768 && wrapper.classList.contains('toggled')) {

            // Jika klik dilakukan di luar sidebar dan bukan tombol toggle
            if (
                !document.getElementById('sidebar-wrapper').contains(event.target) &&
                !toggleButtonMobile.contains(event.target)
            ) {
                // Tutup sidebar
                wrapper.classList.remove('toggled');
            }
        }
    });
});
