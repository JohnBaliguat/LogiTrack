<?php
include "../config/config.php";

// Function to validate input
function validate($data){
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Handle Delete User
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete-user') {
    $response = ['success' => false, 'message' => ''];
    
    $user_id = validate($_POST['userId'] ?? '');
    
    if(empty($user_id)) {
        $response['message'] = "User ID is required";
    } else {
        $delete_sql = "DELETE FROM `user` WHERE `user_id` = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("i", $user_id);
        
        if($delete_stmt->execute()) {
            $response['success'] = true;
            $response['message'] = "User deleted successfully";
        } else {
            $response['message'] = "Error deleting user: " . $delete_stmt->error;
        }
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}
?>
