document.addEventListener('DOMContentLoaded', function() {
    // Fetch menu data
    fetch('api/menu.php')
        .then(response => response.json())
        .then(data => {
            renderCategories(data.categories);
            renderMenuItems(data.menu_by_category);
        });
});

function renderCategories(categories) {
    const tabsContainer = document.getElementById('categoryTabs');
    let html = '<div class="category-tab active" onclick="filterCategory(\'all\')">All</div>';
    
    categories.forEach(category => {
        html += `<div class="category-tab" onclick="filterCategory('${category.name}')">
                    ${category.name}
                </div>`;
    });
    
    tabsContainer.innerHTML = html;
}

function renderMenuItems(menuByCategory) {
    const container = document.getElementById('menuContainer');
    let html = '';
    
    for (const [category, items] of Object.entries(menuByCategory)) {
        html += `
            <div class="category-section" data-category="${category}">
                <h2 class="category-title">${category}</h2>
                <div class="menu-grid">
                    ${renderItems(items)}
                </div>
            </div>`;
    }
    
    container.innerHTML = html;
}

function addToCart(form, event) {
    event.preventDefault();
    
    fetch('api/cart.php', {
        method: 'POST',
        body: new FormData(form)
    })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                showAlert('Item added to cart!');
            }
        });
} 