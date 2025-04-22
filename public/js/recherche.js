const filterForm = document.getElementById('filterForm');
const minRatingSelect = document.getElementById('minRating');
const sortOrderSelect = document.getElementById('sortOrder');

const submitFormOnChange = () => {
    filterForm.submit();
};

minRatingSelect.addEventListener('change', submitFormOnChange);
sortOrderSelect.addEventListener('change', submitFormOnChange);
document.getElementById('excludeBtn').addEventListener('click', function() {
    const excludeIngredient = document.getElementById('excludeIngredient').value;
    if (excludeIngredient) {
        const form = document.getElementById('filterForm');
        const excludeInput = document.createElement('input');
        excludeInput.type = 'hidden';
        excludeInput.name = 'exclude_ingredient';
        excludeInput.value = excludeIngredient;

        form.appendChild(excludeInput);
        form.submit();
    }
});
document.getElementById('filterBtn').addEventListener('click', function() {
    document.getElementById('filterSidebar').classList.add('show');
});

document.getElementById('closeFilter').addEventListener('click', function() {
    document.getElementById('filterSidebar').classList.remove('show');
});