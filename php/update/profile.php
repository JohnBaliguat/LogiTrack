<?php
include "../config/config.php";
session_start();

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not authenticated']);
    exit();
}

// Function to validate input
function validate($data){
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Get current user profile
if($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get-profile') {
    $user_id = $_SESSION['user_id'];
    
    $sql = "SELECT `user_id`, `user_fname`, `user_lname`, `user_email`, `user_type`, `user_accountStat`, `user_code` FROM `user` WHERE `user_id` = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        echo json_encode([
            'success' => true,
            'user' => $user
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'User not found']);
    }
    exit();
}

// Update personal info
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update-personal') {
    $response = ['success' => false, 'message' => ''];
    
    $user_id = $_SESSION['user_id'];
    $fname = validate($_POST['firstName'] ?? '');
    $lname = validate($_POST['lastName'] ?? '');
    $email = validate($_POST['email'] ?? '');
    
    if(empty($fname) || empty($lname) || empty($email)) {
        $response['message'] = "First name, last name, and email are required";
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }
    
    // Check if email is already used by another user
    $check_sql = "SELECT user_id FROM `user` WHERE user_email = ? AND user_id != ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("si", $email, $user_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if($check_result->num_rows > 0) {
        $response['message'] = "Email is already in use";
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }
    
    // Update user profile
    $update_sql = "UPDATE `user` SET `user_fname` = ?, `user_lname` = ?, `user_email` = ? WHERE `user_id` = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("sssi", $fname, $lname, $email, $user_id);
    
    if($update_stmt->execute()) {
        // Update session with new values
        $_SESSION['user_name'] = $fname . ' ' . $lname;
        $_SESSION['user_email'] = $email;
        
        $response['success'] = true;
        $response['message'] = "Personal information updated successfully";
    } else {
        $response['message'] = "Error updating profile: " . $update_stmt->error;
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}
?>
