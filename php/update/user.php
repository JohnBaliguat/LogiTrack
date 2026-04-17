<?php
include "../config/config.php";

// Function to validate input
function validate($data){
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Handle Update User
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update-user') {
    $response = ['success' => false, 'message' => ''];
    
    $user_id = validate($_POST['userId'] ?? '');
    $fname = validate($_POST['firstName'] ?? '');
    $lname = validate($_POST['lastName'] ?? '');
    $mname = validate($_POST['middleName'] ?? '');
    $email = validate($_POST['email'] ?? '');
    $user_type = validate($_POST['role'] ?? '');
    $accountStat = validate($_POST['status'] ?? 'Active');
    
    if(empty($user_id) || empty($fname) || empty($lname) || empty($email)) {
        $response['message'] = "All fields are required";
    } else {
        $update_sql = "UPDATE `user` SET `user_fname` = ?, `user_lname` = ?, `user_mname` = ?, `user_email` = ?, `user_type` = ?, `user_accountStat` = ? WHERE `user_id` = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ssssssi", $fname, $lname, $mname, $email, $user_type, $accountStat, $user_id);
        
        if($update_stmt->execute()) {
            $response['success'] = true;
            $response['message'] = "User updated successfully";
        } else {
            $response['message'] = "Error updating user: " . $update_stmt->error;
        }
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}
?>
