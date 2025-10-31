document.addEventListener('DOMContentLoaded', () => {
    const toogle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    if(toogle && sidebar) {
        toogle.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
        });
    }
});