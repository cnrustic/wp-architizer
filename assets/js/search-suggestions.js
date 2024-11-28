document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('.search-input');
    const suggestionsBox = document.querySelector('.search-suggestions');
    let searchTimer;

    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimer);
        const query = this.value;
        
        if (query.length < 2) {
            suggestionsBox.innerHTML = '';
            return;
        }

        searchTimer = setTimeout(() => {
            fetch(`${architizer.ajaxurl}?action=get_search_suggestions&query=${query}`)
                .then(response => response.json())
                .then(data => {
                    renderSuggestions(data);
                });
        }, 300);
    });
}); 