<?php
/**
 * Smart Traffic Violation Monitoring System
 * Violation Edit Module (violation_edit.php)
 * 
 * Demonstrates UPDATE operations for the VIOLATION entity.
 * Supports updating fine status (Paid/Pending), fine amount, and re-linking vehicles.
 */

// 1. Include database connection
require_once 'config/db_connect.php';

$alert_message = "";
$alert_type = "";

// 2. Validate URL parameter ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: violations.php");
    exit();
}

$violation_id = intval($_GET['id']);

// 3. Handle POST Request (Update Violation)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_violation'])) {
    $vehicle_no = mysqli_real_escape_string($conn, $_POST['vehicle_no']);
    $type = mysqli_real_escape_string($conn, trim($_POST['type']));
    $fine_amount = floatval($_POST['fine_amount']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    
    if (empty($vehicle_no) || empty($type) || $fine_amount < 0 || empty($status)) {
        $alert_message = "All fields are required and Fine Amount must be positive!";
        $alert_type = "error";
    } else {
        // SQL query to update violation details
        $update_query = "
            UPDATE VIOLATION 
            SET Vehicle_No = '$vehicle_no', Type = '$type', Fine_Amount = $fine_amount, Status = '$status' 
            WHERE Violation_ID = $violation_id
        ";
        
        if ($conn->query($update_query)) {
            header("Location: violations.php?msg=updated");
            exit();
        } else {
            $alert_message = "Error updating violation: " . $conn->error;
            $alert_type = "error";
        }
    }
}

// 4. Fetch current violation details to pre-fill the form
$fetch_query = "SELECT * FROM VIOLATION WHERE Violation_ID = $violation_id";
$fetch_result = $conn->query($fetch_query);

if (!$fetch_result || $fetch_result->num_rows == 0) {
    header("Location: violations.php");
    exit();
}

$violation_data = $fetch_result->fetch_assoc();

// 5. Include header
require_once 'includes/header.php';
?>

<!-- Action Notification Alerts -->
<?php if (!empty($alert_message)): ?>
    <div class="alert alert-<?php echo $alert_type; ?>">
        <i class="fa-solid fa-circle-exclamation"></i>
        <span><?php echo $alert_message; ?></span>
    </div>
<?php endif; ?>

<!-- Centered Edit Panel -->
<div class="layout-single" style="max-width: 600px; margin: 0 auto;">
    
    <div class="card">
        <div class="card-header">
            <h3><i class="fa-solid fa-pen-to-square"></i> Edit Violation Log</h3>
            <a href="violations.php" class="btn btn-secondary" style="padding: 8px 16px; font-size: 13px;">
                <i class="fa-solid fa-arrow-left-long"></i> Back to List
            </a>
        </div>
        
        <form action="violation_edit.php?id=<?php echo $violation_id; ?>" method="POST">
            <!-- Disabled ID View (Informational for DBMS projects) -->
            <div class="form-group">
                <label>System Violation ID (Primary Key)</label>
                <input type="text" class="form-control" value="#VIO-<?php echo $violation_id; ?>" disabled style="opacity: 0.7; background-color: var(--bg-main);">
            </div>
            
            <!-- Offending Vehicle (FK selection) -->
            <div class="form-group">
                <label for="vehicle_no">Offending Vehicle (Foreign Key)</label>
                <select id="vehicle_no" name="vehicle_no" class="form-control" required>
                    <?php
                    // Fetch all registered vehicles
                    $vehicles_query = "SELECT Vehicle_No, Model FROM VEHICLE ORDER BY Vehicle_No ASC";
                    $vehicles_result = $conn->query($vehicles_query);
                    
                    if ($vehicles_result && $vehicles_result->num_rows > 0) {
                        while ($vehicle = $vehicles_result->fetch_assoc()) {
                            $selected = ($vehicle['Vehicle_No'] == $violation_data['Vehicle_No']) ? 'selected' : '';
                            echo "<option value='" . $vehicle['Vehicle_No'] . "' $selected>" . htmlspecialchars($vehicle['Vehicle_No']) . " (" . htmlspecialchars($vehicle['Model']) . ")</option>";
                        }
                    }
                    ?>
                </select>
            </div>
            
            <!-- Violation Type with autocomplete datalist -->
            <div class="form-group">
                <label for="type">Violation Type / Offense</label>
                <input type="text" id="type" name="type" class="form-control" value="<?php echo htmlspecialchars($violation_data['Type']); ?>" list="common-violations" required>
                
                <datalist id="common-violations">
                    <option value="Speed Limit Violation">
                    <option value="Red Light Jumping">
                    <option value="No Helmet (Rider)">
                    <option value="Drunken Driving">
                    <option value="Wrong-side Driving">
                    <option value="Triple Riding">
                    <option value="Driving Without License">
                    <option value="Using Mobile While Driving">
                </datalist>
            </div>
            
            <!-- Fine Amount -->
            <div class="form-group">
                <label for="fine_amount">Fine Amount (₹)</label>
                <input type="number" id="fine_amount" name="fine_amount" class="form-control" value="<?php echo htmlspecialchars($violation_data['Fine_Amount']); ?>" min="0" step="100" required>
            </div>
            
            <!-- Settlement Status -->
            <div class="form-group">
                <label for="status">Settlement Status</label>
                <select id="status" name="status" class="form-control" required>
                    <option value="Pending" <?php echo ($violation_data['Status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                    <option value="Paid" <?php echo ($violation_data['Status'] == 'Paid') ? 'selected' : ''; ?>>Paid</option>
                </select>
            </div>
            
            <!-- Form Action Buttons -->
            <div class="actions" style="margin-top: 24px; gap: 12px; width: 100%;">
                <a href="violations.php" class="btn btn-secondary" style="flex: 1;">Cancel</a>
                <button type="submit" name="update_violation" class="btn btn-primary" style="flex: 2;">
                    <i class="fa-solid fa-floppy-disk"></i> Save Changes
                </button>
            </div>
        </form>
    </div>

</div>

<?php
// 6. Include footer
require_once 'includes/footer.php';
?>
