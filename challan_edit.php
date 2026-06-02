<?php
/**
 * Smart Traffic Violation Monitoring System
 * Challan Edit Module (challan_edit.php)
 * 
 * Demonstrates UPDATE operations for the CHALLAN entity.
 * Supports updating date details and re-mapping both Foreign Keys (Violation_ID and Officer_ID).
 */

// 1. Include database connection
require_once 'config/db_connect.php';

$alert_message = "";
$alert_type = "";

// 2. Validate URL parameter ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: challans.php");
    exit();
}

$challan_id = intval($_GET['id']);

// 3. Handle POST Request (Update Challan)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_challan'])) {
    $violation_id = intval($_POST['violation_id']);
    $officer_id = intval($_POST['officer_id']);
    $date = mysqli_real_escape_string($conn, $_POST['date']);
    
    if (empty($violation_id) || empty($officer_id) || empty($date)) {
        $alert_message = "All fields are required!";
        $alert_type = "error";
    } else {
        // SQL query to update challan details
        $update_query = "
            UPDATE CHALLAN 
            SET Violation_ID = $violation_id, Officer_ID = $officer_id, Date = '$date' 
            WHERE Challan_ID = $challan_id
        ";
        
        if ($conn->query($update_query)) {
            header("Location: challans.php?msg=updated");
            exit();
        } else {
            $alert_message = "Error updating challan: " . $conn->error;
            $alert_type = "error";
        }
    }
}

// 4. Fetch current challan details to pre-fill the form
$fetch_query = "SELECT * FROM CHALLAN WHERE Challan_ID = $challan_id";
$fetch_result = $conn->query($fetch_query);

if (!$fetch_result || $fetch_result->num_rows == 0) {
    header("Location: challans.php");
    exit();
}

$challan_data = $fetch_result->fetch_assoc();

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
            <h3><i class="fa-solid fa-pen-to-square"></i> Edit Challan Ticket</h3>
            <a href="challans.php" class="btn btn-secondary" style="padding: 8px 16px; font-size: 13px;">
                <i class="fa-solid fa-arrow-left-long"></i> Back to List
            </a>
        </div>
        
        <form action="challan_edit.php?id=<?php echo $challan_id; ?>" method="POST">
            <!-- Disabled ID View (Informational for DBMS projects) -->
            <div class="form-group">
                <label>System Challan ID (Primary Key)</label>
                <input type="text" class="form-control" value="#CH-<?php echo $challan_id; ?>" disabled style="opacity: 0.7; background-color: var(--bg-main);">
            </div>
            
            <!-- Associated Violation (FK selection) -->
            <div class="form-group">
                <label for="violation_id">Associated Traffic Violation (Foreign Key)</label>
                <select id="violation_id" name="violation_id" class="form-control" required>
                    <?php
                    // Fetch all violations
                    $violations_query = "SELECT Violation_ID, Vehicle_No, Type, Fine_Amount FROM VIOLATION ORDER BY Violation_ID DESC";
                    $violations_result = $conn->query($violations_query);
                    
                    if ($violations_result && $violations_result->num_rows > 0) {
                        while ($violation = $violations_result->fetch_assoc()) {
                            $selected = ($violation['Violation_ID'] == $challan_data['Violation_ID']) ? 'selected' : '';
                            echo "<option value='" . $violation['Violation_ID'] . "' $selected>" .
                                    "#VIO-" . $violation['Violation_ID'] . " - " . htmlspecialchars($violation['Vehicle_No']) . 
                                    " (" . htmlspecialchars($violation['Type']) . " | ₹" . number_format($violation['Fine_Amount'], 0) . ")" .
                                 "</option>";
                        }
                    }
                    ?>
                </select>
            </div>
            
            <!-- Issuing Officer (FK selection) -->
            <div class="form-group">
                <label for="officer_id">Issuing Officer (Foreign Key)</label>
                <select id="officer_id" name="officer_id" class="form-control" required>
                    <?php
                    // Fetch all officers
                    $officers_query = "SELECT Officer_ID, Name FROM OFFICER ORDER BY Name ASC";
                    $officers_result = $conn->query($officers_query);
                    
                    if ($officers_result && $officers_result->num_rows > 0) {
                        while ($officer = $officers_result->fetch_assoc()) {
                            $selected = ($officer['Officer_ID'] == $challan_data['Officer_ID']) ? 'selected' : '';
                            echo "<option value='" . $officer['Officer_ID'] . "' $selected>" . htmlspecialchars($officer['Name']) . "</option>";
                        }
                    }
                    ?>
                </select>
            </div>
            
            <!-- Challan Issue Date -->
            <div class="form-group">
                <label for="date">Challan Issue Date</label>
                <input type="date" id="date" name="date" class="form-control" value="<?php echo htmlspecialchars($challan_data['Date']); ?>" required>
            </div>
            
            <!-- Form Action Buttons -->
            <div class="actions" style="margin-top: 24px; gap: 12px; width: 100%;">
                <a href="challans.php" class="btn btn-secondary" style="flex: 1;">Cancel</a>
                <button type="submit" name="update_challan" class="btn btn-primary" style="flex: 2;">
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
