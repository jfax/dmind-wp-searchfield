document.addEventListener('DOMContentLoaded', () => {
    const toggleButton = document.getElementsByClassName('toggle-search');
    const searchForm = document.getElementById('search-form-container');
    const searchContainer = searchForm.querySelector('.search-container');
    const searchInput = document.getElementById('searchfield');
    const closeButton = document.querySelector('.close-search'); // Falls vorhanden
    // Suchformular ein- und ausblenden
    for (let i = 0; i < toggleButton.length; i++) {
        toggleButton[i].addEventListener('click', (e) => {
            e.preventDefault();
            searchForm.style.display = 'flex';
            searchForm.classList.add('active');

            // Warten, bis das Formular sichtbar ist, dann Fokus setzen
            setTimeout(() => {
                searchInput.focus();
            }, 50);
        });
    }

    searchForm.addEventListener('focusout', (e) => {
        setTimeout(() => {
            if (!searchContainer.contains(document.activeElement)) {
                searchForm.classList.remove('active');
            }
        }, 100); // Timeout, um den neuen Fokus zu berücksichtigen
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
