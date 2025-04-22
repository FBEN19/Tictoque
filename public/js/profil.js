const profile = document.querySelector('.profile-picture');
const overlay = profile.querySelector('.overlay');

profile.addEventListener('mouseover', () => overlay.style.display = 'flex');
profile.addEventListener('mouseout', () => overlay.style.display = 'none');

document.addEventListener('DOMContentLoaded', function () {
    if (window.location.hash === "#recettes") {
        const tab = new bootstrap.Tab(document.querySelector('#recettes-tab'));
        tab.show();
    }
});