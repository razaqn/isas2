<?php
require_once 'admin/database/config.php';

try {
    // Get all menu items ordered by category
    $stmt = $pdo->query("SELECT * FROM menu ORDER BY category, nama");
    $menuItems = $stmt->fetchAll();
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
    die();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu - Osteria del Mare</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #FC51D7;
            --secondary-color: #86C8BC;
        }
        
        .navbar {
            background-color: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .navbar-brand {
            font-family: 'Palatino', serif;
            font-size: 1.8rem;
            color: var(--primary-color) !important;
        }
        
        .dish-card {
            border: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.3s;
            margin-bottom: 20px;
        }
        
        .dish-card:hover {
            transform: translateY(-5px);
        }
        
        .category-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: var(--primary-color);
            color: white;
            padding: 5px 10px;
            border-radius: 15px;
        }

        footer {
            background-color: #333;
            color: white;
            padding: 30px 0;
            margin-top: 50px;
        }

        .cart-icon {
            position: relative;
            cursor: pointer;
        }
        
        .cart-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: #FC51D7;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 12px;
        }
        
        .cart-item {
            border-bottom: 1px solid #eee;
            padding: 10px 0;
        }
        
        .cart-item:last-child {
            border-bottom: none;
        }
        
        .quantity-control {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .quantity-btn {
            background: #FC51D7;
            color: white;
            border: none;
            width: 25px;
            height: 25px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .quantity-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand" href="#">Osteria del Mare</a>
            <div class="d-flex align-items-center">
                <a href="index.php" class="btn btn-outline-primary me-3">Back to Home</a>
                <div class="cart-icon" data-bs-toggle="modal" data-bs-target="#cartModal">
                    <i class="fas fa-shopping-cart fa-lg"></i>
                    <span class="cart-badge" id="cartBadge">0</span>
                </div>
            </div>
        </div>
    </nav>

    <!-- Menu Section -->
    <div class="container my-5">
        <h1 class="text-center mb-5">Our Menu</h1>
        <div class="row">
            <?php foreach($menuItems as $item): ?>
            <div class="col-md-4 mb-4">
                <div class="card dish-card">
                    <img src="uploads/menu/<?php echo htmlspecialchars($item['image']); ?>" 
                         class="card-img-top" 
                         alt="<?php echo htmlspecialchars($item['nama']); ?>"
                         onerror="this.src='/api/placeholder/400/300'">
                    <div class="category-badge"><?php echo htmlspecialchars($item['category']); ?></div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($item['nama']); ?></h5>
                        <p class="card-text"><?php echo htmlspecialchars($item['description']); ?></p>
                        <p class="card-text">
                            <strong>€<?php echo number_format($item['price'], 2); ?></strong>
                        </p>
                        <button class="btn btn-primary w-100" 
                                onclick="addToCart(<?php echo htmlspecialchars(json_encode($item)); ?>)">
                            Add to Cart
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Cart Modal -->
    <div class="modal fade" id="cartModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Shopping Cart</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="cartItems">
                        <!-- Cart items will be inserted here -->
                    </div>
                    <div class="text-end mt-3">
                        <h5>Total: €<span id="cartTotal">0.00</span></h5>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="placeOrder()">Place Order</button>
                </div>
            </div>
        </div>
    </div>

        <!-- Order Form Modal -->
    <div class="modal fade" id="orderFormModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Complete Your Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="orderForm" onsubmit="submitOrder(event)">
                        <div class="mb-3">
                            <label for="customerName" class="form-label">Name</label>
                            <input type="text" class="form-control" id="customerName" required>
                        </div>
                        <div class="mb-3">
                            <label for="customerEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="customerEmail" required>
                        </div>
                        <div class="mb-3">
                            <label for="customerContact" class="form-label">Contact Number</label>
                            <input type="tel" class="form-control" id="customerContact" required>
                        </div>
                        <div class="mb-3">
                            <label for="orderNotes" class="form-label">Special Notes</label>
                            <textarea class="form-control" id="orderNotes" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Place Order</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Review Modal -->
    <div class="modal fade" id="reviewModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Share Your Experience</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="reviewForm" onsubmit="submitReview(event)">
                        <input type="hidden" id="reviewOrderId">
                        <input type="hidden" id="reviewCustomerName">
                        
                        <div class="mb-3">
                            <label class="form-label">Rating</label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="rating" value="5" required>
                                    <label class="form-check-label">5 stars</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="rating" value="4">
                                    <label class="form-check-label">4 stars</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="rating" value="3">
                                    <label class="form-check-label">3 stars</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="rating" value="2">
                                    <label class="form-check-label">2 stars</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="rating" value="1">
                                    <label class="form-check-label">1 star</label>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="reviewText" class="form-label">Your Review</label>
                            <textarea class="form-control" id="reviewText" rows="3" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit Review</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>Osteria del Mare</h5>
                    <p>Experience authentic Italian seafood cuisine</p>
                </div>
                <div class="col-md-4">
                    <h5>Contact Info</h5>
                    <p>Email: info@osteriadelmare.com</p>
                    <p>Phone: +1 234 567 890</p>
                </div>
                <div class="col-md-4">
                    <h5>Follow Us</h5>
                    <div class="social-links">
                        <a href="#" class="text-white me-3"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-12 text-center">
                    <p class="mb-0">© 2024 Osteria del Mare. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let cart = [];
        
        function addToCart(item) {
            const existingItem = cart.find(cartItem => cartItem.id === item.id);
            
            if (existingItem) {
                existingItem.quantity += 1;
            } else {
                cart.push({
                    ...item,
                    quantity: 1
                });
            }
            
            updateCartDisplay();
        }
        
        function removeFromCart(itemId) {
            cart = cart.filter(item => item.id !== itemId);
            updateCartDisplay();
        }
        
        function updateQuantity(itemId, change) {
            const item = cart.find(item => item.id === itemId);
            if (item) {
                const newQuantity = item.quantity + change;
                if (newQuantity > 0) {
                    item.quantity = newQuantity;
                } else {
                    removeFromCart(itemId);
                }
                updateCartDisplay();
            }
        }
        
        function updateCartDisplay() {
            const cartItems = document.getElementById('cartItems');
            const cartBadge = document.getElementById('cartBadge');
            const cartTotal = document.getElementById('cartTotal');
            
            // Update badge
            const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
            cartBadge.textContent = totalItems;
            
            // Update cart items
            cartItems.innerHTML = cart.map(item => `
                <div class="cart-item">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6>${item.nama}</h6>
                        <button class="btn btn-sm text-danger" onclick="removeFromCart(${item.id})">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <div class="quantity-control">
                            <button class="quantity-btn" onclick="updateQuantity(${item.id}, -1)">-</button>
                            <span>${item.quantity}</span>
                            <button class="quantity-btn" onclick="updateQuantity(${item.id}, 1)">+</button>
                        </div>
                        <div>
                            <strong>€${(item.price * item.quantity).toFixed(2)}</strong>
                        </div>
                    </div>
                </div>
            `).join('');
            
            // Update total
            const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            cartTotal.textContent = total.toFixed(2);
            
            // Show empty cart message if needed
            if (cart.length === 0) {
                cartItems.innerHTML = '<p class="text-center text-muted my-4">Your cart is empty</p>';
            }
        }

        // Tambahkan fungsi ini setelah placeOrder()
        function placeOrder() {
            if (cart.length === 0) {
                alert('Your cart is empty!');
                return;
            }
            
            // Show order form modal
            const orderModal = new bootstrap.Modal(document.getElementById('orderFormModal'));
            orderModal.show();
        }

        async function submitOrder(event) {
            event.preventDefault();
            
            const formData = {
                customerName: document.getElementById('customerName').value,
                customerEmail: document.getElementById('customerEmail').value,
                customerContact: document.getElementById('customerContact').value,
                notes: document.getElementById('orderNotes').value,
                totalAmount: parseFloat(document.getElementById('cartTotal').textContent),
                items: cart.map(item => ({
                    id: item.id,
                    quantity: item.quantity,
                    price: item.price
                }))
            };

            try {
                const response = await fetch('admin/process/process_order.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(formData)
                });

                const result = await response.json();

                if (result.success) {
                    // Close order modal
                    const orderModal = bootstrap.Modal.getInstance(document.getElementById('orderFormModal'));
                    orderModal.hide();

                    // Show review modal
                    showReviewModal(result.orderId, formData.customerName);

                    // Clear cart
                    cart = [];
                    updateCartDisplay();
                } else {
                    alert('Failed to place order: ' + result.message);
                }
            } catch (error) {
                alert('Error placing order: ' + error.message);
            }
        }

        function showReviewModal(orderId, customerName) {
            const reviewModal = new bootstrap.Modal(document.getElementById('reviewModal'));
            
            // Set hidden inputs
            document.getElementById('reviewOrderId').value = orderId;
            document.getElementById('reviewCustomerName').value = customerName;
            
            reviewModal.show();
        }

        async function submitReview(event) {
            event.preventDefault();
            
            const formData = {
                orderId: document.getElementById('reviewOrderId').value,
                customerName: document.getElementById('reviewCustomerName').value,
                rating: document.querySelector('input[name="rating"]:checked').value,
                reviewText: document.getElementById('reviewText').value
            };

            try {
                const response = await fetch('admin/process/process_review.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(formData)
                });

                const result = await response.json();

                if (result.success) {
                    // Close review modal
                    const reviewModal = bootstrap.Modal.getInstance(document.getElementById('reviewModal'));
                    reviewModal.hide();
                    
                    alert('Thank you for your review!');
                } else {
                    alert('Failed to submit review: ' + result.message);
                }
            } catch (error) {
                alert('Error submitting review: ' + error.message);
            }
        }
    </script>
</body>
</html>