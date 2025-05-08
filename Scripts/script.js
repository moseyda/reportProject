
const hamburgerMenu = document.getElementById('hamburger-menu');
const sidebar = document.getElementById('sidebar');
const closeBtn = document.getElementById('close-btn');


hamburgerMenu.addEventListener('click', () => {

    if (sidebar.style.width === '250px') {
        sidebar.style.width = '0'; 
    } else {
        sidebar.style.width = '250px';
    }
});


closeBtn.addEventListener('click', () => {
    sidebar.style.width = '0';
});
