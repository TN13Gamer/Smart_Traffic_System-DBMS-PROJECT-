<?php
/**
 * Smart Traffic Violation Monitoring System
 * Owner Edit Module (owner_edit.php)
 * 
 * Demonstrates UPDATE operations for the OWNER entity in standard SQL relational DBMS.
 */

// 1. Include database connection
require_once 'config/db_connect.php';

$alert_message = "";
$alert_type = "";

// 2. Validate URL parameter ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: owners.php");
    exit();
}

$owner_id = intval($_GET['id']);

// 3. Handle POST Request (Update Owner)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_owner'])) {
    $name = mysqli_real_escape_string($conn, trim($_POST['name']));
    $license_no = mysqli_real_escape_string($conn, trim($_POST['license_no']));
    
    if (empty($name) || empty($license_no)) {
        $alert_message = "All fields are required!";
        $alert_type = "error";
    } else {
        // SQL query to update owner details
        $update_query = "UPDATE OWNER SET Name = '$name', License_No = '$license_no' WHERE Owner_ID = $owner_id";
        
        if ($conn->query($update_query)) {
            header("Location: owners.php?msg=updated");
            exit();
        } else {
            // Check duplicate License No
            if ($conn->errno == 1062) {
                $alert_message = "License Number already exists in the system!";
            } else {
                $alert_message = "Error updating owner: " . $conn->error;
            }
            $alert_type = "error";
        }
    }
}

// 4. Fetch current owner details to pre-fill the form
$fetch_query = "SELECT * FROM OWNER WHERE Owner_ID = $owner_id";
$fetch_result = $conn->query($fetch_query);

if (!$fetch_result || $fetch_result->num_rows == 0) {
    header("Location: owners.php");
    exit();
}

$owner_data = $fetch_result->fetch_assoc();

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
            <h3><i class="fa-solid fa-user-pen"></i> Edit Owner Details</h3>
            <a href="owners.php" class="btn btn-secondary" style="padding: 8px 16px; font-size: 13px;">
                <i class="fa-solid fa-arrow-left-long"></i> Back to List
            </a>
        </div>
        
        <form action="owner_edit.php?id=<?php echo $owner_id; ?>" method="POST">
            <!-- Disabled ID View (Informational for DBMS projects) -->
            <div class="form-group">
                <label>System Owner ID (Primary Key)</label>
                <input type="text" class="form-control" value="#OWN-<?php echo $owner_id; ?>" disabled style="opacity: 0.7; background-color: var(--bg-main);">
            </div>

            <!-- Form Input: Owner Name -->
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($owner_data['Name']); ?>" required>
            </div>
            
            <!-- Form Input: Driving License Number -->
            <div class="form-group">
                <label for="license_no">Driving License Number</label>
                <input type="text" id="license_no" name="license_no" class="form-control" value="<?php echo htmlspecialchars($owner_data['License_No']); ?>" style="text-transform: uppercase;" required>
            </div>
            
            <!-- Form Action Buttons -->
            <div class="actions" style="margin-top: 24px; gap: 12px; width: 100%;">
                <a href="owners.php" class="btn btn-secondary" style="flex: 1;">Cancel</a>
                <button type="submit" name="update_owner" class="btn btn-primary" style="flex: 2;">
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
