<?php
/**
 * Smart Traffic Violation Monitoring System
 * Vehicle Edit Module (vehicle_edit.php)
 * 
 * Demonstrates UPDATE operations for the VEHICLE entity.
 * Keeps the Primary Key (Vehicle_No) disabled/read-only to maintain system stability,
 * while allowing reassignment of Owner_ID (FK) and modification of Model details.
 */

// 1. Include database connection
require_once 'config/db_connect.php';

$alert_message = "";
$alert_type = "";

// 2. Validate URL parameter ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: vehicles.php");
    exit();
}

$vehicle_no = mysqli_real_escape_string($conn, $_GET['id']);

// 3. Handle POST Request (Update Vehicle)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_vehicle'])) {
    $owner_id = intval($_POST['owner_id']);
    $model = mysqli_real_escape_string($conn, trim($_POST['model']));
    
    if (empty($owner_id) || empty($model)) {
        $alert_message = "All fields are required!";
        $alert_type = "error";
    } else {
        // SQL query to update vehicle details
        $update_query = "UPDATE VEHICLE SET Owner_ID = $owner_id, Model = '$model' WHERE Vehicle_No = '$vehicle_no'";
        
        if ($conn->query($update_query)) {
            header("Location: vehicles.php?msg=updated");
            exit();
        } else {
            $alert_message = "Error updating vehicle: " . $conn->error;
            $alert_type = "error";
        }
    }
}

// 4. Fetch current vehicle details to pre-fill the form
$fetch_query = "SELECT * FROM VEHICLE WHERE Vehicle_No = '$vehicle_no'";
$fetch_result = $conn->query($fetch_query);

if (!$fetch_result || $fetch_result->num_rows == 0) {
    header("Location: vehicles.php");
    exit();
}

$vehicle_data = $fetch_result->fetch_assoc();

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
            <h3><i class="fa-solid fa-car-rear"></i> Edit Vehicle Details</h3>
            <a href="vehicles.php" class="btn btn-secondary" style="padding: 8px 16px; font-size: 13px;">
                <i class="fa-solid fa-arrow-left-long"></i> Back to List
            </a>
        </div>
        
        <form action="vehicle_edit.php?id=<?php echo urlencode($vehicle_no); ?>" method="POST">
            <!-- Read-Only Primary Key Display -->
            <div class="form-group">
                <label>Vehicle Plate Number (Primary Key - Read Only)</label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($vehicle_no); ?>" disabled style="opacity: 0.7; background-color: var(--bg-main); font-weight: 700; color: var(--primary);">
            </div>
            
            <!-- Owner Selector (FK editing) -->
            <div class="form-group">
                <label for="owner_id">Registered Owner (Foreign Key)</label>
                <select id="owner_id" name="owner_id" class="form-control" required>
                    <?php
                    // Fetch all owners to populate the list
                    $owners_query = "SELECT Owner_ID, Name, License_No FROM OWNER ORDER BY Name ASC";
                    $owners_result = $conn->query($owners_query);
                    
                    if ($owners_result && $owners_result->num_rows > 0) {
                        while ($owner = $owners_result->fetch_assoc()) {
                            $selected = ($owner['Owner_ID'] == $vehicle_data['Owner_ID']) ? 'selected' : '';
                            echo "<option value='" . $owner['Owner_ID'] . "' $selected>" . htmlspecialchars($owner['Name']) . " (" . htmlspecialchars($owner['License_No']) . ")</option>";
                        }
                    }
                    ?>
                </select>
            </div>
            
            <!-- Vehicle Model input -->
            <div class="form-group">
                <label for="model">Vehicle Model & Color</label>
                <input type="text" id="model" name="model" class="form-control" value="<?php echo htmlspecialchars($vehicle_data['Model']); ?>" required>
            </div>
            
            <!-- Form Action Buttons -->
            <div class="actions" style="margin-top: 24px; gap: 12px; width: 100%;">
                <a href="vehicles.php" class="btn btn-secondary" style="flex: 1;">Cancel</a>
                <button type="submit" name="update_vehicle" class="btn btn-primary" style="flex: 2;">
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
