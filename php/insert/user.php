<?php
include "../config/config.php";

// Function to validate input
function validate($data){
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Handle Insert User
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add-user') {
    $response = ['success' => false, 'message' => ''];
    
    $fname = validate($_POST['firstName'] ?? '');
    $lname = validate($_POST['lastName'] ?? '');
    $mname = validate($_POST['middleName'] ?? '');
    $email = validate($_POST['email'] ?? '');
    $username = validate($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';
    $user_type = validate($_POST['role'] ?? '');
    $accountStat = validate($_POST['status'] ?? 'Active');
    $user_code = 'USR' . rand(100000, 999999);
    
    if(empty($fname) || empty($lname) || empty($email) || empty($username) || empty($password)) {
        $response['message'] = "All fields are required";
    } elseif($password !== $confirmPassword) {
        $response['message'] = "Passwords do not match";
    } else {
        // Check if user already exists
        $check_sql = "SELECT user_id FROM `user` WHERE user_name = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $username);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if($check_result->num_rows > 0) {
            $response['message'] = "Username already exists";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $insert_sql = "INSERT INTO `user` (`user_name`, `user_fname`, `user_lname`, `user_mname`, `user_email`, `user_pass`, `user_type`, `user_accountStat`, `user_code`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param("sssssssss", $username, $fname, $lname, $mname, $email, $hashedPassword, $user_type, $accountStat, $user_code);
            
            if($insert_stmt->execute()) {
                $response['success'] = true;
                $response['message'] = "User added successfully";
            } else {
                $response['message'] = "Error adding user: " . $insert_stmt->error;
            }
        }
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}
?>
