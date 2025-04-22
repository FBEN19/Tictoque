function ajouterFormulaire(type) {
    const container = document.getElementById(type);
    const prototype = container.dataset.prototype;
    const index = container.querySelectorAll('div.row').length;
    const newForm = document.createElement('div');
    newForm.classList.add('row', 'g-2', 'mb-2');
    newForm.innerHTML = prototype.replace(/__name__/g, index);

    // Ajouter la classe 'form-control' aux balises input, select et textarea
    const elements = newForm.querySelectorAll('input, select, textarea');
    elements.forEach(el => {
        el.classList.add('form-control');
    });

    if (type === 'etapes') {
        const numeroInput = newForm.querySelector('[id*="_numeroEtape"]');
        if (numeroInput) {
            numeroInput.value = index + 1; // commence à 1 puis +1 à chaque ajout
        }
    }

    const boutonSuppression = document.createElement('button');
    boutonSuppression.type = "button";
    boutonSuppression.className = "btn btn-sm btn-danger mt-2 w-25";
    boutonSuppression.innerText = "Supprimer";
    boutonSuppression.onclick = function() { supprimerFormulaire(boutonSuppression); };

    newForm.appendChild(boutonSuppression);
    container.insertBefore(newForm, container.lastElementChild);
}

function supprimerFormulaire(button) {
    button.parentElement.remove();
}