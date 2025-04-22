document.addEventListener('DOMContentLoaded', function () {
    const champNote = document.getElementById('note_note');
    const boutons = document.querySelectorAll('input[name="note_etoile"]');
    const formulaire = document.getElementById('form-note');

    if (!champNote || !formulaire) {
        console.error('Champ ou formulaire introuvable');
        return;
    }

    boutons.forEach(bouton => {
        bouton.addEventListener('change', () => {
            champNote.value = bouton.value;

            formulaire.style.display = 'none';

            formulaire.submit();
        });
    });
});