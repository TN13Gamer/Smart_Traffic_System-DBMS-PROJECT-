<?php
/**
 * Smart Traffic Violation Monitoring System
 * Central Analytics Dashboard (index.php)
 * 
 * This is the landing page. It displays aggregate statistics computed directly
 * from the database using SQL queries, demonstrating primary relational queries.
 * It also lists the most recent challans issued using high-performance SQL JOINs.
 */

// 1. Include database connection
require_once 'config/db_connect.php';

// 2. Fetch statistics using COUNT and SUM queries
// Count Owners
$owner_count_query = "SELECT COUNT(*) AS total_owners FROM OWNER";
$owner_count_result = $conn->query($owner_count_query);
$total_owners = ($owner_count_result) ? $owner_count_result->fetch_assoc()['total_owners'] : 0;

// Count Vehicles
$vehicle_count_query = "SELECT COUNT(*) AS total_vehicles FROM VEHICLE";
$vehicle_count_result = $conn->query($vehicle_count_query);
$total_vehicles = ($vehicle_count_result) ? $vehicle_count_result->fetch_assoc()['total_vehicles'] : 0;

// Count Officers
$officer_count_query = "SELECT COUNT(*) AS total_officers FROM OFFICER";
$officer_count_result = $conn->query($officer_count_query);
$total_officers = ($officer_count_result) ? $officer_count_result->fetch_assoc()['total_officers'] : 0;

// Count Violations
$violation_count_query = "SELECT COUNT(*) AS total_violations FROM VIOLATION";
$violation_count_result = $conn->query($violation_count_query);
$total_violations = ($violation_count_result) ? $violation_count_result->fetch_assoc()['total_violations'] : 0;

// Sum of Fines Collected (Status = 'Paid')
$paid_fines_query = "SELECT SUM(Fine_Amount) AS total_paid FROM VIOLATION WHERE Status = 'Paid'";
$paid_fines_result = $conn->query($paid_fines_query);
$total_paid = ($paid_fines_result) ? $paid_fines_result->fetch_assoc()['total_paid'] : 0;
$total_paid = $total_paid ? $total_paid : 0.00;

// Sum of Pending Fines (Status = 'Pending')
$pending_fines_query = "SELECT SUM(Fine_Amount) AS total_pending FROM VIOLATION WHERE Status = 'Pending'";
$pending_fines_result = $conn->query($pending_fines_query);
$total_pending = ($pending_fines_result) ? $pending_fines_result->fetch_assoc()['total_pending'] : 0;
$total_pending = $total_pending ? $total_pending : 0.00;

// 3. Include shared header
require_once 'includes/header.php';
?>

<!-- Metric Cards Grid -->
<div class="metrics-grid">
    <!-- Card 1: Registered Owners -->
    <div class="metric-card owners">
        <div class="metric-info">
            <h3>Registered Owners</h3>
            <span class="number"><?php echo $total_owners; ?></span>
        </div>
        <div class="metric-icon">
            <i class="fa-solid fa-users"></i>
        </div>
    </div>
    
    <!-- Card 2: Registered Vehicles -->
    <div class="metric-card vehicles">
        <div class="metric-info">
            <h3>Total Vehicles</h3>
            <span class="number"><?php echo $total_vehicles; ?></span>
        </div>
        <div class="metric-icon">
            <i class="fa-solid fa-car"></i>
        </div>
    </div>

    <!-- Card 3: Active Officers -->
    <div class="metric-card officers">
        <div class="metric-info">
            <h3>Active Officers</h3>
            <span class="number"><?php echo $total_officers; ?></span>
        </div>
        <div class="metric-icon">
            <i class="fa-solid fa-user-shield"></i>
        </div>
    </div>

    <!-- Card 4: Total Violations -->
    <div class="metric-card violations">
        <div class="metric-info">
            <h3>Total Violations</h3>
            <span class="number"><?php echo $total_violations; ?></span>
        </div>
        <div class="metric-icon">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
    </div>
</div>

<!-- Financial Summary Section -->
<div class="metrics-grid" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); margin-bottom: 40px;">
    <!-- Revenue Collected -->
    <div class="metric-card" style="border-left: 6px solid var(--success); background-image: radial-gradient(circle at top right, rgba(16, 185, 129, 0.05), transparent 60%);">
        <div class="metric-info">
            <h3 style="color: var(--success);"><i class="fa-solid fa-circle-check"></i> Paid Violations Fine</h3>
            <span class="number" style="color: var(--success);">₹<?php echo number_format($total_paid, 2); ?></span>
            <p style="color: var(--text-muted); font-size: 12px; margin-top: 4px;">Total revenue successfully cleared</p>
        </div>
    </div>
    
    <!-- Outstanding Receivables -->
    <div class="metric-card" style="border-left: 6px solid var(--danger); background-image: radial-gradient(circle at top right, rgba(244, 63, 94, 0.05), transparent 60%);">
        <div class="metric-info">
            <h3 style="color: var(--danger);"><i class="fa-solid fa-circle-exclamation"></i> Pending Violations Fine</h3>
            <span class="number" style="color: var(--danger);">₹<?php echo number_format($total_pending, 2); ?></span>
            <p style="color: var(--text-muted); font-size: 12px; margin-top: 4px;">Awaiting payment from vehicle owners</p>
        </div>
    </div>
</div>

<!-- Layout Single Column for Recent Activities -->
<div class="layout-single">
    <div class="card">
        <div class="card-header">
            <h3><i class="fa-solid fa-clock-rotate-left"></i> Recently Issued Challans</h3>
            <a href="challans.php" class="btn btn-secondary" style="padding: 8px 16px; font-size: 13px;">
                <i class="fa-solid fa-arrow-right"></i> View All Challans
            </a>
        </div>
        
        <div class="table-container">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Challan ID</th>
                        <th>Date Issued</th>
                        <th>Vehicle Plate No.</th>
                        <th>Violation Type</th>
                        <th>Fine Amount</th>
                        <th>Officer in Charge</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetch recent challans using JOIN operation (relational model demonstration)
                    $recent_challans_query = "
                        SELECT C.Challan_ID, C.Date, V.Vehicle_No, V.Type AS Violation_Type, V.Fine_Amount, V.Status, O.Name AS Officer_Name
                        FROM CHALLAN C
                        JOIN VIOLATION V ON C.Violation_ID = V.Violation_ID
                        JOIN OFFICER O ON C.Officer_ID = O.Officer_ID
                        ORDER BY C.Date DESC, C.Challan_ID DESC
                        LIMIT 5
                    ";
                    $recent_result = $conn->query($recent_challans_query);

                    if ($recent_result && $recent_result->num_rows > 0) {
                        while ($row = $recent_result->fetch_assoc()) {
                            $status_class = ($row['Status'] == 'Paid') ? 'paid' : 'pending';
                            echo "<tr>";
                            echo "<td><strong>#CH-" . $row['Challan_ID'] . "</strong></td>";
                            echo "<td>" . date('d M Y', strtotime($row['Date'])) . "</td>";
                            echo "<td><code style='background-color: var(--bg-main); padding: 4px 8px; border-radius: var(--radius-sm); color: var(--primary); font-weight: 600; border: 1px solid var(--border-color);'>" . htmlspecialchars($row['Vehicle_No']) . "</code></td>";
                            echo "<td>" . htmlspecialchars($row['Violation_Type']) . "</td>";
                            echo "<td>₹" . number_format($row['Fine_Amount'], 2) . "</td>";
                            echo "<td>" . htmlspecialchars($row['Officer_Name']) . "</td>";
                            echo "<td><span class='badge " . $status_class . "'>" . htmlspecialchars($row['Status']) . "</span></td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7' class='no-data'><i class='fa-solid fa-triangle-exclamation'></i>No Challans found in the database. Add some data to begin.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
// 4. Include shared footer
require_once 'includes/footer.php';
?>
