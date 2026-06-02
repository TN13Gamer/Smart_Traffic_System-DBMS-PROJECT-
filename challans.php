<?php
/**
 * Smart Traffic Violation Monitoring System
 * Challan Management Module (challans.php)
 * 
 * Demonstrates CRUD operations for the CHALLAN entity.
 * Employs robust multi-table SQL JOINs to aggregate ticket data on screen,
 * utilizing two distinct Foreign Keys (Violation_ID & Officer_ID).
 */

// 1. Include database connection
require_once 'config/db_connect.php';

$alert_message = "";
$alert_type = "";

// 2. Handle POST Request (Add Challan Ticket)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_challan'])) {
    $violation_id = intval($_POST['violation_id']);
    $officer_id = intval($_POST['officer_id']);
    $date = mysqli_real_escape_string($conn, $_POST['date']);
    
    // Basic validation
    if (empty($violation_id) || empty($officer_id) || empty($date)) {
        $alert_message = "All fields are required!";
        $alert_type = "error";
    } else {
        // SQL query to insert new challan
        $insert_query = "INSERT INTO CHALLAN (Violation_ID, Officer_ID, Date) VALUES ($violation_id, $officer_id, '$date')";
        
        if ($conn->query($insert_query)) {
            header("Location: challans.php?msg=added");
            exit();
        } else {
            $alert_message = "Error generating challan: " . $conn->error;
            $alert_type = "error";
        }
    }
}

// 3. Handle GET Request (Delete Challan Ticket)
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $challan_id = intval($_GET['id']);
    
    // SQL query to delete challan
    $delete_query = "DELETE FROM CHALLAN WHERE Challan_ID = $challan_id";
    
    if ($conn->query($delete_query)) {
        header("Location: challans.php?msg=deleted");
        exit();
    } else {
        $alert_message = "Could not delete challan: " . $conn->error;
        $alert_type = "error";
    }
}

// 4. Handle Success Redirect Notifications
if (isset($_GET['msg'])) {
    if ($_GET['msg'] == 'added') {
        $alert_message = "Challan ticket issued successfully!";
        $alert_type = "success";
    } elseif ($_GET['msg'] == 'deleted') {
        $alert_message = "Challan ticket deleted successfully!";
        $alert_type = "success";
    } elseif ($_GET['msg'] == 'updated') {
        $alert_message = "Challan ticket updated successfully!";
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
    
    <!-- Left Panel: View Registered Challan Tickets -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fa-solid fa-receipt"></i> Issued Challans Audit Log</h3>
        </div>
        
        <div class="table-container">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Challan ID</th>
                        <th>Date Issued</th>
                        <th>Vehicle Plate</th>
                        <th>Violation Type</th>
                        <th>Fine (₹)</th>
                        <th>Officer in Charge</th>
                        <th>Status</th>
                        <th style="width: 100px; text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetch challan records with full details via multi-table JOINs
                    $select_query = "
                        SELECT C.Challan_ID, C.Date, V.Vehicle_No, V.Type AS Violation_Type, V.Fine_Amount, V.Status, O.Name AS Officer_Name
                        FROM CHALLAN C
                        JOIN VIOLATION V ON C.Violation_ID = V.Violation_ID
                        JOIN OFFICER O ON C.Officer_ID = O.Officer_ID
                        ORDER BY C.Challan_ID DESC
                    ";
                    $result = $conn->query($select_query);

                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $status_class = ($row['Status'] == 'Paid') ? 'paid' : 'pending';
                            echo "<tr>";
                            echo "<td><strong>#CH-" . $row['Challan_ID'] . "</strong></td>";
                            echo "<td>" . date('d M Y', strtotime($row['Date'])) . "</td>";
                            echo "<td><code style='background-color: var(--bg-main); padding: 4px 8px; border-radius: var(--radius-sm); color: var(--primary); font-weight: 600; border: 1px solid var(--border-color);'>" . htmlspecialchars($row['Vehicle_No']) . "</code></td>";
                            echo "<td>" . htmlspecialchars($row['Violation_Type']) . "</td>";
                            echo "<td>" . number_format($row['Fine_Amount'], 2) . "</td>";
                            echo "<td>" . htmlspecialchars($row['Officer_Name']) . "</td>";
                            echo "<td><span class='badge " . $status_class . "'>" . htmlspecialchars($row['Status']) . "</span></td>";
                            echo "<td class='actions' style='justify-content: center;'>
                                    <a href='challan_edit.php?id=" . $row['Challan_ID'] . "' class='btn-icon edit' title='Edit Challan'><i class='fa-solid fa-pen'></i></a>
                                    <a href='challans.php?action=delete&id=" . $row['Challan_ID'] . "' class='btn-icon delete' title='Delete Challan' onclick='return confirm(\"Are you sure you want to delete this challan ticket?\");'><i class='fa-solid fa-trash'></i></a>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='8' class='no-data'><i class='fa-solid fa-receipt'></i>No issued challan tickets found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Right Panel: Issue New Challan Ticket Form -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fa-solid fa-file-invoice"></i> Issue New Challan</h3>
        </div>
        
        <?php
        // 1. Fetch all Violations (FK Selection)
        $violations_query = "SELECT Violation_ID, Vehicle_No, Type, Fine_Amount FROM VIOLATION ORDER BY Violation_ID DESC";
        $violations_result = $conn->query($violations_query);
        
        // 2. Fetch all Officers (FK Selection)
        $officers_query = "SELECT Officer_ID, Name FROM OFFICER ORDER BY Name ASC";
        $officers_result = $conn->query($officers_query);
        
        if ($violations_result->num_rows > 0 && $officers_result->num_rows > 0):
        ?>
            <form action="challans.php" method="POST">
                
                <!-- Form Input: Violation Selector (FK) -->
                <div class="form-group">
                    <label for="violation_id">Select Unassigned/Active Violation</label>
                    <select id="violation_id" name="violation_id" class="form-control" required>
                        <option value="" disabled selected>-- Select Violation --</option>
                        <?php
                        while ($violation = $violations_result->fetch_assoc()) {
                            echo "<option value='" . $violation['Violation_ID'] . "'>" .
                                    "#VIO-" . $violation['Violation_ID'] . " - " . htmlspecialchars($violation['Vehicle_No']) . 
                                    " (" . htmlspecialchars($violation['Type']) . " | ₹" . number_format($violation['Fine_Amount'], 0) . ")" .
                                 "</option>";
                        }
                        ?>
                    </select>
                </div>
                
                <!-- Form Input: Officer Selector (FK) -->
                <div class="form-group">
                    <label for="officer_id">Issuing Officer (Foreign Key)</label>
                    <select id="officer_id" name="officer_id" class="form-control" required>
                        <option value="" disabled selected>-- Select Officer --</option>
                        <?php
                        while ($officer = $officers_result->fetch_assoc()) {
                            echo "<option value='" . $officer['Officer_ID'] . "'>" . htmlspecialchars($officer['Name']) . "</option>";
                        }
                        ?>
                    </select>
                </div>
                
                <!-- Form Input: Ticket Issuance Date -->
                <div class="form-group">
                    <label for="date">Challan Issue Date</label>
                    <!-- Defaults automatically to local system date in YYYY-MM-DD format -->
                    <input type="date" id="date" name="date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
                
                <!-- Submit Button -->
                <button type="submit" name="add_challan" class="btn btn-primary btn-block">
                    <i class="fa-solid fa-receipt"></i> Generate Challan Ticket
                </button>
            </form>
        <?php else: ?>
            <!-- Helper block prompting the student to add violations & officers first -->
            <div style="text-align: center; padding: 20px; border: 1px dashed var(--border-color); border-radius: var(--radius-md);">
                <i class="fa-solid fa-file-invoice" style="font-size: 32px; color: var(--warning); margin-bottom: 12px; display: block;"></i>
                <p style="font-size: 13px; margin-bottom: 16px; color: var(--text-muted); line-height: 1.6;">
                    You must have at least one **Traffic Violation** and one active **Officer** registered in the system database to generate a challan ticket!
                </p>
                <div class="actions" style="flex-direction: column; gap: 8px;">
                    <a href="violations.php" class="btn btn-primary btn-block" style="font-size: 12px; padding: 10px;"><i class="fa-solid fa-triangle-exclamation"></i> Go to Violations</a>
                    <a href="officers.php" class="btn btn-secondary btn-block" style="font-size: 12px; padding: 10px;"><i class="fa-solid fa-user-shield"></i> Go to Officers</a>
                </div>
            </div>
        <?php endif; ?>
    </div>

</div>

<?php
// 6. Include footer
require_once 'includes/footer.php';
?>
