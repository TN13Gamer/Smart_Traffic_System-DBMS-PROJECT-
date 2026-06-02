<?php
/**
 * Smart Traffic Violation Monitoring System
 * Officer Edit Module (officer_edit.php)
 * 
 * Demonstrates UPDATE operations for the OFFICER entity in standard SQL relational DBMS.
 */

// 1. Include database connection
require_once 'config/db_connect.php';

$alert_message = "";
$alert_type = "";

// 2. Validate URL parameter ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: officers.php");
    exit();
}

$officer_id = intval($_GET['id']);

// 3. Handle POST Request (Update Officer)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_officer'])) {
    $name = mysqli_real_escape_string($conn, trim($_POST['name']));
    $phone = mysqli_real_escape_string($conn, trim($_POST['phone']));
    
    if (empty($name) || empty($phone)) {
        $alert_message = "All fields are required!";
        $alert_type = "error";
    } else {
        // SQL query to update officer details
        $update_query = "UPDATE OFFICER SET Name = '$name', Phone = '$phone' WHERE Officer_ID = $officer_id";
        
        if ($conn->query($update_query)) {
            header("Location: officers.php?msg=updated");
            exit();
        } else {
            $alert_message = "Error updating officer details: " . $conn->error;
            $alert_type = "error";
        }
    }
}

// 4. Fetch current officer details to pre-fill the form
$fetch_query = "SELECT * FROM OFFICER WHERE Officer_ID = $officer_id";
$fetch_result = $conn->query($fetch_query);

if (!$fetch_result || $fetch_result->num_rows == 0) {
    header("Location: officers.php");
    exit();
}

$officer_data = $fetch_result->fetch_assoc();

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
            <h3><i class="fa-solid fa-user-gear"></i> Edit Officer Profile</h3>
            <a href="officers.php" class="btn btn-secondary" style="padding: 8px 16px; font-size: 13px;">
                <i class="fa-solid fa-arrow-left-long"></i> Back to List
            </a>
        </div>
        
        <form action="officer_edit.php?id=<?php echo $officer_id; ?>" method="POST">
            <!-- Disabled ID View (Informational for DBMS projects) -->
            <div class="form-group">
                <label>System Officer ID (Primary Key)</label>
                <input type="text" class="form-control" value="#OFF-<?php echo $officer_id; ?>" disabled style="opacity: 0.7; background-color: var(--bg-main);">
            </div>

            <!-- Form Input: Officer Name -->
            <div class="form-group">
                <label for="name">Officer Full Name</label>
                <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($officer_data['Name']); ?>" required>
            </div>
            
            <!-- Form Input: Officer Contact Phone -->
            <div class="form-group">
                <label for="phone">Contact Phone Number</label>
                <input type="tel" id="phone" name="phone" class="form-control" value="<?php echo htmlspecialchars($officer_data['Phone']); ?>" pattern="[0-9]{10,15}" title="Please enter a valid phone number (10 to 15 digits)." required>
            </div>
            
            <!-- Form Action Buttons -->
            <div class="actions" style="margin-top: 24px; gap: 12px; width: 100%;">
                <a href="officers.php" class="btn btn-secondary" style="flex: 1;">Cancel</a>
                <button type="submit" name="update_officer" class="btn btn-primary" style="flex: 2;">
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
