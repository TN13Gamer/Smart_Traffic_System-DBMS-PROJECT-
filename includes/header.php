<?php
/**
 * Smart Traffic Violation Monitoring System
 * Shared Header Template
 * 
 * This file contains the standard head elements, CSS links,
 * and the fixed premium sidebar navigation bar used across all pages.
 */

// Determine the active page to apply active styling in sidebar
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Traffic Violation Monitoring System</title>
    
    <!-- Google Fonts (Outfit - modern geometric sans-serif) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- FontAwesome Icons CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Premium Custom Stylesheet -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <!-- 1. Fixed Dashboard Sidebar -->
    <nav class="sidebar">
        <div class="sidebar-brand">
            <i class="fa-solid fa-traffic-light"></i>
            <h1>Smart Traffic</h1>
        </div>
        
        <ul class="sidebar-menu">
            <!-- Dashboard Link -->
            <li class="sidebar-menu-item <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">
                <a href="index.php">
                    <i class="fa-solid fa-chart-line"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            
            <!-- Owners Link -->
            <li class="sidebar-menu-item <?php echo ($current_page == 'owners.php' || $current_page == 'owner_edit.php') ? 'active' : ''; ?>">
                <a href="owners.php">
                    <i class="fa-solid fa-users"></i>
                    <span>Owners</span>
                </a>
            </li>
            
            <!-- Vehicles Link -->
            <li class="sidebar-menu-item <?php echo ($current_page == 'vehicles.php' || $current_page == 'vehicle_edit.php') ? 'active' : ''; ?>">
                <a href="vehicles.php">
                    <i class="fa-solid fa-car"></i>
                    <span>Vehicles</span>
                </a>
            </li>
            
            <!-- Officers Link -->
            <li class="sidebar-menu-item <?php echo ($current_page == 'officers.php' || $current_page == 'officer_edit.php') ? 'active' : ''; ?>">
                <a href="officers.php">
                    <i class="fa-solid fa-user-shield"></i>
                    <span>Officers</span>
                </a>
            </li>
            
            <!-- Violations Link -->
            <li class="sidebar-menu-item <?php echo ($current_page == 'violations.php' || $current_page == 'violation_edit.php') ? 'active' : ''; ?>">
                <a href="violations.php">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <span>Violations</span>
                </a>
            </li>
            
            <!-- Challans Link -->
            <li class="sidebar-menu-item <?php echo ($current_page == 'challans.php' || $current_page == 'challan_edit.php') ? 'active' : ''; ?>">
                <a href="challans.php">
                    <i class="fa-solid fa-receipt"></i>
                    <span>Challans</span>
                </a>
            </li>
        </ul>
        
        <div class="sidebar-footer">
            <p>DBMS Mini Project</p>
            <p style="font-size: 10px; margin-top: 4px; color: var(--primary);">v1.0.0 • Stable</p>
        </div>
    </nav>

    <!-- 2. Main Page Wrapper -->
    <main class="main-content">
        
        <!-- Top Navbar with Header & Dynamic Date Badge -->
        <header class="top-navbar">
            <div class="top-navbar-title">
                <?php
                // Display custom dynamic subtitles based on current page
                if ($current_page == 'index.php') {
                    echo "<h2>Control Center Dashboard</h2>";
                    echo "<p>Real-time analytics and data synchronization overview</p>";
                } elseif ($current_page == 'owners.php' || $current_page == 'owner_edit.php') {
                    echo "<h2>Vehicle Owners Registry</h2>";
                    echo "<p>Manage system registered drivers and driver licensing data</p>";
                } elseif ($current_page == 'vehicles.php' || $current_page == 'vehicle_edit.php') {
                    echo "<h2>Registered Vehicles</h2>";
                    echo "<p>Manage active automotive details and database linkage to owners</p>";
                } elseif ($current_page == 'officers.php' || $current_page == 'officer_edit.php') {
                    echo "<h2>Traffic Duty Officers</h2>";
                    echo "<p>Manage law enforcement personnel, roster status and phone contacts</p>";
                } elseif ($current_page == 'violations.php' || $current_page == 'violation_edit.php') {
                    echo "<h2>Traffic Law Violations</h2>";
                    echo "<p>Record, review, update and audit vehicular offenses and fine structures</p>";
                } elseif ($current_page == 'challans.php' || $current_page == 'challan_edit.php') {
                    echo "<h2>Challan Invoicing Ticket System</h2>";
                    echo "<p>Generate and audit formal digital challan records issued by duty officers</p>";
                }
                ?>
            </div>
            
            <div class="top-navbar-actions">
                <div class="time-badge">
                    <i class="fa-regular fa-clock"></i>
                    <span id="live-clock"><?php echo date('Y-m-d H:i'); ?></span>
                </div>
            </div>
        </header>
        
        <!-- Opening section for page body container -->
        <div class="page-container">
