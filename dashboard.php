<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login/login.php');
    exit();
}

// Get user information from session
$user_name = $_SESSION['user_name'];
$user_email = $_SESSION['user_email'];
$user_role = $_SESSION['user_role'];
$user_country = $_SESSION['user_country'];
$user_city = $_SESSION['user_city'];
$user_phone = $_SESSION['user_phone'];

// Determine user type
$user_type = ($user_role == 1) ? 'Customer' : 'Restaurant Owner';
$welcome_message = ($user_role == 1) ? 'Welcome to your customer dashboard!' : 'Welcome to your restaurant owner dashboard!';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Taste of Africa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .navbar-custom {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255,255,255,0.2);
        }

        .navbar-brand {
            color: white !important;
            font-weight: 700;
            font-size: 1.5rem;
        }

        .nav-link {
            color: rgba(255,255,255,0.9) !important;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .nav-link:hover {
            color: white !important;
        }

        .dashboard-container {
            padding-top: 100px;
            min-height: 100vh;
        }

        .welcome-card {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
            border: 1px solid rgba(255,255,255,0.2);
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
        }

        .welcome-title {
            color: white;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .welcome-subtitle {
            color: rgba(255,255,255,0.8);
            font-size: 1.2rem;
            margin-bottom: 2rem;
        }

        .user-info-card {
            background: rgba(255,255,255,0.95);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
        }

        .info-item {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
            padding: 0.5rem 0;
            border-bottom: 1px solid rgba(0,0,0,0.1);
        }

        .info-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }

        .info-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(45deg, #D19C97, #b77a7a);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            margin-right: 1rem;
            font-size: 1.1rem;
        }

        .info-label {
            font-weight: 600;
            color: #333;
            margin-right: 0.5rem;
            min-width: 120px;
        }

        .info-value {
            color: #666;
            flex: 1;
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin-top: 2rem;
        }

        .btn-custom {
            background: linear-gradient(45deg, #D19C97, #b77a7a);
            border: none;
            color: white;
            padding: 12px 24px;
            font-weight: 600;
            border-radius: 50px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(209, 156, 151, 0.4);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
        }

        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(209, 156, 151, 0.6);
            color: white;
        }

        .btn-outline-custom {
            border: 2px solid #D19C97;
            color: #D19C97;
            background: transparent;
            padding: 12px 24px;
            font-weight: 600;
            border-radius: 50px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
        }

        .btn-outline-custom:hover {
            background: #D19C97;
            color: white;
            transform: translateY(-2px);
        }

        .btn-danger-custom {
            background: linear-gradient(45deg, #dc3545, #c82333);
            border: none;
            color: white;
            padding: 12px 24px;
            font-weight: 600;
            border-radius: 50px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.4);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
        }

        .btn-danger-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(220, 53, 69, 0.6);
            color: white;
        }

        .role-badge {
            background: linear-gradient(45deg, #D19C97, #b77a7a);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            display: inline-block;
        }

        .stats-card {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 1.5rem;
            text-align: center;
            border: 1px solid rgba(255,255,255,0.2);
            margin-bottom: 1rem;
        }

        .stats-number {
            font-size: 2rem;
            font-weight: 700;
            color: white;
            margin-bottom: 0.5rem;
        }

        .stats-label {
            color: rgba(255,255,255,0.8);
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-custom fixed-top">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">
                <i class="fas fa-utensils me-2"></i>Taste of Africa
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">
                            <i class="fas fa-home me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="login/logout.php">
                            <i class="fas fa-sign-out-alt me-1"></i>Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Dashboard Content -->
    <div class="container dashboard-container">
        <!-- Welcome Section -->
        <div class="welcome-card animate__animated animate__fadeInDown">
            <h1 class="welcome-title">
                <i class="fas fa-user-circle me-3"></i>Hello, <?php echo htmlspecialchars($user_name); ?>!
            </h1>
            <p class="welcome-subtitle"><?php echo $welcome_message; ?></p>
            <span class="role-badge">
                <i class="fas fa-<?php echo ($user_role == 1) ? 'user' : 'store'; ?> me-2"></i>
                <?php echo $user_type; ?>
            </span>
        </div>

        <div class="row">
            <!-- User Information -->
            <div class="col-lg-8">
                <div class="user-info-card animate__animated animate__fadeInLeft">
                    <h3 class="mb-4">
                        <i class="fas fa-info-circle me-2" style="color: #D19C97;"></i>
                        Your Information
                    </h3>
                    
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-user"></i>
                        </div>
                        <span class="info-label">Name:</span>
                        <span class="info-value"><?php echo htmlspecialchars($user_name); ?></span>
                    </div>

                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <span class="info-label">Email:</span>
                        <span class="info-value"><?php echo htmlspecialchars($user_email); ?></span>
                    </div>

                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <span class="info-label">Phone:</span>
                        <span class="info-value"><?php echo htmlspecialchars($user_phone); ?></span>
                    </div>

                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-globe"></i>
                        </div>
                        <span class="info-label">Country:</span>
                        <span class="info-value"><?php echo htmlspecialchars($user_country); ?></span>
                    </div>

                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <span class="info-label">City:</span>
                        <span class="info-value"><?php echo htmlspecialchars($user_city); ?></span>
                    </div>

                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-<?php echo ($user_role == 1) ? 'user' : 'store'; ?>"></i>
                        </div>
                        <span class="info-label">Role:</span>
                        <span class="info-value"><?php echo $user_type; ?></span>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="col-lg-4">
                <div class="stats-card animate__animated animate__fadeInRight">
                    <div class="stats-number">1</div>
                    <div class="stats-label">Account Created</div>
                </div>

                <div class="stats-card animate__animated animate__fadeInRight" style="animation-delay: 0.2s;">
                    <div class="stats-number">0</div>
                    <div class="stats-label">Restaurants Found</div>
                </div>

                <div class="stats-card animate__animated animate__fadeInRight" style="animation-delay: 0.4s;">
                    <div class="stats-number">0</div>
                    <div class="stats-label">Reviews Written</div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="row">
            <div class="col-12">
                <div class="welcome-card animate__animated animate__fadeInUp">
                    <h3 class="mb-4" style="color: white;">
                        <i class="fas fa-rocket me-2"></i>Quick Actions
                    </h3>
                    <div class="action-buttons">
                        <?php if ($user_role == 1): ?>
                            <!-- Customer Actions -->
                            <a href="#" class="btn btn-custom">
                                <i class="fas fa-search me-2"></i>Find Restaurants
                            </a>
                            <a href="#" class="btn btn-outline-custom">
                                <i class="fas fa-star me-2"></i>My Reviews
                            </a>
                            <a href="#" class="btn btn-outline-custom">
                                <i class="fas fa-heart me-2"></i>Favorites
                            </a>
                        <?php else: ?>
                            <!-- Restaurant Owner Actions -->
                            <a href="#" class="btn btn-custom">
                                <i class="fas fa-plus me-2"></i>Add Restaurant
                            </a>
                            <a href="#" class="btn btn-outline-custom">
                                <i class="fas fa-chart-bar me-2"></i>Analytics
                            </a>
                            <a href="#" class="btn btn-outline-custom">
                                <i class="fas fa-comments me-2"></i>Reviews
                            </a>
                        <?php endif; ?>
                        
                        <a href="login/logout.php" class="btn btn-danger-custom">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Add some interactive animations
        $(document).ready(function() {
            // Animate stats numbers
            $('.stats-number').each(function() {
                const $this = $(this);
                const countTo = parseInt($this.text());
                
                $({ countNum: 0 }).animate({
                    countNum: countTo
                }, {
                    duration: 2000,
                    easing: 'swing',
                    step: function() {
                        $this.text(Math.floor(this.countNum));
                    },
                    complete: function() {
                        $this.text(this.countNum);
                    }
                });
            });
        });
    </script>
</body>
</html>
