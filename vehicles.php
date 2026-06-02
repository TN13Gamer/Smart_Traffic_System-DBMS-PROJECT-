<?php
/**
 * Smart Traffic Violation Monitoring System
 * Vehicle Management Module (vehicles.php)
 * 
 * Demonstrates CRUD operations for the VEHICLE entity.
 * Showcases Foreign Key mapping to the OWNER entity via an HTML dropdown selector.
 */

// 1. Include database connection
require_once 'config/db_connect.php';

$alert_message = "";
$alert_type = "";

// 2. Handle POST Request (Add Vehicle)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_vehicle'])) {
    // Sanitize and format primary key inputs
    $vehicle_no = mysqli_real_escape_string($conn, strtoupper(trim($_POST['vehicle_no'])));
    $owner_id = intval($_POST['owner_id']);
    $model = mysqli_real_escape_string($conn, trim($_POST['model']));
    
    // Basic validation
    if (empty($vehicle_no) || empty($owner_id) || empty($model)) {
        $alert_message = "All fields are required!";
        $alert_type = "error";
    } else {
        // SQL query to insert new vehicle
        $insert_query = "INSERT INTO VEHICLE (Vehicle_No, Owner_ID, Model) VALUES ('$vehicle_no', $owner_id, '$model')";
        
        if ($conn->query($insert_query)) {
            header("Location: vehicles.php?msg=added");
            exit();
        } else {
            // Check for Duplicate Primary Key (Vehicle Number already registered)
            if ($conn->errno == 1062) {
                $alert_message = "This Vehicle Number (License Plate) is already registered in the system!";
            } else {
                $alert_message = "Error registering vehicle: " . $conn->error;
            }
            $alert_type = "error";
        }
    }
}

// 3. Handle GET Request (Delete Vehicle)
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    // Vehicle_No is a string PK, so we escape it
    $vehicle_no = mysqli_real_escape_string($conn, $_GET['id']);
    
    // SQL query to delete vehicle
    $delete_query = "DELETE FROM VEHICLE WHERE Vehicle_No = '$vehicle_no'";
    
    if ($conn->query($delete_query)) {
        header("Location: vehicles.php?msg=deleted");
        exit();
    } else {
        $alert_message = "Could not delete vehicle: " . $conn->error;
        $alert_type = "error";
    }
}

// 4. Handle Success Redirect Notifications
if (isset($_GET['msg'])) {
    if ($_GET['msg'] == 'added') {
        $alert_message = "Vehicle registered successfully!";
        $alert_type = "success";
    } elseif ($_GET['msg'] == 'deleted') {
        $alert_message = "Vehicle and all its violations/challans deleted successfully! (Cascading Applied)";
        $alert_type = "success";
    } elseif ($_GET['msg'] == 'updated') {
        $alert_message = "Vehicle details updated successfully!";
        $alert_type = "success";
    }
}

// 5. Include header
require_once 'includes/header.php';
?>

<!-- Action Notification Alerts -->
<?php if (!empty($alert_message)): ?>
    <div class="alert alert-<?php echo $alert_type; ?>">
        <?php if ($alert_type == 'success'): ?>
            <i class="fa-solid fa-circle-check"></i>
        <?php else: ?>
            <i class="fa-solid fa-circle-exclamation"></i>
        <?php endif; ?>
        <span><?php echo $alert_message; ?></span>
    </div>
<?php endif; ?>

<!-- Dashboard Grid (Split Layout) -->
<div class="layout-grid">
    
    <!-- Left Panel: View Registered Vehicles -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fa-solid fa-car-side"></i> Registered Vehicles List</h3>
        </div>
        
        <div class="table-container">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Vehicle Plate No.</th>
                        <th>Model Details</th>
                        <th>Registered Owner</th>
                        <th style="width: 100px; text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetch vehicles using INNER JOIN to get Owner Name (relational mapping)
                    $select_query = "
                        SELECT V.Vehicle_No, V.Model, O.Owner_ID, O.Name AS Owner_Name 
                        FROM VEHICLE V
                        JOIN OWNER O ON V.Owner_ID = O.Owner_ID
                        ORDER BY V.Vehicle_No ASC
                    ";
                    $result = $conn->query($select_query);

                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td><code style='background-color: var(--bg-main); padding: 4px 8px; border-radius: var(--radius-sm); color: var(--primary); font-weight: 600; border: 1px solid var(--border-color);'>" . htmlspecialchars($row['Vehicle_No']) . "</code></td>";
                            echo "<td>" . htmlspecialchars($row['Model']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['Owner_Name']) . " <span style='font-size: 11px; color: var(--text-muted);'>(#OWN-" . $row['Owner_ID'] . ")</span></td>";
                            echo "<td class='actions' style='justify-content: center;'>
                                    <a href='vehicle_edit.php?id=" . urlencode($row['Vehicle_No']) . "' class='btn-icon edit' title='Edit Vehicle'><i class='fa-solid fa-pen'></i></a>
                                    <a href='vehicles.php?action=delete&id=" . urlencode($row['Vehicle_No']) . "' class='btn-icon delete' title='Delete Vehicle' onclick='return confirm(\"WARNING: Deleting this vehicle will automatically delete all its associated violations and challans! Do you wish to continue?\");'><i class='fa-solid fa-trash'></i></a>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4' class='no-data'><i class='fa-solid fa-car'></i>No registered vehicles found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Right Panel: Add New Vehicle Form -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fa-solid fa-plus"></i> Register New Vehicle</h3>
        </div>
        
        <?php
        // Fetch all owners to populate the select dropdown (FK mapping constraint)
        $owners_query = "SELECT Owner_ID, Name, License_No FROM OWNER ORDER BY Name ASC";
        $owners_result = $conn->query($owners_query);
        
        if ($owners_result && $owners_result->num_rows > 0):
        ?>
            <form action="vehicles.php" method="POST">
                <!-- Form Input: Vehicle Number (PK) -->
                <div class="form-group">
                    <label for="vehicle_no">Vehicle Plate Number (Primary Key)</label>
                    <input type="text" id="vehicle_no" name="vehicle_no" class="form-control" placeholder="e.g. MH-02-CD-5678" style="text-transform: uppercase;" required>
                    <p style="font-size: 11px; color: var(--text-muted); margin-top: 4px;">Use a standard license plate format (e.g. DL-3C-AB-1234).</p>
                </div>
                
                <!-- Form Input: Owner Selector (FK) -->
                <div class="form-group">
                    <label for="owner_id">Select Registered Owner (Foreign Key)</label>
                    <select id="owner_id" name="owner_id" class="form-control" required>
                        <option value="" disabled selected>-- Select Owner --</option>
                        <?php
                        while ($owner = $owners_result->fetch_assoc()) {
                            echo "<option value='" . $owner['Owner_ID'] . "'>" . htmlspecialchars($owner['Name']) . " (" . htmlspecialchars($owner['License_No']) . ")</option>";
                        }
                        ?>
                    </select>
                </div>
                
                <!-- Form Input: Vehicle Model -->
                <div class="form-group">
                    <label for="model">Vehicle Model & Color</label>
                    <input type="text" id="model" name="model" class="form-control" placeholder="e.g. Honda City (White)" required>
                </div>
                
                <!-- Submit Button -->
                <button type="submit" name="add_vehicle" class="btn btn-primary btn-block">
                    <i class="fa-solid fa-car"></i> Register Vehicle
                </button>
            </form>
        <?php else: ?>
            <!-- Helper block prompting the student to add an Owner first to satisfy DB constraint -->
            <div style="text-align: center; padding: 20px; border: 1px dashed var(--border-color); border-radius: var(--radius-md);">
                <i class="fa-solid fa-users" style="font-size: 32px; color: var(--warning); margin-bottom: 12px; display: block;"></i>
                <p style="font-size: 14px; margin-bottom: 16px; color: var(--text-muted);">You must add at least one **Owner** in the database before you can register a vehicle!</p>
                <a href="owners.php" class="btn btn-primary btn-block"><i class="fa-solid fa-user-plus"></i> Go to Owners Module</a>
            </div>
        <?php endif; ?>
    </div>

</div>

<?php
// 6. Include footer
require_once 'includes/footer.php';
?>
