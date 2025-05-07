<?php
session_start(); // Only call this once

$isLoggedIn = isset($_SESSION['user_id']); // More accurate for logged-in check

// Check if user is logged in and is a customer
if (!$isLoggedIn || $_SESSION['user_role'] !== 'customer') {
    $_SESSION['error'] = "You don't have permission to access this page";
    header("Location: ../login.php");
    exit;
}

require_once '../config/database.php';

// Get user information
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

$name = $user['name']; // Define $name

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Savoria - Fine Dining Restaurant</title>
    <link rel="stylesheet" href="../css/restaurant.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Preloader -->
    <div class="preloader">
        <div class="loader">
            <svg width="80" height="80" viewBox="0 0 100 100">
                <circle class="loader-circle" cx="50" cy="50" r="45"></circle>
                <path class="loader-spoon" d="M30,40 Q50,10 70,40 L60,80 Q50,90 40,80 Z"></path>
            </svg>
            <p>Savoria</p>
        </div>
    </div>

    <!-- Header -->
    <header id="header">
        <div class="container">
            <div class="logo">
                <a href="index.php">
                    <span class="logo-icon"><i class="fas fa-utensils"></i></span>
                    <span class="logo-text">Savoria</span>
                </a>
            </div>

            <nav class="navbar">
                <ul class="nav-links">
                    <li><a href="#home" class="active">Home</a></li>
                    <li><a href="#about">About</a></li>
                    <li><a href="#menu">Menu</a></li>
                    <li><a href="#events">Events</a></li>
                    <li><a href="#chefs">Chefs</a></li>
                    <li><a href="#gallery">Gallery</a></li>
                    <li><a href="#contact">Contact</a></li>
                    <?php if ($isLoggedIn): ?>
                    <li class="dropdown">
                        <a href="#profile"><i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($name); ?></a>
                        <div class="dropdown-content">
                            <a href="#profile"><i class="fas fa-id-card"></i> My Profile</a>
                            <a href="#reservations"><i class="fas fa-calendar-check"></i> My Reservations</a>
                            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                        </div>
                    </li>
                    <?php else: ?>
                    <li class="auth-buttons">
                        <a href="login.php" class="btn-login">Login</a>
                        <a href="register.php" class="btn-register">Register</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>

            <div class="mobile-nav-toggle">
                <div class="bar"></div>
                <div class="bar"></div>
                <div class="bar"></div>
            </div>
        </div>
    </header>

    <!-- Mobile Menu Overlay -->
    <div class="mobile-menu">
        <ul>
            <li><a href="#home" class="active">Home</a></li>
            <li><a href="#about">About</a></li>
            <li><a href="#menu">Menu</a></li>
            <li><a href="#events">Events</a></li>
            <li><a href="#chefs">Chefs</a></li>
            <li><a href="#gallery">Gallery</a></li>
            <li><a href="#contact">Contact</a></li>
            <?php if ($isLoggedIn): ?>
                <li><a href="#profile"><i class="fas fa-user-circle"></i> My Profile</a></li>
                <li><a href="#reservations"><i class="fas fa-calendar-check"></i> My Reservations</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            <?php else: ?>
                <li><a href="login.php" class="mobile-btn login">Login</a></li>
                <li><a href="register.php" class="mobile-btn register">Register</a></li>
            <?php endif; ?>
        </ul>
    </div>

    <!-- Hero Section -->
    <section id="home" class="hero">
        <div class="hero-overlay"></div>
        <div class="container">
            <div class="hero-content">
                <h1 class="reveal-text">Welcome to <span>Savoria</span></h1>
                <p class="reveal-text delay-1">Where Culinary Excellence Meets Elegant Atmosphere</p>
                <div class="hero-buttons reveal-text delay-2">
                    <a href="#menu" class="btn-primary">View Our Menu</a>
                    <a href="#reservation" class="btn-secondary">Reserve a Table</a>
                </div>
            </div>
        </div>
        <div class="scroll-down">
            <a href="#about">
                <span class="mouse">
                    <span class="wheel"></span>
                </span>
                <span class="scroll-text">Scroll Down</span>
            </a>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="about section-padding">
        <div class="container">
            <div class="section-header">
                <span class="sub-heading">Our Story</span>
                <h2>About Savoria</h2>
                <div class="separator">
                    <span class="line"></span>
                    <span class="icon"><i class="fas fa-utensils"></i></span>
                    <span class="line"></span>
                </div>
            </div>

            <div class="about-content">
                <div class="about-image reveal-left">
                    <div class="image-frame">
                        <img src="img/placeholder.php?width=600&height=600&text=Restaurant Interior&bg=222" alt="Restaurant Interior">
                    </div>
                    <div class="experience">
                        <span class="number">15</span>
                        <span class="text">Years of Excellence</span>
                    </div>
                </div>
                <div class="about-text reveal-right">
                    <h3>A Culinary Journey Since 2008</h3>
                    <p>Savoria was born from a passion for exceptional cuisine and unforgettable dining experiences. Our journey began 15 years ago with a simple vision: to create a restaurant where traditional recipes meet modern culinary techniques.</p>
                    <p>Today, Savoria stands as a testament to that vision, offering a unique blend of flavors, a warm atmosphere, and impeccable service that has earned us recognition among food enthusiasts and critics alike.</p>
                    
                    <div class="features">
                        <div class="feature">
                            <div class="icon">
                                <i class="fas fa-award"></i>
                            </div>
                            <div class="text">
                                <h4>Award Winning</h4>
                                <p>Recognized for culinary excellence with multiple regional and national awards.</p>
                            </div>
                        </div>
                        <div class="feature">
                            <div class="icon">
                                <i class="fas fa-leaf"></i>
                            </div>
                            <div class="text">
                                <h4>Fresh Ingredients</h4>
                                <p>We source only the freshest seasonal ingredients from local farmers and producers.</p>
                            </div>
                        </div>
                    </div>
                    
                    <a href="#about-full" class="btn-text">Learn More About Our Story <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </section>

    <!-- Menu Section -->
    <section id="menu" class="menu section-padding bg-light">
        <div class="container">
            <div class="section-header">
                <span class="sub-heading">Delicious Offerings</span>
                <h2>Our Menu</h2>
                <div class="separator">
                    <span class="line"></span>
                    <span class="icon"><i class="fas fa-utensils"></i></span>
                    <span class="line"></span>
                </div>
            </div>

            <div class="menu-tabs">
                <button class="menu-tab active" data-category="all">All</button>
                <button class="menu-tab" data-category="starters">Starters</button>
                <button class="menu-tab" data-category="mains">Main Courses</button>
                <button class="menu-tab" data-category="desserts">Desserts</button>
                <button class="menu-tab" data-category="drinks">Drinks</button>
            </div>

            <div class="menu-items">
                <!-- Starters -->
                <div class="menu-item reveal-up" data-category="starters">
                    <div class="menu-image">
                        <img src="img/placeholder.php?width=400&height=300&text=Caprese Salad&bg=222" alt="Caprese Salad">
                    </div>
                    <div class="menu-content">
                        <div class="menu-title">
                            <h3>Caprese Salad</h3>
                            <span class="price">$12</span>
                        </div>
                        <div class="menu-ingredients">Fresh tomatoes, buffalo mozzarella, basil, olive oil, balsamic glaze</div>
                        <span class="badge">Popular</span>
                    </div>
                </div>

                <div class="menu-item reveal-up" data-category="starters">
                    <div class="menu-image">
                        <img src="img/placeholder.php?width=400&height=300&text=Truffle Arancini&bg=222" alt="Truffle Arancini">
                    </div>
                    <div class="menu-content">
                        <div class="menu-title">
                            <h3>Truffle Arancini</h3>
                            <span class="price">$14</span>
                        </div>
                        <div class="menu-ingredients">Risotto balls, black truffle, parmesan, herbs, truffle aioli</div>
                    </div>
                </div>

                <!-- Main Courses -->
                <div class="menu-item reveal-up" data-category="mains">
                    <div class="menu-image">
                        <img src="img/placeholder.php?width=400&height=300&text=Filet Mignon&bg=222" alt="Filet Mignon">
                    </div>
                    <div class="menu-content">
                        <div class="menu-title">
                            <h3>Filet Mignon</h3>
                            <span class="price">$38</span>
                        </div>
                        <div class="menu-ingredients">8oz center-cut beef tenderloin, red wine reduction, roasted vegetables, truffle mashed potatoes</div>
                        <span class="badge">Chef's Choice</span>
                    </div>
                </div>

                <div class="menu-item reveal-up" data-category="mains">
                    <div class="menu-image">
                        <img src="img/placeholder.php?width=400&height=300&text=Lobster Ravioli&bg=222" alt="Lobster Ravioli">
                    </div>
                    <div class="menu-content">
                        <div class="menu-title">
                            <h3>Lobster Ravioli</h3>
                            <span class="price">$32</span>
                        </div>
                        <div class="menu-ingredients">House-made pasta, Maine lobster, ricotta, saffron cream sauce, fresh herbs</div>
                    </div>
                </div>

                <!-- Desserts -->
                <div class="menu-item reveal-up" data-category="desserts">
                    <div class="menu-image">
                        <img src="img/placeholder.php?width=400&height=300&text=Tiramisu&bg=222" alt="Tiramisu">
                    </div>
                    <div class="menu-content">
                        <div class="menu-title">
                            <h3>Classic Tiramisu</h3>
                            <span class="price">$10</span>
                        </div>
                        <div class="menu-ingredients">Espresso-soaked ladyfingers, mascarpone cream, cocoa powder, coffee liqueur</div>
                        <span class="badge">Popular</span>
                    </div>
                </div>

                <div class="menu-item reveal-up" data-category="desserts">
                    <div class="menu-image">
                        <img src="img/placeholder.php?width=400&height=300&text=Crème Brûlée&bg=222" alt="Crème Brûlée">
                    </div>
                    <div class="menu-content">
                        <div class="menu-title">
                            <h3>Vanilla Crème Brûlée</h3>
                            <span class="price">$12</span>
                        </div>
                        <div class="menu-ingredients">Madagascar vanilla custard, caramelized sugar crust, seasonal berries</div>
                    </div>
                </div>

                <!-- Drinks -->
                <div class="menu-item reveal-up" data-category="drinks">
                    <div class="menu-image">
                        <img src="img/placeholder.php?width=400&height=300&text=Signature Cocktail&bg=222" alt="Signature Cocktail">
                    </div>
                    <div class="menu-content">
                        <div class="menu-title">
                            <h3>Savoria Sunset</h3>
                            <span class="price">$15</span>
                        </div>
                        <div class="menu-ingredients">Aged rum, passion fruit, lime, vanilla syrup, angostura bitters</div>
                        <span class="badge">Signature</span>
                    </div>
                </div>

                <div class="menu-item reveal-up" data-category="drinks">
                    <div class="menu-image">
                        <img src="img/placeholder.php?width=400&height=300&text=Wine Selection&bg=222" alt="Wine Selection">
                    </div>
                    <div class="menu-content">
                        <div class="menu-title">
                            <h3>Premium Wine Selection</h3>
                            <span class="price">$12-$200</span>
                        </div>
                        <div class="menu-ingredients">Curated selection of domestic and imported wines by the glass or bottle</div>
                    </div>
                </div>
            </div>

            <div class="text-center">
                <a href="#full-menu" class="btn-primary">View Full Menu</a>
            </div>
        </div>
    </section>

    <!-- Events Section -->
    <section id="events" class="events section-padding">
        <div class="container">
            <div class="section-header">
                <span class="sub-heading">Special Occasions</span>
                <h2>Our Events & Private Dining</h2>
                <div class="separator">
                    <span class="line"></span>
                    <span class="icon"><i class="fas fa-glass-cheers"></i></span>
                    <span class="line"></span>
                </div>
            </div>

            <div class="events-slider">
                <div class="events-slide active">
                    <div class="event-image reveal-left">
                        <img src="img/placeholder.php?width=600&height=400&text=Wedding Reception&bg=222" alt="Wedding Reception">
                    </div>
                    <div class="event-content reveal-right">
                        <h3>Wedding Receptions</h3>
                        <p>Make your special day unforgettable with our elegant wedding reception packages. Our private dining room can accommodate up to 80 guests, and our culinary team will create a custom menu tailored to your preferences.</p>
                        <ul class="event-features">
                            <li><i class="fas fa-check-circle"></i> Customized menu options</li>
                            <li><i class="fas fa-check-circle"></i> Elegant table settings</li>
                            <li><i class="fas fa-check-circle"></i> Dedicated event coordinator</li>
                            <li><i class="fas fa-check-circle"></i> Audiovisual equipment available</li>
                        </ul>
                        <a href="#wedding-info" class="btn-secondary">Learn More</a>
                    </div>
                </div>

                <div class="events-slide">
                    <div class="event-image reveal-left">
                        <img src="img/placeholder.php?width=600&height=400&text=Corporate Events&bg=222" alt="Corporate Events">
                    </div>
                    <div class="event-content reveal-right">
                        <h3>Corporate Events</h3>
                        <p>Impress your clients and colleagues with an exceptional corporate dining experience. From business lunches to formal dinners, our team will ensure your event is professional and memorable.</p>
                        <ul class="event-features">
                            <li><i class="fas fa-check-circle"></i> Business-focused packages</li>
                            <li><i class="fas fa-check-circle"></i> Private meeting spaces</li>
                            <li><i class="fas fa-check-circle"></i> High-speed Wi-Fi</li>
                            <li><i class="fas fa-check-circle"></i> Customized catering options</li>
                        </ul>
                        <a href="#corporate-info" class="btn-secondary">Learn More</a>
                    </div>
                </div>

                <div class="events-slide">
                    <div class="event-image reveal-left">
                        <img src="img/placeholder.php?width=600&height=400&text=Special Celebrations&bg=222" alt="Special Celebrations">
                    </div>
                    <div class="event-content reveal-right">
                        <h3>Special Celebrations</h3>
                        <p>Whether it's a birthday, anniversary, or graduation, celebrate life's special moments in our elegant private dining rooms. Our dedicated staff will help you create a personalized celebration.</p>
                        <ul class="event-features">
                            <li><i class="fas fa-check-circle"></i> Custom celebration cakes</li>
                            <li><i class="fas fa-check-circle"></i> Themed decoration options</li>
                            <li><i class="fas fa-check-circle"></i> Special menu packages</li>
                            <li><i class="fas fa-check-circle"></i> Professional photography services</li>
                        </ul>
                        <a href="#celebrations-info" class="btn-secondary">Learn More</a>
                    </div>
                </div>
            </div>

            <div class="slider-controls">
                <button class="slider-control active" data-slide="0"></button>
                <button class="slider-control" data-slide="1"></button>
                <button class="slider-control" data-slide="2"></button>
            </div>
        </div>
    </section>

    <!-- Chefs Section -->
    <section id="chefs" class="chefs section-padding bg-light">
        <div class="container">
            <div class="section-header">
                <span class="sub-heading">Culinary Experts</span>
                <h2>Meet Our Chefs</h2>
                <div class="separator">
                    <span class="line"></span>
                    <span class="icon"><i class="fas fa-hat-chef"></i></span>
                    <span class="line"></span>
                </div>
            </div>

            <div class="chefs-container">
                <div class="chef-card reveal-up">
                    <div class="chef-image">
                        <img src="img/placeholder.php?width=400&height=500&text=Executive Chef&bg=222" alt="Executive Chef">
                        <div class="chef-social">
                            <a href="#"><i class="fab fa-instagram"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                    <div class="chef-info">
                        <h3>Alessandro Romano</h3>
                        <span class="chef-title">Executive Chef</span>
                        <p>With 20 years of experience in Michelin-starred restaurants across Europe, Chef Alessandro brings his passion for Italian cuisine with a modern twist to Savoria.</p>
                    </div>
                </div>

                <div class="chef-card reveal-up delay-1">
                    <div class="chef-image">
                        <img src="img/placeholder.php?width=400&height=500&text=Pastry Chef&bg=222" alt="Pastry Chef">
                        <div class="chef-social">
                            <a href="#"><i class="fab fa-instagram"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                    <div class="chef-info">
                        <h3>Sophia Martinez</h3>
                        <span class="chef-title">Head Pastry Chef</span>
                        <p>Trained in Paris, Chef Sophia creates exquisite desserts that are as beautiful as they are delicious. Her signature chocolate soufflé is not to be missed.</p>
                    </div>
                </div>

                <div class="chef-card reveal-up delay-2">
                    <div class="chef-image">
                        <img src="img/placeholder.php?width=400&height=500&text=Sous Chef&bg=222" alt="Sous Chef">
                        <div class="chef-social">
                            <a href="#"><i class="fab fa-instagram"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                    <div class="chef-info">
                        <h3>James Wilson</h3>
                        <span class="chef-title">Sous Chef</span>
                        <p>Chef James specializes in sourcing the finest local ingredients and transforming them into innovative dishes that highlight the flavors of each season.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Gallery Section -->
    <section id="gallery" class="gallery section-padding">
        <div class="container">
            <div class="section-header">
                <span class="sub-heading">Visual Journey</span>
                <h2>Our Gallery</h2>
                <div class="separator">
                    <span class="line"></span>
                    <span class="icon"><i class="fas fa-camera"></i></span>
                    <span class="line"></span>
                </div>
            </div>

            <div class="gallery-filters">
                <button class="gallery-filter active" data-filter="all">All</button>
                <button class="gallery-filter" data-filter="food">Food</button>
                <button class="gallery-filter" data-filter="interior">Interior</button>
                <button class="gallery-filter" data-filter="events">Events</button>
            </div>

            <div class="gallery-container">
                <div class="gallery-item reveal-up" data-category="food">
                    <img src="img/placeholder.php?width=600&height=400&text=Signature Dish&bg=222" alt="Signature Dish">
                    <div class="gallery-overlay">
                        <div class="gallery-info">
                            <h3>Herb-Crusted Lamb</h3>
                            <span>Signature Dish</span>
                        </div>
                        <a href="img/placeholder.php?width=1200&height=800&text=Signature Dish&bg=222" class="gallery-expand">
                            <i class="fas fa-search-plus"></i>
                        </a>
                    </div>
                </div>

                <div class="gallery-item reveal-up" data-category="interior">
                    <img src="img/placeholder.php?width=600&height=400&text=Main Dining Area&bg=222" alt="Main Dining Area">
                    <div class="gallery-overlay">
                        <div class="gallery-info">
                            <h3>Main Dining Area</h3>
                            <span>Interior</span>
                        </div>
                        <a href="img/placeholder.php?width=1200&height=800&text=Main Dining Area&bg=222" class="gallery-expand">
                            <i class="fas fa-search-plus"></i>
                        </a>
                    </div>
                </div>

                <div class="gallery-item reveal-up" data-category="events">
                    <img src="img/placeholder.php?width=600&height=400&text=Private Event&bg=222" alt="Private Event">
                    <div class="gallery-overlay">
                        <div class="gallery-info">
                            <h3>Anniversary Celebration</h3>
                            <span>Special Event</span>
                        </div>
                        <a href="img/placeholder.php?width=1200&height=800&text=Private Event&bg=222" class="gallery-expand">
                            <i class="fas fa-search-plus"></i>
                        </a>
                    </div>
                </div>

                <div class="gallery-item reveal-up" data-category="food">
                    <img src="img/placeholder.php?width=600&height=400&text=Dessert Platter&bg=222" alt="Dessert Platter">
                    <div class="gallery-overlay">
                        <div class="gallery-info">
                            <h3>Dessert Assortment</h3>
                            <span>Sweet Creations</span>
                        </div>
                        <a href="img/placeholder.php?width=1200&height=800&text=Dessert Platter&bg=222" class="gallery-expand">
                            <i class="fas fa-search-plus"></i>
                        </a>
                    </div>
                </div>

                <div class="gallery-item reveal-up" data-category="interior">
                    <img src="img/placeholder.php?width=600&height=400&text=Bar Area&bg=222" alt="Bar Area">
                    <div class="gallery-overlay">
                        <div class="gallery-info">
                            <h3>Cocktail Bar</h3>
                            <span>Interior</span>
                        </div>
                        <a href="img/placeholder.php?width=1200&height=800&text=Bar Area&bg=222" class="gallery-expand">
                            <i class="fas fa-search-plus"></i>
                        </a>
                    </div>
                </div>

                <div class="gallery-item reveal-up" data-category="events">
                    <img src="img/placeholder.php?width=600&height=400&text=Wine Tasting&bg=222" alt="Wine Tasting">
                    <div class="gallery-overlay">
                        <div class="gallery-info">
                            <h3>Wine Tasting Event</h3>
                            <span>Special Event</span>
                        </div>
                        <a href="img/placeholder.php?width=1200&height=800&text=Wine Tasting&bg=222" class="gallery-expand">
                            <i class="fas fa-search-plus"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Reservation Section -->
<section id="reservation" class="reservation section-padding bg-light">
    <div class="container">
        <div class="section-header">
            <span class="sub-heading">Book a Table</span>
            <h2>Make a Reservation</h2>
            <div class="separator">
                <span class="line"></span>
                <span class="icon"><i class="fas fa-calendar-check"></i></span>
                <span class="line"></span>
            </div>
        </div>

        <div class="reservation-container">
            <div class="reservation-image reveal-left">
                <img src="img/placeholder.php?width=600&height=600&text=Reservation&bg=222" alt="Reservation">
                <div class="reservation-info">
                    <div class="info-item">
                        <i class="fas fa-phone"></i>
                        <div>
                            <h4>Phone</h4>
                            <p>(123) 456-7890</p>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-envelope"></i>
                        <div>
                            <h4>Email</h4>
                            <p>reservations@savoria.com</p>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-clock"></i>
                        <div>
                            <h4>Hours</h4>
                            <p>Mon-Sun: 5:00 PM - 11:00 PM</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="reservation-form reveal-right">
                <form action="#" method="POST" id="reservationForm">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" id="name" name="name" placeholder="Your Name" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" placeholder="Your Email" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Contact Number</label>
                        <input type="tel" id="phone" name="phone" placeholder="Your Phone Number" required>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="reservation-date">Date</label>
                            <input type="date" id="reservation-date" name="date" required>
                        </div>
                        <div class="form-group time-group">
                            <label for="reservation-time">Time</label>
                            <div class="time-input-wrapper">
                                <input type="text" id="reservation-time" name="time" placeholder="HH:MM" pattern="(0?[1-9]|1[0-2]):[0-5][0-9]" required>
                                <select id="time-period" name="time-period">
                                    <option value="AM">AM</option>
                                    <option value="PM" selected>PM</option>
                                </select>
                            </div>
                            <small class="time-hint">Restaurant hours: 5:00 PM - 11:00 PM</small>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="guests">How many persons</label>
                        <select id="guests" name="guests" required>
                            <option value="" disabled selected>Select Number of Guests</option>
                            <option value="1">1 Person</option>
                            <option value="2">2 People</option>
                            <option value="3">3 People</option>
                            <option value="4">4 People</option>
                            <option value="5">5 People</option>
                            <option value="6">6 People</option>
                            <option value="7">7 People</option>
                            <option value="8">8 People</option>
                            <option value="9+">9+ People</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Theme and occasion <span class="theme-status">(No theme selected)</span></label>
                        <div class="theme-carousel-container">
                            <div class="theme-carousel">
                                <div class="theme-slide" data-theme="birthday">
                                    <div class="theme-slide-content">
                                        <img src="img/placeholder.php?width=400&height=250&text=Birthday&bg=222" alt="Birthday">
                                        <div class="theme-info">
                                            <h4>Birthday</h4>
                                            <p>Celebrate your special day with us</p>
                                        </div>
                                        <div class="theme-select-overlay">
                                            <span class="select-indicator"><i class="fas fa-check-circle"></i></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="theme-slide" data-theme="anniversary">
                                    <div class="theme-slide-content">
                                        <img src="img/placeholder.php?width=400&height=250&text=Anniversary&bg=222" alt="Anniversary">
                                        <div class="theme-info">
                                            <h4>Anniversary</h4>
                                            <p>Romantic setting for your celebration</p>
                                        </div>
                                        <div class="theme-select-overlay">
                                            <span class="select-indicator"><i class="fas fa-check-circle"></i></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="theme-slide" data-theme="corporate">
                                    <div class="theme-slide-content">
                                        <img src="img/placeholder.php?width=400&height=250&text=Corporate&bg=222" alt="Corporate">
                                        <div class="theme-info">
                                            <h4>Corporate</h4>
                                            <p>Professional setting for business events</p>
                                        </div>
                                        <div class="theme-select-overlay">
                                            <span class="select-indicator"><i class="fas fa-check-circle"></i></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="theme-slide" data-theme="casual">
                                    <div class="theme-slide-content">
                                        <img src="img/placeholder.php?width=400&height=250&text=Casual&bg=222" alt="Casual">
                                        <div class="theme-info">
                                            <h4>Casual</h4>
                                            <p>Relaxed atmosphere for friendly gatherings</p>
                                        </div>
                                        <div class="theme-select-overlay">
                                            <span class="select-indicator"><i class="fas fa-check-circle"></i></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="theme-slide" data-theme="wedding">
                                    <div class="theme-slide-content">
                                        <img src="img/placeholder.php?width=400&height=250&text=Wedding&bg=222" alt="Wedding">
                                        <div class="theme-info">
                                            <h4>Wedding</h4>
                                            <p>Elegant setup for wedding receptions</p>
                                        </div>
                                        <div class="theme-select-overlay">
                                            <span class="select-indicator"><i class="fas fa-check-circle"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="theme-carousel-nav">
                                <button type="button" class="theme-prev"><i class="fas fa-chevron-left"></i></button>
                                <button type="button" class="theme-next"><i class="fas fa-chevron-right"></i></button>
                            </div>
                            
                            <div class="theme-carousel-dots"></div>
                            
                            <!-- Hidden input to store the selected theme value -->
                            <input type="hidden" id="selected-theme" name="theme" value="">
                        </div>
                        <div class="theme-actions">
                            <button type="button" class="btn-text theme-clear" disabled>
                                <i class="fas fa-times-circle"></i> Clear Selection
                            </button>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Special Request</label>
                        <textarea id="message" name="message" placeholder="Any special requests or dietary requirements?"></textarea>
                    </div>
                    
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-calendar-check"></i> Reserve Now
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>
    <!-- Contact Section -->
    <section id="contact" class="contact section-padding">
        <div class="container">
            <div class="section-header">
                <span class="sub-heading">Get in Touch</span>
                <h2>Contact Us</h2>
                <div class="separator">
                    <span class="line"></span>
                    <span class="icon"><i class="fas fa-envelope"></i></span>
                    <span class="line"></span>
                </div>
            </div>

            <div class="contact-container">
                <div class="contact-info reveal-left">
                    <h3>Our Information</h3>
                    <p>We'd love to hear from you! Whether you have a question about our menu, events, or anything else, our team is ready to answer all your questions.</p>
                    
                    <div class="info-items">
                        <div class="info-item">
                            <div class="icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="text">
                                <h4>Location</h4>
                                <p>123 Gourmet Avenue, Culinary District<br>New York, NY 10001</p>
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <div class="icon">
                                <i class="fas fa-phone-alt"></i>
                            </div>
                            <div class="text">
                                <h4>Call Us</h4>
                                <p>General: (123) 456-7890<br>Reservations: (123) 456-7891</p>
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <div class="icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="text">
                                <h4>Email Us</h4>
                                <p>info@savoria.com<br>reservations@savoria.com</p>
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <div class="icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="text">
                                <h4>Opening Hours</h4>
                                <p>Monday-Friday: 5:00 PM - 11:00 PM<br>Saturday-Sunday: 12:00 PM - 11:00 PM</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="social-links">
                        <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-tripadvisor"></i></a>
                    </div>
                </div>
                
                <div class="contact-form reveal-right">
                    <h3>Send a Message</h3>
                    <form action="#" method="POST" id="contactForm">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="contact-name">Name</label>
                                <input type="text" id="contact-name" name="name" placeholder="Your Name" required>
                            </div>
                            <div class="form-group">
                                <label for="contact-email">Email</label>
                                <input type="email" id="contact-email" name="email" placeholder="Your Email" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="contact-subject">Subject</label>
                            <input type="text" id="contact-subject" name="subject" placeholder="Subject" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="contact-message">Message</label>
                            <textarea id="contact-message" name="message" placeholder="Your Message" required></textarea>
                        </div>
                        
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-paper-plane"></i> Send Message
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Map Section -->
    <div id="map" class="map-container">
        <div class="map-overlay" onclick="this.style.display='none';">
            <div class="map-overlay-text">Click to Activate Map</div>
        </div>
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3022.215366960225!2d-73.98632932472508!3d40.75895937138728!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNDDCsDE3JzA5LjQiTiA3M8KwNDAnNTAuNyJX!5e0!3m2!1sen!2sus!4v1650000000000!5m2!1sen!2sus" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
    </div>

    <!-- Testimonials Section -->
    <section class="testimonials section-padding bg-light">
        <div class="container">
            <div class="section-header">
                <span class="sub-heading">Feedback</span>
                <h2>What Our Guests Say</h2>
                <div class="separator">
                    <span class="line"></span>
                    <span class="icon"><i class="fas fa-quote-right"></i></span>
                    <span class="line"></span>
                </div>
            </div>

            <div class="testimonials-slider">
                <div class="testimonial-slide active">
                    <div class="testimonial-content">
                        <div class="quote-icon">
                            <i class="fas fa-quote-left"></i>
                        </div>
                        <p>Savoria exceeded all my expectations. The food was exceptional, the service impeccable, and the ambiance perfect for our anniversary dinner. The chef's tasting menu was a culinary journey I won't soon forget.</p>
                        <div class="testimonial-rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <div class="testimonial-author">
                            <img src="img/placeholder.php?width=80&height=80&text=JD&bg=222" alt="John Doe">
                            <div class="author-info">
                                <h4>John Doe</h4>
                                <span>Food Critic</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="testimonial-slide">
                    <div class="testimonial-content">
                        <div class="quote-icon">
                            <i class="fas fa-quote-left"></i>
                        </div>
                        <p>We hosted our corporate event at Savoria, and it was a huge success. From the planning process to the execution, the staff was professional and attentive. Our colleagues are still talking about the amazing food!</p>
                        <div class="testimonial-rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <div class="testimonial-author">
                            <img src="img/placeholder.php?width=80&height=80&text=JS&bg=222" alt="Jane Smith">
                            <div class="author-info">
                                <h4>Jane Smith</h4>
                                <span>Event Manager</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="testimonial-slide">
                    <div class="testimonial-content">
                        <div class="quote-icon">
                            <i class="fas fa-quote-left"></i>
                        </div>
                        <p>I've dined at restaurants all over the world, and Savoria ranks among the best. The lobster ravioli is divine, and the wine pairing suggestions were spot-on. It's become our favorite special occasion restaurant.</p>
                        <div class="testimonial-rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                        </div>
                        <div class="testimonial-author">
                            <img src="img/placeholder.php?width=80&height=80&text=RJ&bg=222" alt="Robert Johnson">
                            <div class="author-info">
                                <h4>Robert Johnson</h4>
                                <span>Food Blogger</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="testimonial-controls">
                <button class="testimonial-control active" data-slide="0"></button>
                <button class="testimonial-control" data-slide="1"></button>
                <button class="testimonial-control" data-slide="2"></button>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-column">
                    <div class="footer-logo">
                        <i class="fas fa-utensils"></i>
                        <h3>Savoria</h3>
                    </div>
                    <p>Experience the art of fine dining in an elegant atmosphere with exceptional service and world-class cuisine.</p>
                    <div class="footer-social">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                
                <div class="footer-column">
                    <h3>Contact Info</h3>
                    <div class="footer-contact">
                        <div class="contact-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <p>123 Gourmet Avenue, Culinary District<br>New York, NY 10001</p>
                        </div>
                        <div class="contact-item">
                            <i class="fas fa-phone-alt"></i>
                            <p>(123) 456-7890</p>
                        </div>
                        <div class="contact-item">
                            <i class="fas fa-envelope"></i>
                            <p>info@savoria.com</p>
                        </div>
                    </div>
                </div>
                
                <div class="footer-column">
                    <h3>Opening Hours</h3>
                    <div class="footer-hours">
                        <div class="hours-item">
                            <span class="day">Monday - Friday</span>
                            <span class="time">5:00 PM - 11:00 PM</span>
                        </div>
                        <div class="hours-item">
                            <span class="day">Saturday</span>
                            <span class="time">12:00 PM - 11:00 PM</span>
                        </div>
                        <div class="hours-item">
                            <span class="day">Sunday</span>
                            <span class="time">12:00 PM - 11:00 PM</span>
                        </div>
                        <div class="hours-item special">
                            <span class="day">Happy Hour</span>
                            <span class="time">5:00 PM - 7:00 PM</span>
                        </div>
                    </div>
                </div>
                
                <div class="footer-column">
                    <h3>Newsletter</h3>
                    <p>Subscribe to our newsletter to get updates on special events, promotions, and seasonal menus.</p>
                    <form class="newsletter-form">
                        <input type="email" placeholder="Your Email Address" required>
                        <button type="submit">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="footer-bottom">
                <div class="copyright">
                    <p>&copy; <?php echo date("Y"); ?> Savoria Restaurant. All Rights Reserved.</p>
                </div>
                <div class="footer-links">
                    <a href="#">Privacy Policy</a>
                    <a href="#">Terms of Service</a>
                    <a href="#">Sitemap</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Back to Top Button -->
    <a href="#" class="back-to-top">
        <i class="fas fa-chevron-up"></i>
    </a>

    <!-- Gallery Modal -->
    <div class="gallery-modal">
        <span class="gallery-close">&times;</span>
        <div class="gallery-modal-content">
            <img src="/placeholder.svg" alt="Gallery Image" id="galleryModalImg">
            <div class="gallery-caption"></div>
        </div>
    </div>
<script>
   // Theme Carousel
document.addEventListener('DOMContentLoaded', function() {
    const themeCarousel = document.querySelector('.theme-carousel');
    const themeSlides = document.querySelectorAll('.theme-slide');
    const prevButton = document.querySelector('.theme-prev');
    const nextButton = document.querySelector('.theme-next');
    const dotsContainer = document.querySelector('.theme-carousel-dots');
    const selectedThemeInput = document.getElementById('selected-theme');
    const themeStatus = document.querySelector('.theme-status');
    const themeClearBtn = document.querySelector('.theme-clear');
    
    let currentSlide = 0;
    let slideWidth = 0;
    let isDragging = false;
    let startPos = 0;
    let currentTranslate = 0;
    let prevTranslate = 0;
    let selectedTheme = null;
    
    // Create dots
    themeSlides.forEach((_, index) => {
        const dot = document.createElement('span');
        dot.classList.add('theme-dot');
        if (index === 0) dot.classList.add('active');
        dot.addEventListener('click', () => goToSlide(index));
        dotsContainer.appendChild(dot);
    });
    
    const dots = document.querySelectorAll('.theme-dot');
    
    // Set initial slide width
    function setSlideWidth() {
        slideWidth = themeCarousel.clientWidth;
        themeSlides.forEach(slide => {
            slide.style.width = `${slideWidth}px`;
        });
        updateSlidePosition();
    }
    
    // Update slide position
    function updateSlidePosition() {
        themeCarousel.style.transform = `translateX(${-currentSlide * slideWidth}px)`;
    }
    
    // Go to specific slide
    function goToSlide(index) {
        if (index < 0) {
            index = themeSlides.length - 1;
        } else if (index >= themeSlides.length) {
            index = 0;
        }
        
        currentSlide = index;
        updateSlidePosition();
        
        // Update dots
        dots.forEach((dot, i) => {
            dot.classList.toggle('active', i === currentSlide);
        });
    }
    
    // Next slide
    function nextSlide() {
        goToSlide(currentSlide + 1);
    }
    
    // Previous slide
    function prevSlide() {
        goToSlide(currentSlide - 1);
    }
    
    // Update theme status
    function updateThemeStatus(theme) {
        if (theme) {
            const selectedSlide = document.querySelector(`.theme-slide[data-theme="${theme}"]`);
            const themeName = selectedSlide.querySelector('h4').textContent;
            themeStatus.textContent = `(${themeName} selected)`;
            themeStatus.style.color = 'var(--primary-color)';
            themeClearBtn.disabled = false;
        } else {
            themeStatus.textContent = '(No theme selected)';
            themeStatus.style.color = 'var(--gray-color)';
            themeClearBtn.disabled = true;
        }
    }
    
    // Clear theme selection
    function clearThemeSelection() {
        themeSlides.forEach(slide => slide.classList.remove('selected'));
        selectedThemeInput.value = '';
        selectedTheme = null;
        updateThemeStatus(null);
    }
    
    // Event listeners
    prevButton.addEventListener('click', prevSlide);
    nextButton.addEventListener('click', nextSlide);
    themeClearBtn.addEventListener('click', clearThemeSelection);
    
    // Touch events for mobile swipe
    themeCarousel.addEventListener('touchstart', touchStart);
    themeCarousel.addEventListener('touchmove', touchMove);
    themeCarousel.addEventListener('touchend', touchEnd);
    
    // Mouse events for desktop swipe
    themeCarousel.addEventListener('mousedown', touchStart);
    themeCarousel.addEventListener('mousemove', touchMove);
    themeCarousel.addEventListener('mouseup', touchEnd);
    themeCarousel.addEventListener('mouseleave', touchEnd);
    
    function touchStart(event) {
        isDragging = true;
        startPos = getPositionX(event);
        themeCarousel.style.transition = 'none';
    }
    
    function touchMove(event) {
        if (!isDragging) return;
        const currentPosition = getPositionX(event);
        currentTranslate = prevTranslate + currentPosition - startPos;
        themeCarousel.style.transform = `translateX(${currentTranslate}px)`;
    }
    
    function touchEnd() {
        isDragging = false;
        themeCarousel.style.transition = 'transform 0.3s ease';
        
        const movedBy = currentTranslate - prevTranslate;
        
        // If moved enough negative, next slide
        if (movedBy < -100) {
            nextSlide();
        }
        // If moved enough positive, prev slide
        else if (movedBy > 100) {
            prevSlide();
        }
        // Otherwise, go back to current slide
        else {
            goToSlide(currentSlide);
        }
        
        prevTranslate = -currentSlide * slideWidth;
    }
    
    function getPositionX(event) {
        return event.type.includes('mouse') ? event.pageX : event.touches[0].clientX;
    }
    
    // Theme selection
    themeSlides.forEach(slide => {
        slide.addEventListener('click', function() {
            const themeValue = this.getAttribute('data-theme');
            
            // If clicking the already selected theme, unselect it
            if (this.classList.contains('selected')) {
                clearThemeSelection();
                return;
            }
            
            // Remove selected class from all slides
            themeSlides.forEach(s => s.classList.remove('selected'));
            
            // Add selected class to clicked slide
            this.classList.add('selected');
            
            // Update hidden input value
            selectedThemeInput.value = themeValue;
            selectedTheme = themeValue;
            
            // Update status text
            updateThemeStatus(themeValue);
        });
    });
    
    // Initialize
    setSlideWidth();
    updateThemeStatus(null);
    
    // Update on window resize
    window.addEventListener('resize', setSlideWidth);
    
    // Auto-advance slides every 5 seconds
    let autoSlideInterval = setInterval(nextSlide, 5000);
    
    // Pause auto-slide when interacting with carousel
    themeCarousel.addEventListener('mouseenter', () => {
        clearInterval(autoSlideInterval);
    });
    
    themeCarousel.addEventListener('mouseleave', () => {
        autoSlideInterval = setInterval(nextSlide, 5000);
    });
    
    // Time input formatting
    const timeInput = document.getElementById('reservation-time');
    if (timeInput) {
        timeInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/[^0-9:]/g, '');
            
            // Auto-add colon after hours
            if (value.length === 2 && !value.includes(':')) {
                value += ':';
            }
            
            // Limit to 5 characters (HH:MM)
            if (value.length > 5) {
                value = value.slice(0, 5);
            }
            
            e.target.value = value;
        });
    }
});
</script>
    <script>
        // Preloader
        window.addEventListener('load', function() {
            document.querySelector('.preloader').style.opacity = '0';
            setTimeout(function() {
                document.querySelector('.preloader').style.display = 'none';
            }, 500);
        });

        // Sticky Header
        window.addEventListener('scroll', function() {
            const header = document.getElementById('header');
            if (window.scrollY > 100) {
                header.classList.add('sticky');
            } else {
                header.classList.remove('sticky');
            }
        });

        // Mobile Menu Toggle
        const mobileToggle = document.querySelector('.mobile-nav-toggle');
        const mobileMenu = document.querySelector('.mobile-menu');

        mobileToggle.addEventListener('click', function() {
            this.classList.toggle('active');
            mobileMenu.classList.toggle('active');
        });

        // Close mobile menu when link is clicked
        document.querySelectorAll('.mobile-menu a').forEach(item => {
            item.addEventListener('click', () => {
                mobileMenu.classList.remove('active');
                mobileToggle.classList.remove('active');
            });
        });

        // Smooth Scrolling
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    window.scrollTo({
                        top: target.offsetTop - 80,
                        behavior: 'smooth'
                    });
                }
            });
        });

        // Menu Tabs
        const menuTabs = document.querySelectorAll('.menu-tab');
        const menuItems = document.querySelectorAll('.menu-item');

        menuTabs.forEach(tab => {
            tab.addEventListener('click', function() {
                // Remove active class from all tabs
                menuTabs.forEach(tab => tab.classList.remove('active'));
                
                // Add active class to clicked tab
                this.classList.add('active');
                
                // Get category
                const category = this.getAttribute('data-category');
                
                // Show/hide menu items based on category
                menuItems.forEach(item => {
                    if (category === 'all' || item.getAttribute('data-category') === category) {
                        item.style.display = 'flex';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        });

        // Event Slider
        const eventSlides = document.querySelectorAll('.events-slide');
        const eventControls = document.querySelectorAll('.slider-control');

        eventControls.forEach(control => {
            control.addEventListener('click', function() {
                const slideIndex = parseInt(this.getAttribute('data-slide'));
                
                // Remove active class from all slides and controls
                eventSlides.forEach(slide => slide.classList.remove('active'));
                eventControls.forEach(control => control.classList.remove('active'));
                
                // Add active class to selected slide and control
                eventSlides[slideIndex].classList.add('active');
                this.classList.add('active');
            });
        });

        // Testimonial Slider
        const testimonialSlides = document.querySelectorAll('.testimonial-slide');
        const testimonialControls = document.querySelectorAll('.testimonial-control');

        testimonialControls.forEach(control => {
            control.addEventListener('click', function() {
                const slideIndex = parseInt(this.getAttribute('data-slide'));
                
                // Remove active class from all slides and controls
                testimonialSlides.forEach(slide => slide.classList.remove('active'));
                testimonialControls.forEach(control => control.classList.remove('active'));
                
                // Add active class to selected slide and control
                testimonialSlides[slideIndex].classList.add('active');
                this.classList.add('active');
            });
        });

        // Gallery Filtering
        const galleryFilters = document.querySelectorAll('.gallery-filter');
        const galleryItems = document.querySelectorAll('.gallery-item');

        galleryFilters.forEach(filter => {
            filter.addEventListener('click', function() {
                // Remove active class from all filters
                galleryFilters.forEach(filter => filter.classList.remove('active'));
                
                // Add active class to clicked filter
                this.classList.add('active');
                
                // Get category
                const category = this.getAttribute('data-filter');
                
                // Show/hide gallery items based on category
                galleryItems.forEach(item => {
                    if (category === 'all' || item.getAttribute('data-category') === category) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        });

        // Gallery Modal
        const galleryModal = document.querySelector('.gallery-modal');
        const galleryModalImg = document.getElementById('galleryModalImg');
        const galleryCaption = document.querySelector('.gallery-caption');
        const galleryClose = document.querySelector('.gallery-close');
        const galleryExpand = document.querySelectorAll('.gallery-expand');

        galleryExpand.forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                galleryModal.style.display = 'flex';
                galleryModalImg.src = this.getAttribute('href');
                galleryCaption.innerHTML = this.parentElement.querySelector('.gallery-info h3').innerHTML;
                document.body.style.overflow = 'hidden';
            });
        });

        galleryClose.addEventListener('click', function() {
            galleryModal.style.display = 'none';
            document.body.style.overflow = 'auto';
        });

        window.addEventListener('click', function(e) {
            if (e.target === galleryModal) {
                galleryModal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        });

        // Scroll animations
        const revealElements = document.querySelectorAll('.reveal-up, .reveal-left, .reveal-right');

        function revealOnScroll() {
            revealElements.forEach(element => {
                const elementTop = element.getBoundingClientRect().top;
                const windowHeight = window.innerHeight;
                
                if (elementTop < windowHeight - 50) {
                    element.classList.add('active');
                }
            });
        }

        window.addEventListener('scroll', revealOnScroll);
        window.addEventListener('load', revealOnScroll);

        // Back to top button
        const backToTopButton = document.querySelector('.back-to-top');

        window.addEventListener('scroll', function() {
            if (window.scrollY > 300) {
                backToTopButton.classList.add('visible');
            } else {
                backToTopButton.classList.remove('visible');
            }
        });

        backToTopButton.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });

        // Map Overlay
        const mapOverlay = document.querySelector('.map-overlay');
        
        mapOverlay.addEventListener('click', function() {
            this.style.display = 'none';
        });
    </script>
</body>
</html>