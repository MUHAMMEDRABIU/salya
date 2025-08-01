<script>
    // Example usage in product pages or dashboard
    function handleAddToCart(productId, quantity = 1) {
        // Use the global addToCart function
        addToCart(productId, quantity);
    }

    // For existing add to cart buttons
    document.querySelectorAll('.add-to-cart-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.dataset.productId;
            const quantity = this.dataset.quantity || 1;
            addToCart(productId, quantity);
        });
    });
</script>