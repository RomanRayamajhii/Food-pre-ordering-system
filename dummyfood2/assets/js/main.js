let cart = [];

function addToCart(itemId) {
    if(!isLoggedIn()) {
        window.location.href = 'login.php';
        return;
    }
    
    fetch('api/add_to_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            item_id: itemId
        })
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            updateCartCount();
            showNotification('Item added to cart!');
        }
    })
    .catch(error => console.error('Error:', error));
}

function updateCartCount() {
    fetch('api/get_cart_count.php')
    .then(response => response.json())
    .then(data => {
        document.getElementById('cart-count').textContent = data.count;
    });
}

function showNotification(message) {
    const notification = document.createElement('div');
    notification.className = 'notification';
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

function isLoggedIn() {
    return document.cookie.includes('PHPSESSID');
}

// Initialize cart count on page load
document.addEventListener('DOMContentLoaded', () => {
    updateCartCount();
}); 