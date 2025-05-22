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


    $menu_items_stmt = $pdo->query("SELECT * FROM menu_items ORDER BY category, id");
    $menu_items = $menu_items_stmt->fetchAll(PDO::FETCH_ASSOC);    
    
    ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DIWATA RESTAURANT</title>
    <link rel="stylesheet" href="../css/restaurant.css">
    <link rel="stylesheet" href="../css/reservation.css">
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
            <p>DIWATA RESTAURANT</p>
        </div>
    </div>

    <!-- Header -->
    <header id="header">
        <div class="container">
            <div class="logo">
                <a href="index.php">
                    <span class="logo-icon"><i class="fas fa-utensils"></i></span>
                    <span class="logo-text">DIWATA RESTAURANT</span>
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
                            <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
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
                <h1 class="reveal-text">Welcome to <span>Diwata Restaurant</span></h1>
                <p class="reveal-text delay-1">The classic Filipino braised beef stew with unlimited rice, soup, and water. Owned by internet personality Diwata</p>
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
                <h2>About Diwata Restaurant</h2>
                <div class="separator">
                    <span class="line"></span>
                    <span class="icon"><i class="fas fa-utensils"></i></span>
                    <span class="line"></span>
                </div>
            </div>

            <div class="about-content">
                <div class="about-image reveal-left">
                    <div class="image-frame">
                        <img src="../image/logoo.jpg" alt="Restaurant Interior">
                    </div>
                    <div class="experience">
                        <span class="number">9</span>
                        <span class="text">Years of Excellence</span>
                    </div>
                </div>
                <div class="about-text reveal-right">
                    <h3>Diwata Established in 2016 at Diokno Boulevard</h3>
                    <p>Diwata Pares Overload serves delicious Filipino comfort food, best known for its flavorful and tender pares with garlic rice and soup. With generous servings and affordable prices, it's a go-to spot for a satisfying meal.
                    </p>
                    <p>More than just food, Diwata Pares Overload offers a fun, local dining experience. It's where great taste, big portions, and good vibes come together in every bite.</p>
                    
                    <div class="features">
                        <div class="feature">
                            <div class="icon">
                                <i class="fas fa-award"></i>
                            </div>
                            <div class="text">
                                <h4>Award Winning</h4>
                                <p>Recognized for everyday clearing operation by SCOG</p>
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
            <?php foreach ($menu_items as $item): ?>
                <div class="menu-item reveal-up" data-category="<?= htmlspecialchars($item['category']) ?>">
                    <div class="menu-image">
                        <img src="<?= htmlspecialchars($item['image_url']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                    </div>
                    <div class="menu-content">
                        <div class="menu-title">
                            <h3><?= htmlspecialchars($item['name']) ?></h3>
                            <span class="price">₱ <?= number_format($item['price'], 2) ?></span>
                        </div>
                        <div class="menu-ingredients"><?= htmlspecialchars($item['description']) ?></div>
                        <?php if (!empty($item['badge'])): ?>
                            <span class="badge"><?= htmlspecialchars($item['badge']) ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
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
                        <img src="../image/wedding.jpeg" alt="Wedding Reception">
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
                        <img src="../image/Corporate.jpg" alt="Corporate Events">
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
                        <img src="../image/Special.jpg" alt="Special Celebrations">
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
                        <img src="../image/Chef1.jpg" alt="Executive Chef">
                        <div class="chef-social">
                            <a href="#"><i class="fab fa-instagram"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                    <div class="chef-info">
                        <h3>Chef Diwata</h3>
                        <span class="chef-title">Executive Chef</span>
                        <p>With 20 years of experience in Michelin-starred restaurants across Europe, Chef Alessandro brings his passion for Italian cuisine with a modern twist to Savoria.</p>
                    </div>
                </div>

                <div class="chef-card reveal-up delay-1">
                    <div class="chef-image">
                        <img src="../image/chef3.jpg" alt="Pastry Chef">
                        <div class="chef-social">
                            <a href="#"><i class="fab fa-instagram"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                    <div class="chef-info">
                        <h3>Chef Gordon Ramsay</h3>
                        <span class="chef-title">Head Pastry Chef</span>
                        <p>Trained in Paris, Chef Sophia creates exquisite desserts that are as beautiful as they are delicious. Her signature chocolate soufflé is not to be missed.</p>
                    </div>
                </div>

                <div class="chef-card reveal-up delay-2">
                    <div class="chef-image">
                        <img src="../image/chef2.png" alt="Sous Chef">
                        <div class="chef-social">
                            <a href="#"><i class="fab fa-instagram"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                    <div class="chef-info">
                        <h3>chef Ninong Bry</h3>
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
                    <img src="../image/NORMAL.jpg" alt="Signature Dish">
                    <div class="gallery-overlay">
                        <div class="gallery-info">
                            <h3>NORMAL Occasions</h3>
                            <span>Signature Venue</span>
                        </div>
                        <a href="../image/NORMAL.jpg" class="gallery-expand">
                            <i class="fas fa-search-plus"></i>
                        </a>
                    </div>
                </div>

                <div class="gallery-item reveal-up" data-category="interior">
                    <img src="../image/FAMILY.jpg" alt="Main Dining Area">
                    <div class="gallery-overlay">
                        <div class="gallery-info">
                            <h3>Main Dining Area</h3>
                            <span>Interior</span>
                        </div>
                        <a href="../image/FAMILY.jpg" class="gallery-expand">
                            <i class="fas fa-search-plus"></i>
                        </a>
                    </div>
                </div>

                <div class="gallery-item reveal-up" data-category="events">
                    <img src="../image/Special.jpg" alt="Private Event">
                    <div class="gallery-overlay">
                        <div class="gallery-info">
                            <h3>Anniversary Celebration</h3>
                            <span>Special Event</span>
                        </div>
                        <a href="../image/Special.jpg" class="gallery-expand">
                            <i class="fas fa-search-plus"></i>
                        </a>
                    </div>
                </div>

                <div class="gallery-item reveal-up" data-category="food">
                    <img src="../image/wedding.jpeg" alt="Dessert Platter">
                    <div class="gallery-overlay">
                        <div class="gallery-info">
                            <h3>Wedding Celebration</h3>
                            <span>Sweet Creations</span>
                        </div>
                        <a href="../image/wedding.jpeg" class="gallery-expand">
                            <i class="fas fa-search-plus"></i>
                        </a>
                    </div>
                </div>

                <div class="gallery-item reveal-up" data-category="interior">
                    <img src="../image/CORPORATE theme.jpg" alt="Bar Area">
                    <div class="gallery-overlay">
                        <div class="gallery-info">
                            <h3>Cocktail Bar</h3>
                            <span>Interior</span>
                        </div>
                        <a href="../image/CORPORATE theme.jpg" class="gallery-expand">
                            <i class="fas fa-search-plus"></i>
                        </a>
                    </div>
                </div>

                <div class="gallery-item reveal-up" data-category="events">
                    <img src="../image/bday theme.jpg" alt="Wine Tasting">
                    <div class="gallery-overlay">
                        <div class="gallery-info">
                            <h3>Birthday Celebration</h3>
                            <span>Special Event</span>
                        </div>
                        <a href="../image/bday theme.jpg" class="gallery-expand">
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
            
            <!-- Availability Table -->
            <div class="availability-container">
                <h3>Available Time Slots</h3>
                <div class="time-slots-container" id="availabilityTable">
                    <!-- This will be populated by AJAX -->
                    <div class="loading">Loading availability...</div>
                </div>
            </div>

            <div class="reservation-container">
                <div class="reservation-image reveal-left">
                    <img src="../image/logoo.jpg" alt="Reservation">
                    <div class="reservation-info">
                        <div class="info-item">
                            <i class="fas fa-phone"></i>
                            <div>
                                <h4>Phone</h4>
                                <p>(123) 999-9999</p>
                            </div>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-envelope"></i>
                            <div>
                                <h4>Email</h4>
                                <p>DiwataPares@gmail.com</p>
                            </div>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-clock"></i>
                            <div>
                                <h4>Hours</h4>
                                <p>7:00 AM - 12:00 AM</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="reservation-form reveal-right">
                    <form action="../customer/process_reservation.php" method="POST" id="reservationForm">
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
                            <div class="form-group">
                                <label for="time-slot">Time Slot</label>
                                <select id="time-slot" name="time_slot" required>
                                    <option value="" disabled selected>Select Time Slot</option>
                                    <option value="morning">Morning (7:00 AM - 12:30 PM)</option>
                                    <option value="afternoon">Afternoon (1:00 PM - 6:30 PM)</option>
                                    <option value="evening">Evening (7:00 PM - 12:30 AM)</option>
                                </select>
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
                        
                        <!-- Food Package Selection -->
                        <div class="form-group">
                            <label for="food-package">Food Package</label>
                            <select id="food-package" name="food_package" required>
                                <option value="" disabled selected>Select Food Package</option>
                                <option value="basic">Basic Package (₱3,500)</option>
                                <option value="standard">Standard Package (₱5,000)</option>
                                <option value="premium">Premium Package (₱7,500)</option>
                                <option value="deluxe">Deluxe Package (₱10,000)</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>Theme and occasion <span class="theme-status">(No theme selected)</span></label>
                            <div class="theme-carousel-container">
                                <div class="theme-carousel">
                                    <div class="theme-slide" data-theme="birthday" data-price="2500">
                                        <div class="theme-slide-content">
                                            <img src="../image/bday theme.jpg" alt="Birthday">
                                            <div class="theme-info">
                                                <h4>Birthday (₱2,500)</h4>
                                                <p>Celebrate your special day with us</p>
                                            </div>
                                            <div class="theme-select-overlay">
                                                <span class="select-indicator"><i class="fas fa-check-circle"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="theme-slide" data-theme="anniversary" data-price="3000">
                                        <div class="theme-slide-content">
                                            <img src="../image/FAMILY.jpg" alt="Anniversary">
                                            <div class="theme-info">
                                                <h4>Anniversary (₱3,000)</h4>
                                                <p>Romantic setting for your celebration</p>
                                            </div>
                                            <div class="theme-select-overlay">
                                                <span class="select-indicator"><i class="fas fa-check-circle"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="theme-slide" data-theme="corporate" data-price="5000">
                                        <div class="theme-slide-content">
                                            <img src="../image/CORPORATE theme.jpg" alt="Corporate">
                                            <div class="theme-info">
                                                <h4>Corporate (₱5,000)</h4>
                                                <p>Professional setting for business events</p>
                                            </div>
                                            <div class="theme-select-overlay">
                                                <span class="select-indicator"><i class="fas fa-check-circle"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="theme-slide" data-theme="casual" data-price="1500">
                                        <div class="theme-slide-content">
                                            <img src="../image/NORMAL.jpg" alt="Casual">
                                            <div class="theme-info">
                                                <h4>Casual (₱1,500)</h4>
                                                <p>Relaxed atmosphere for friendly gatherings</p>
                                            </div>
                                            <div class="theme-select-overlay">
                                                <span class="select-indicator"><i class="fas fa-check-circle"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="theme-slide" data-theme="wedding" data-price="8000">
                                        <div class="theme-slide-content">
                                            <img src="../image/wedding.jpeg" alt="Wedding">
                                            <div class="theme-info">
                                                <h4>Wedding (₱8,000)</h4>
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
                                <!-- Hidden inputs to store the selected theme value and price -->
                                <input type="hidden" id="selected-theme" name="theme" value="">
                                <input type="hidden" id="theme-price" name="theme_price" value="0">
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
                        
                        <div class="form-group total-price-container">
                            <h3>Total Price: <span id="total-price">₱0.00</span></h3>
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
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d990.5762615856413!2d120.98220987681289!3d14.548933268144285!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397cb006d580d4b%3A0x9c26bfec3a729c8e!2sDiwata%20Pares%20Overload%20Pasay!5e0!3m2!1sen!2sus!4v1747833763813!5m2!1sen!2sus" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
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
fetch('get_menu.php')
    .then(response => response.json())
    .then(data => {
        const container = document.querySelector('.menu-items');
        container.innerHTML = '';

        data.forEach(item => {
            container.innerHTML += `
                <div class="menu-item reveal-up" data-category="${item.category}">
                    <div class="menu-image">
                        <img src="${item.image_url}" alt="${item.name}">
                    </div>
                    <div class="menu-content">
                        <div class="menu-title">
                            <h3>${item.name}</h3>
                            <span class="price">$${item.price}</span>
                        </div>
                        <div class="menu-ingredients">${item.description}</div>
                        ${item.badge ? `<span class="badge">${item.badge}</span>` : ''}
                    </div>
                </div>
            `;
        });
    });
</script>

<script>
   document.addEventListener('DOMContentLoaded', function() {
    // Theme carousel functionality
    const carousel = document.querySelector('.theme-carousel');
    const slides = document.querySelectorAll('.theme-slide');
    const prevBtn = document.querySelector('.theme-prev');
    const nextBtn = document.querySelector('.theme-next');
    const dotsContainer = document.querySelector('.theme-carousel-dots');
    const clearBtn = document.querySelector('.theme-clear');
    const themeStatus = document.querySelector('.theme-status');
    const selectedThemeInput = document.getElementById('selected-theme');
    const themePriceInput = document.getElementById('theme-price');
    
    // Food package select
    const foodPackageSelect = document.getElementById('food-package');
    
    // Total price element
    const totalPriceElement = document.getElementById('total-price');
    
    // Date input
    const dateInput = document.getElementById('reservation-date');
    
    // Time slot select
    const timeSlotSelect = document.getElementById('time-slot');
    
    // Set minimum date to today
    const today = new Date();
    const yyyy = today.getFullYear();
    const mm = String(today.getMonth() + 1).padStart(2, '0');
    const dd = String(today.getDate()).padStart(2, '0');
    const formattedToday = `${yyyy}-${mm}-${dd}`;
    dateInput.min = formattedToday;
    
    // Create dots for carousel
    let currentSlide = 0;
    slides.forEach((_, index) => {
        const dot = document.createElement('div');
        dot.classList.add('dot');
        if (index === 0) dot.classList.add('active');
        dot.addEventListener('click', () => goToSlide(index));
        dotsContainer.appendChild(dot);
    });
    
    // Carousel navigation
    function goToSlide(index) {
        if (index < 0) index = slides.length - 1;
        if (index >= slides.length) index = 0;
        
        carousel.scrollLeft = slides[index].offsetLeft;
        currentSlide = index;
        
        // Update dots
        document.querySelectorAll('.dot').forEach((dot, i) => {
            dot.classList.toggle('active', i === index);
        });
    }
    
    prevBtn.addEventListener('click', () => goToSlide(currentSlide - 1));
    nextBtn.addEventListener('click', () => goToSlide(currentSlide + 1));
    
    // Theme selection
    slides.forEach(slide => {
        slide.addEventListener('click', function() {
            // Remove selected class from all slides
            slides.forEach(s => s.classList.remove('selected'));
            
            // Add selected class to clicked slide
            this.classList.add('selected');
            
            // Update hidden input value
            const theme = this.getAttribute('data-theme');
            const price = this.getAttribute('data-price');
            selectedThemeInput.value = theme;
            themePriceInput.value = price;
            
            // Update theme status text
            themeStatus.textContent = `(${theme.charAt(0).toUpperCase() + theme.slice(1)} selected)`;
            
            // Enable clear button
            clearBtn.disabled = false;
            
            // Update total price
            updateTotalPrice();
        });
    });
    
    // Clear theme selection
    clearBtn.addEventListener('click', function() {
        slides.forEach(slide => slide.classList.remove('selected'));
        selectedThemeInput.value = '';
        themePriceInput.value = '0';
        themeStatus.textContent = '(No theme selected)';
        this.disabled = true;
        updateTotalPrice();
    });
    
    // Food package change
    foodPackageSelect.addEventListener('change', updateTotalPrice);
    
    // Update total price function
    function updateTotalPrice() {
        let total = 0;
        
        // Add theme price
        const themePrice = parseInt(themePriceInput.value) || 0;
        total += themePrice;
        
        // Add food package price
        const foodPackage = foodPackageSelect.value;
        if (foodPackage === 'basic') {
            total += 3500;
        } else if (foodPackage === 'standard') {
            total += 5000;
        } else if (foodPackage === 'premium') {
            total += 7500;
        } else if (foodPackage === 'deluxe') {
            total += 10000;
        }
        
        // Format and display total
        totalPriceElement.textContent = `₱${total.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
    }
    
    // Load availability data when date or time slot changes
    dateInput.addEventListener('change', loadAvailability);
    
    // Function to load availability data
    function loadAvailability() {
        const date = dateInput.value;
        if (!date) return;
        
        const availabilityTable = document.getElementById('availabilityTable');
        availabilityTable.innerHTML = '<div class="loading">Loading availability...</div>';
        
        // AJAX request to get availability
        fetch(`../customer/check_availability.php?date=${date}`)
            .then(response => response.json())
            .then(data => {
                availabilityTable.innerHTML = '';
                
                // Create time slot cards
                const timeSlots = [
                    { id: 'morning', name: 'Morning', time: '7:00 AM - 12:30 PM' },
                    { id: 'afternoon', name: 'Afternoon', time: '1:00 PM - 6:30 PM' },
                    { id: 'evening', name: 'Evening', time: '7:00 PM - 12:30 AM' }
                ];
                
                timeSlots.forEach(slot => {
                    const isAvailable = data[slot.id];
                    const slotElement = document.createElement('div');
                    slotElement.className = 'time-slot';
                    slotElement.innerHTML = `
                        <h4>${slot.name}</h4>
                        <p>${slot.time}</p>
                        <p class="time-slot-status ${isAvailable ? 'available' : 'booked'}">
                            ${isAvailable ? 'Available' : 'Booked'}
                        </p>
                    `;
                    availabilityTable.appendChild(slotElement);
                    
                    // Disable booked options in the select
                    const option = timeSlotSelect.querySelector(`option[value="${slot.id}"]`);
                    if (option) {
                        if (!isAvailable) {
                            option.disabled = true;
                        } else {
                            option.disabled = false;
                        }
                    }
                });
            })
            .catch(error => {
                console.error('Error loading availability:', error);
                availabilityTable.innerHTML = '<div class="loading">Error loading availability. Please try again.</div>';
            });
    }
    
    // Form submission
    const form = document.getElementById('reservationForm');
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Basic validation
        const requiredFields = ['name', 'email', 'phone', 'reservation-date', 'time-slot', 'guests', 'food-package'];
        let isValid = true;
        
        requiredFields.forEach(field => {
            const element = document.getElementById(field);
            if (!element.value) {
                isValid = false;
                element.style.borderColor = 'red';
            } else {
                element.style.borderColor = '';
            }
        });
        
        if (!isValid) {
            alert('Please fill in all required fields.');
            return;
        }
        
        // Submit form via AJAX
        const formData = new FormData(form);
        
        fetch('../customer/process_reservation.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Reservation submitted successfully! We will contact you soon to confirm your reservation.');
                form.reset();
                slides.forEach(slide => slide.classList.remove('selected'));
                themeStatus.textContent = '(No theme selected)';
                clearBtn.disabled = true;
                updateTotalPrice();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again later.');
        });
    });
    
    // Initialize
    updateTotalPrice();
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