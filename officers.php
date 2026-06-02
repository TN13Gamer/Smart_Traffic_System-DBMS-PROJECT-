<?php
/**
 * Smart Traffic Violation Monitoring System
 * Officer Management Module (officers.php)
 * 
 * Demonstrates CRUD operations (Create, Read, Delete) for the OFFICER entity.
 * Uses a clean Split Screen Layout: List view on the left, Add form on the right.
 */

// 1. Include database connection
require_once 'config/db_connect.php';

$alert_message = "";
$alert_type = "";

// 2. Handle POST Request (Add Officer)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_officer'])) {
    $name = mysqli_real_escape_string($conn, trim($_POST['name']));
    $phone = mysqli_real_escape_string($conn, trim($_POST['phone']));
    
    // Basic validation
    if (empty($name) || empty($phone)) {
        $alert_message = "All fields are required!";
        $alert_type = "error";
    } else {
        // SQL query to insert new officer
        $insert_query = "INSERT INTO OFFICER (Name, Phone) VALUES ('$name', '$phone')";
        
        if ($conn->query($insert_query)) {
            header("Location: officers.php?msg=added");
            exit();
        } else {
            $alert_message = "Error registering officer: " . $conn->error;
            $alert_type = "error";
        }
    }
}

// 3. Handle GET Request (Delete Officer)
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $officer_id = intval($_GET['id']);
    
    // SQL query to delete officer
    $delete_query = "DELETE FROM OFFICER WHERE Officer_ID = $officer_id";
    
    if ($conn->query($delete_query)) {
        header("Location: officers.php?msg=deleted");
        exit();
    } else {
        $alert_message = "Could not delete officer: " . $conn->error;
        $alert_type = "error";
    }
}

// 4. Handle Success Redirect Notifications
if (isset($_GET['msg'])) {
    if ($_GET['msg'] == 'added') {
        $alert_message = "Officer registered successfully!";
        $alert_type = "success";
    } elseif ($_GET['msg'] == 'deleted') {
        $alert_message = "Officer and all their issued challans deleted successfully! (Cascading Applied)";
        $alert_type = "success";
    } elseif ($_GET['msg'] == 'updated') {
        $alert_message = "Officer information updated successfully!";
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
    
    <!-- Left Panel: View Registered Officers -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fa-solid fa-shield-halved"></i> Active Duty Officers List</h3>
        </div>
        
        <div class="table-container">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Officer ID</th>
                        <th>Officer Name</th>
                        <th>Contact Number</th>
                        <th style="width: 100px; text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetch all officers sorted by descending ID
                    $select_query = "SELECT * FROM OFFICER ORDER BY Officer_ID DESC";
                    $result = $conn->query($select_query);

                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td><strong>#OFF-" . $row['Officer_ID'] . "</strong></td>";
                            echo "<td>" . htmlspecialchars($row['Name']) . "</td>";
                            echo "<td><i class='fa-solid fa-phone' style='color: var(--text-muted); font-size: 12px; margin-right: 6px;'></i>" . htmlspecialchars($row['Phone']) . "</td>";
                            echo "<td class='actions' style='justify-content: center;'>
                                    <a href='officer_edit.php?id=" . $row['Officer_ID'] . "' class='btn-icon edit' title='Edit Officer'><i class='fa-solid fa-pen'></i></a>
                                    <a href='officers.php?action=delete&id=" . $row['Officer_ID'] . "' class='btn-icon delete' title='Delete Officer' onclick='return confirm(\"WARNING: Deleting this officer will automatically delete all challans issued by them! Do you wish to continue?\");'><i class='fa-solid fa-trash'></i></a>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4' class='no-data'><i class='fa-solid fa-user-shield'></i>No registered officers found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Right Panel: Add New Officer Form -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fa-solid fa-user-plus"></i> Register New Officer</h3>
        </div>
        
        <form action="officers.php" method="POST">
            <!-- Form Input: Officer Name -->
            <div class="form-group">
                <label for="name">Officer Full Name</label>
                <input type="text" id="name" name="name" class="form-control" placeholder="e.g. Inspector Suresh Patil" required>
            </div>
            
            <!-- Form Input: Officer Contact Phone -->
            <div class="form-group">
                <label for="phone">Contact Phone Number</label>
                <input type="tel" id="phone" name="phone" class="form-control" placeholder="e.g. 9876543210" pattern="[0-9]{10,15}" title="Please enter a valid phone number (10 to 15 digits)." required>
            </div>
            
            <!-- Submit Button -->
            <button type="submit" name="add_officer" class="btn btn-primary btn-block">
                <i class="fa-solid fa-user-plus"></i> Register Officer
            </button>
        </form>
    </div>

</div>

<?php
// 6. Include footer
require_once 'includes/footer.php';
?>
