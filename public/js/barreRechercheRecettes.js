function chargerRecettes(url) {
    fetch(url, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.text())
    .then(html => {
        document.getElementById('recipe-list').innerHTML = html;
    });
}

document.addEventListener('DOMContentLoaded', function () {
    const barreRecherche = document.getElementById('barre-recherche');
    const urlBase = barreRecherche.dataset.url;

    function mettreAJour(page = 1) {
        const motCle = barreRecherche.value;
        const url = `${urlBase}?q=${encodeURIComponent(motCle)}&page=${page}`;
        chargerRecettes(url);
    }

    barreRecherche.addEventListener('input', () => mettreAJour(1));

    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('pagination-link')) {
            e.preventDefault();
            const page = e.target.dataset.page;
            mettreAJour(page);
        }
    });
});