document.addEventListener('DOMContentLoaded', () => {
    const toggleButton = document.querySelector('.toggle-search');
    const searchForm = document.getElementById('search-form-container');
    const searchInput = document.getElementById('searchfield');
    const closeButton = document.querySelector('.close-search'); // Falls vorhanden

    // Suchformular ein- und ausblenden
    toggleButton.addEventListener('click', (e) => {
        e.preventDefault();
        searchForm.style.display = 'flex';
        searchForm.classList.add('active');

        // Warten, bis das Formular sichtbar ist, dann Fokus setzen
        setTimeout(() => {
            searchInput.focus();
        }, 50);
    });

    // Prüfe, ob das Formular den Fokus verliert und blende es aus
    searchForm.addEventListener('focusout', (e) => {
        setTimeout(() => {
            searchForm.classList.remove('active');
        }, 100); // Timeout stellt sicher, dass der neue Fokus berücksichtigt wird
    });

    // Suchformular schließen mit ESC oder Click auf Close-Button
    document.addEventListener('keydown', (e) => {
        if (e.key === "Escape") {
            searchForm.classList.remove('active');
        }
    });

    if (closeButton) {
        closeButton.addEventListener('click', () => {
            searchForm.classList.remove('active');
        });
    }

    // Formular schließen, wenn der Fokus verloren geht
    searchForm.addEventListener('click', (e) => {
        if (!e.target.closest('.search-container')) {
            searchForm.classList.remove('active');
        }
    });
});
