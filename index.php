<?php
require_once 'admin/database/config.php';

try {
    // Get top 3 dishes by order count
    $stmt = $pdo->query("SELECT * FROM menu ORDER BY jumlah_beli DESC LIMIT 3");
    $topDishes = $stmt->fetchAll();
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
    <title>Osteria del Mare</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #1A5F7A;
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
        
        .nav-link {
            color: #333 !important;
            font-weight: 500;
        }
        
        .hero-section {
            background-color: #f8f9fa;
            padding: 100px 0;
        }
        
        .hero-title {
            font-size: 3rem;
            color: #333;
            font-weight: bold;
        }
        
        .hero-text {
            color: #666;
            font-size: 1.2rem;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
        
        .dish-card {
            border: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        
        .dish-card:hover {
            transform: translateY(-5px);
        }
        
        .testimonial-card {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .service-card {
            text-align: center;
            padding: 30px;
            border-radius: 10px;
            background-color: white;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            height: 100%;
        }
        
        .service-icon {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: 20px;
        }
        
        footer {
            background-color: #333;
            color: white;
            padding: 50px 0;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">Osteria del Mare</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#home">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#menu">Menu</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#testimonials">Testimonials</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#services">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contact</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="hero-title">Experience Authentic Italian Cuisine</h1>
                    <p class="hero-text mt-4">Where Each Plate Tells a Story of Traditional Italian Craftsmanship and Passion</p>
                    <a class="btn btn-primary btn-lg mt-4" href="menu.php">View Menu</a>
                </div>
                <div class="col-lg-6">
                    <img src="/api/placeholder/600/400" alt="Italian dish" class="img-fluid rounded-circle">
                </div>
            </div>
        </div>
    </section>

    <!-- Menu Section -->
    <section id="menu" class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">Our Most Popular Dishes</h2>
            <div class="row g-4">
                <?php foreach($topDishes as $dish): ?>
                <div class="col-md-4">
                    <div class="card dish-card">
                        <!-- <img src="uploads/menu/<?php echo htmlspecialchars($dish['image']); ?>" 
                             class="card-img-top" 
                             alt="<?php echo htmlspecialchars($dish['nama']); ?>"
                             onerror="this.src='/api/placeholder/400/300'"> -->
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($dish['nama']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($dish['description']); ?></p>
                            <p class="card-text">
                                <small class="text-muted">€<?php echo number_format($dish['price'], 2); ?></small>
                                <small class="text-muted float-end">
                                    <i class="fas fa-heart"></i> <?php echo $dish['jumlah_beli']; ?> orders
                                </small>
                            </p>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="text-center mt-5">
                <a href="menu.php" class="btn btn-primary btn-lg">View All Menu</a>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section id="testimonials" class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5">What Our Guests Say</h2>
            <div id="testimonialCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="testimonial-card">
                                    <h5>Maria Romano</h5>
                                    <div class="text-warning mb-2">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                    </div>
                                    <p>"The best Italian seafood I've had outside of Italy. Absolutely authentic!"</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="testimonial-card">
                                    <h5>Marco Bianchi</h5>
                                    <div class="text-warning mb-2">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                    </div>
                                    <p>"Exceptional service and amazing food. The risotto was perfect!"</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="testimonial-card">
                                    <h5>Laura Conti</h5>
                                    <div class="text-warning mb-2">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                    </div>
                                    <p>"The atmosphere and food quality are outstanding. Will definitely return!"</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">Our Services</h2>
            <div class="row g-4">
                <div class="col-md-3">
                    <div class="service-card">
                        <i class="fas fa-utensils service-icon"></i>
                        <h5>Fine Dining</h5>
                        <p>Experience authentic Italian cuisine in an elegant setting</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="service-card">
                        <i class="fas fa-truck service-icon"></i>
                        <h5>Delivery</h5>
                        <p>Enjoy our dishes in the comfort of your home</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="service-card">
                        <i class="fas fa-glass-cheers service-icon"></i>
                        <h5>Events</h5>
                        <p>Let us cater your special occasions</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="service-card">
                        <i class="fas fa-gift service-icon"></i>
                        <h5>Gift Cards</h5>
                        <p>Share the experience with loved ones</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5">Contact Us</h2>
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <?php
                    // Display success message
                    if (isset($_GET['contact_status'])) {
                        if ($_GET['contact_status'] == 'success') {
                            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                                    Thank you for your message. We will contact you soon!
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>';
                        } elseif ($_GET['contact_status'] == 'error') {
                            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    Sorry, there was an error sending your message. Please try again later.
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>';
                        } elseif ($_GET['contact_status'] == 'validation') {
                            $errors = explode(",", $_GET['errors']);
                            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <ul class="mb-0">';
                            foreach ($errors as $error) {
                                echo '<li>' . htmlspecialchars($error) . '</li>';
                            }
                            echo '</ul>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>';
                        }
                    }
                    ?>
                    <form action="admin/process/process_contact.php" method="POST">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="contact" class="form-label">Contact Number</label>
                            <input type="tel" class="form-control" id="contact" name="contact" required>
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">Message</label>
                            <textarea class="form-control" id="message" name="message" rows="4" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Send Message</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

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
    document.addEventListener('DOMContentLoaded', function() {
        // Auto hide alerts after 5 seconds
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            setTimeout(function() {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 5000);
        });
    });
    </script>
</body>
</html>