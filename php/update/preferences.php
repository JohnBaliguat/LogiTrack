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

// Handle Update Preferences
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update-preferences') {
    $response = ['success' => false, 'message' => ''];
    
    $user_id = $_SESSION['user_id'];
    $emailNotif = isset($_POST['emailNotif']) ? 1 : 0;
    $pushNotif = isset($_POST['pushNotif']) ? 1 : 0;
    $weeklyReport = isset($_POST['weeklyReport']) ? 1 : 0;
    $theme = validate($_POST['theme'] ?? 'light');
    $language = validate($_POST['language'] ?? 'en');
    $timezone = validate($_POST['timezone'] ?? 'est');
    
    // Create or update preferences (check if exists first)
    $check_sql = "SELECT id FROM `user_preferences` WHERE user_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $user_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if($check_result->num_rows > 0) {
        // Update existing preferences
        $update_sql = "UPDATE `user_preferences` SET `email_notifications` = ?, `push_notifications` = ?, `weekly_reports` = ?, `theme` = ?, `language` = ?, `timezone` = ? WHERE `user_id` = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("iiisssi", $emailNotif, $pushNotif, $weeklyReport, $theme, $language, $timezone, $user_id);
        
        if($update_stmt->execute()) {
            $response['success'] = true;
            $response['message'] = "Preferences updated successfully";
        } else {
            $response['message'] = "Error updating preferences: " . $update_stmt->error;
        }
    } else {
        // Insert new preferences
        $insert_sql = "INSERT INTO `user_preferences` (`user_id`, `email_notifications`, `push_notifications`, `weekly_reports`, `theme`, `language`, `timezone`, `created_at`) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("iiissss", $user_id, $emailNotif, $pushNotif, $weeklyReport, $theme, $language, $timezone);
        
        if($insert_stmt->execute()) {
            $response['success'] = true;
            $response['message'] = "Preferences saved successfully";
        } else {
            $response['message'] = "Error saving preferences: " . $insert_stmt->error;
        }
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

// Get user preferences
if($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get-preferences') {
    $user_id = $_SESSION['user_id'];
    
    $sql = "SELECT * FROM `user_preferences` WHERE `user_id` = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows > 0) {
        $preferences = $result->fetch_assoc();
        echo json_encode([
            'success' => true,
            'preferences' => $preferences
        ]);
    } else {
        // Return default preferences
        echo json_encode([
            'success' => true,
            'preferences' => [
                'email_notifications' => 1,
                'push_notifications' => 1,
                'weekly_reports' => 0,
                'theme' => 'light',
                'language' => 'en',
                'timezone' => 'est'
            ]
        ]);
    }
    exit();
}
?>
