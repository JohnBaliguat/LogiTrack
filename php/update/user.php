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
    $username = validate($_POST['username'] ?? '');
    $idNumber = validate($_POST['idNumber'] ?? '');
    $email = validate($_POST['email'] ?? '');
    $user_type = validate($_POST['role'] ?? '');
    $accountStat = validate($_POST['status'] ?? 'Active');
    
    if(empty($user_id) || empty($fname) || empty($lname) || empty($username) || empty($idNumber) || empty($email)) {
        $response['message'] = "All fields are required";
    } else {
        $check_sql = "SELECT user_id FROM `user` WHERE (`user_name` = ? OR `user_idNumber` = ?) AND `user_id` != ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("ssi", $username, $idNumber, $user_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if($check_result->num_rows > 0) {
            $response['message'] = "Username or ID Number already exists";
        } else {
            $update_sql = "UPDATE `user` SET `user_name` = ?, `user_fname` = ?, `user_lname` = ?, `user_mname` = ?, `user_email` = ?, `user_type` = ?, `user_accountStat` = ?, `user_idNumber` = ? WHERE `user_id` = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("ssssssssi", $username, $fname, $lname, $mname, $email, $user_type, $accountStat, $idNumber, $user_id);
            
            if($update_stmt->execute()) {
                $response['success'] = true;
                $response['message'] = "User updated successfully";
            } else {
                $response['message'] = "Error updating user: " . $update_stmt->error;
            }
        }
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}
?>
