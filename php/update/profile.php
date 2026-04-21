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
    
    $sql = "SELECT `user_id`, `user_idNumber`, `user_name`, `user_fname`, `user_lname`, `user_mname`, `user_email`, `user_type`, `user_image`, `user_accountStat`, `user_code`, `user_address`, `user_city`, `user_country`, `user_bio`, `user_contact` FROM `user` WHERE `user_id` = ?";
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
    $mname = validate($_POST['middleName'] ?? '');
    $username = validate($_POST['username'] ?? '');
    $idNumber = validate($_POST['idNumber'] ?? '');
    $email = validate($_POST['email'] ?? '');
    $contact = validate($_POST['contact'] ?? '');
    $address = validate($_POST['address'] ?? '');
    $city = validate($_POST['city'] ?? '');
    $country = validate($_POST['country'] ?? '');
    $bio = validate($_POST['bio'] ?? '');
    
    if(empty($fname) || empty($lname) || empty($username) || empty($idNumber) || empty($email)) {
        $response['message'] = "First name, last name, username, ID number, and email are required";
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }
    
    // Check if email, username, or id number is already used by another user
    $check_sql = "SELECT user_id FROM `user` WHERE (user_email = ? OR user_name = ? OR user_idNumber = ?) AND user_id != ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("sssi", $email, $username, $idNumber, $user_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if($check_result->num_rows > 0) {
        $response['message'] = "Email, username, or ID number is already in use";
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }
    
    // Update user profile
    $update_sql = "UPDATE `user` SET `user_idNumber` = ?, `user_name` = ?, `user_fname` = ?, `user_lname` = ?, `user_mname` = ?, `user_email` = ?, `user_address` = ?, `user_city` = ?, `user_country` = ?, `user_bio` = ?, `user_contact` = ? WHERE `user_id` = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("sssssssssssi", $idNumber, $username, $fname, $lname, $mname, $email, $address, $city, $country, $bio, $contact, $user_id);
    
    if($update_stmt->execute()) {
        // Update session with new values
        $_SESSION['user_name'] = trim($fname . ' ' . $lname);
        $_SESSION['user_email'] = $email;
        $_SESSION['user_idNumber'] = $idNumber;
        
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
