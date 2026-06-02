<?php
/**
 * Smart Traffic Violation Monitoring System
 * Violation Management Module (violations.php)
 * 
 * Demonstrates CRUD operations for the VIOLATION entity.
 * Uses a complex three-table SQL JOIN to link Violations, Vehicles, and Owners together,
 * showing advanced DBMS relational querying.
 */

// 1. Include database connection
require_once 'config/db_connect.php';

$alert_message = "";
$alert_type = "";

// 2. Handle POST Request (Add Violation)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_violation'])) {
    $vehicle_no = mysqli_real_escape_string($conn, $_POST['vehicle_no']);
    $type = mysqli_real_escape_string($conn, trim($_POST['type']));
    $fine_amount = floatval($_POST['fine_amount']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    
    // Basic validation
    if (empty($vehicle_no) || empty($type) || $fine_amount < 0 || empty($status)) {
        $alert_message = "All fields are required and Fine Amount must be positive!";
        $alert_type = "error";
    } else {
        // SQL query to insert new violation
        $insert_query = "INSERT INTO VIOLATION (Vehicle_No, Type, Fine_Amount, Status) VALUES ('$vehicle_no', '$type', $fine_amount, '$status')";
        
        if ($conn->query($insert_query)) {
            header("Location: violations.php?msg=added");
            exit();
        } else {
            $alert_message = "Error recording violation: " . $conn->error;
            $alert_type = "error";
        }
    }
}

// 3. Handle GET Request (Delete Violation)
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $violation_id = intval($_GET['id']);
    
    // SQL query to delete violation
    $delete_query = "DELETE FROM VIOLATION WHERE Violation_ID = $violation_id";
    
    if ($conn->query($delete_query)) {
        header("Location: violations.php?msg=deleted");
        exit();
    } else {
        $alert_message = "Could not delete violation: " . $conn->error;
        $alert_type = "error";
    }
}

// 4. Handle Success Redirect Notifications
if (isset($_GET['msg'])) {
    if ($_GET['msg'] == 'added') {
        $alert_message = "Violation recorded successfully!";
        $alert_type = "success";
    } elseif ($_GET['msg'] == 'deleted') {
        $alert_message = "Violation record and its associated challan deleted successfully! (Cascading Applied)";
        $alert_type = "success";
    } elseif ($_GET['msg'] == 'updated') {
        $alert_message = "Violation details updated successfully!";
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
    
    <!-- Left Panel: View Registered Violations -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fa-solid fa-circle-exclamation"></i> Traffic Violations Log</h3>
        </div>
        
        <div class="table-container">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Violation ID</th>
                        <th>Vehicle Plate</th>
                        <th>Owner</th>
                        <th>Offense Type</th>
                        <th>Fine (₹)</th>
                        <th>Status</th>
                        <th style="width: 100px; text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetch violations using a three-table JOIN to display Owner details
                    $select_query = "
                        SELECT V.Violation_ID, V.Vehicle_No, V.Type, V.Fine_Amount, V.Status, O.Name AS Owner_Name
                        FROM VIOLATION V
                        JOIN VEHICLE VE ON V.Vehicle_No = VE.Vehicle_No
                        JOIN OWNER O ON VE.Owner_ID = O.Owner_ID
                        ORDER BY V.Violation_ID DESC
                    ";
                    $result = $conn->query($select_query);

                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $status_class = ($row['Status'] == 'Paid') ? 'paid' : 'pending';
                            echo "<tr>";
                            echo "<td><strong>#VIO-" . $row['Violation_ID'] . "</strong></td>";
                            echo "<td><code style='background-color: var(--bg-main); padding: 4px 8px; border-radius: var(--radius-sm); color: var(--primary); font-weight: 600; border: 1px solid var(--border-color);'>" . htmlspecialchars($row['Vehicle_No']) . "</code></td>";
                            echo "<td>" . htmlspecialchars($row['Owner_Name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
                            echo "<td>" . number_format($row['Fine_Amount'], 2) . "</td>";
                            echo "<td><span class='badge " . $status_class . "'>" . htmlspecialchars($row['Status']) . "</span></td>";
                            echo "<td class='actions' style='justify-content: center;'>
                                    <a href='violation_edit.php?id=" . $row['Violation_ID'] . "' class='btn-icon edit' title='Edit Violation'><i class='fa-solid fa-pen'></i></a>
                                    <a href='violations.php?action=delete&id=" . $row['Violation_ID'] . "' class='btn-icon delete' title='Delete Violation' onclick='return confirm(\"WARNING: Deleting this violation will automatically delete its associated Challan! Do you wish to continue?\");'><i class='fa-solid fa-trash'></i></a>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7' class='no-data'><i class='fa-solid fa-triangle-exclamation'></i>No recorded violations found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Right Panel: Add New Violation Form -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fa-solid fa-circle-plus"></i> Record New Violation</h3>
        </div>
        
        <?php
        // Fetch registered vehicles to populate the select dropdown (FK validation)
        $vehicles_query = "SELECT Vehicle_No, Model FROM VEHICLE ORDER BY Vehicle_No ASC";
        $vehicles_result = $conn->query($vehicles_query);
        
        if ($vehicles_result && $vehicles_result->num_rows > 0):
        ?>
            <form action="violations.php" method="POST">
                
                <!-- Form Input: Vehicle Selector (FK) -->
                <div class="form-group">
                    <label for="vehicle_no">Offending Vehicle (Foreign Key)</label>
                    <select id="vehicle_no" name="vehicle_no" class="form-control" required>
                        <option value="" disabled selected>-- Select Vehicle --</option>
                        <?php
                        while ($vehicle = $vehicles_result->fetch_assoc()) {
                            echo "<option value='" . $vehicle['Vehicle_No'] . "'>" . htmlspecialchars($vehicle['Vehicle_No']) . " (" . htmlspecialchars($vehicle['Model']) . ")</option>";
                        }
                        ?>
                    </select>
                </div>
                
                <!-- Form Input: Violation Type with Autocomplete HTML Datalist -->
                <div class="form-group">
                    <label for="type">Violation Type / Offense</label>
                    <input type="text" id="type" name="type" class="form-control" placeholder="Select or type violation..." list="common-violations" required>
                    
                    <!-- Smart Datalist representing typical offenses -->
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
                
                <!-- Form Input: Fine Amount (numeric validation) -->
                <div class="form-group">
                    <label for="fine_amount">Fine Amount (₹)</label>
                    <input type="number" id="fine_amount" name="fine_amount" class="form-control" placeholder="e.g. 1000" min="0" step="100" required>
                </div>
                
                <!-- Form Input: Fine Settlement Status -->
                <div class="form-group">
                    <label for="status">Settlement Status</label>
                    <select id="status" name="status" class="form-control" required>
                        <option value="Pending" selected>Pending</option>
                        <option value="Paid">Paid</option>
                    </select>
                </div>
                
                <!-- Submit Button -->
                <button type="submit" name="add_violation" class="btn btn-primary btn-block">
                    <i class="fa-solid fa-bullseye"></i> Record Violation
                </button>
            </form>
        <?php else: ?>
            <!-- Helper block prompting the student to add a vehicle first -->
            <div style="text-align: center; padding: 20px; border: 1px dashed var(--border-color); border-radius: var(--radius-md);">
                <i class="fa-solid fa-car" style="font-size: 32px; color: var(--warning); margin-bottom: 12px; display: block;"></i>
                <p style="font-size: 14px; margin-bottom: 16px; color: var(--text-muted);">You must add at least one **Vehicle** in the database before you can record a traffic violation!</p>
                <a href="vehicles.php" class="btn btn-primary btn-block"><i class="fa-solid fa-car"></i> Go to Vehicles Module</a>
            </div>
        <?php endif; ?>
    </div>

</div>

<?php
// 6. Include footer
require_once 'includes/footer.php';
?>
