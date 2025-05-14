document.querySelectorAll('.delete-pari-btn').forEach(button => {
    button.addEventListener('click', function () {
        if (!confirm('Êtes-vous sûr de vouloir supprimer ce pari ?')) {
            return;
        }

        const form = this.closest('.delete-pari-form');
        const pariId = form.dataset.id;

        fetch(`/paris/delete/${pariId}`, {
            method: 'POST',
            body: new FormData(form)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                form.closest('tr').remove(); // Supprime la ligne du tableau
            } else {
                alert(data.message || 'Erreur lors de la suppression.');
            }
        })
        .catch(error => console.error('Erreur:', error));
    });
});
