<?php
/**
 * Smart Traffic Violation Monitoring System
 * Owner Management Module (owners.php)
 * 
 * Demonstrates CRUD operations (Create, Read, Delete) for the OWNER entity.
 * Uses a clean Split Screen Layout: List view on the left, Add form on the right.
 */

// 1. Include database connection
require_once 'config/db_connect.php';

$alert_message = "";
$alert_type = "";

// 2. Handle POST Request (Add Owner)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_owner'])) {
    // Sanitize inputs to prevent SQL Injection (excellent standard for college DBMS)
    $name = mysqli_real_escape_string($conn, trim($_POST['name']));
    $license_no = mysqli_real_escape_string($conn, trim($_POST['license_no']));
    
    // Basic validation
    if (empty($name) || empty($license_no)) {
        $alert_message = "All fields are required!";
        $alert_type = "error";
    } else {
        // SQL query to insert new owner
        $insert_query = "INSERT INTO OWNER (Name, License_No) VALUES ('$name', '$license_no')";
        
        if ($conn->query($insert_query)) {
            // Redirect to avoid form resubmission
            header("Location: owners.php?msg=added");
            exit();
        } else {
            // Check for duplicate License No constraint error
            if ($conn->errno == 1062) {
                $alert_message = "License Number already exists in the system!";
            } else {
                $alert_message = "Error: " . $conn->error;
            }
            $alert_type = "error";
        }
    }
}

// 3. Handle GET Request (Delete Owner)
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $owner_id = intval($_GET['id']);
    
    // SQL query to delete owner
    $delete_query = "DELETE FROM OWNER WHERE Owner_ID = $owner_id";
    
    if ($conn->query($delete_query)) {
        header("Location: owners.php?msg=deleted");
        exit();
    } else {
        $alert_message = "Could not delete Owner: " . $conn->error;
        $alert_type = "error";
    }
}

// 4. Handle Success Redirect Notifications
if (isset($_GET['msg'])) {
    if ($_GET['msg'] == 'added') {
        $alert_message = "Owner registered successfully!";
        $alert_type = "success";
    } elseif ($_GET['msg'] == 'deleted') {
        $alert_message = "Owner and all associated vehicles/violations/challans deleted successfully! (Cascading Applied)";
        $alert_type = "success";
    } elseif ($_GET['msg'] == 'updated') {
        $alert_message = "Owner information updated successfully!";
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
    
    <!-- Left Panel: View Registered Owners -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fa-solid fa-address-book"></i> Registered Owners List</h3>
        </div>
        
        <div class="table-container">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Owner ID</th>
                        <th>Name</th>
                        <th>License Number</th>
                        <th style="width: 100px; text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetch all owners sorted by descending ID
                    $select_query = "SELECT * FROM OWNER ORDER BY Owner_ID DESC";
                    $result = $conn->query($select_query);

                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td><strong>#OWN-" . $row['Owner_ID'] . "</strong></td>";
                            echo "<td>" . htmlspecialchars($row['Name']) . "</td>";
                            echo "<td><code style='color: var(--primary); font-weight: 600;'>" . htmlspecialchars($row['License_No']) . "</code></td>";
                            echo "<td class='actions' style='justify-content: center;'>
                                    <a href='owner_edit.php?id=" . $row['Owner_ID'] . "' class='btn-icon edit' title='Edit Owner'><i class='fa-solid fa-pen'></i></a>
                                    <a href='owners.php?action=delete&id=" . $row['Owner_ID'] . "' class='btn-icon delete' title='Delete Owner' onclick='return confirm(\"WARNING: Deleting this owner will automatically delete all their registered vehicles, violations, and challans! Do you wish to continue?\");'><i class='fa-solid fa-trash'></i></a>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4' class='no-data'><i class='fa-solid fa-users'></i>No registered owners found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Right Panel: Add New Owner Form -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fa-solid fa-user-plus"></i> Register New Owner</h3>
        </div>
        
        <form action="owners.php" method="POST">
            <!-- Form Input: Owner Name -->
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" class="form-control" placeholder="e.g. Ramesh Kumar" required>
            </div>
            
            <!-- Form Input: Driving License Number -->
            <div class="form-group">
                <label for="license_no">Driving License Number</label>
                <input type="text" id="license_no" name="license_no" class="form-control" placeholder="e.g. DL123456789012" style="text-transform: uppercase;" required>
            </div>
            
            <!-- Submit Button -->
            <button type="submit" name="add_owner" class="btn btn-primary btn-block">
                <i class="fa-solid fa-user-plus"></i> Register Owner
            </button>
        </form>
    </div>

</div>

<?php
// 6. Include footer
require_once 'includes/footer.php';
?>
