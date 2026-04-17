<?php
session_start();
include "php/config/config.php";

if (isset($_POST["login-btn"])) {
    function validate($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    $uname = validate($_POST["uname"]);
    $pass = validate($_POST["pass"]);

    if (empty($uname)) {
        header("Location: login?error=Username is required");
        exit();
    } elseif (empty($pass)) {
        header("Location: login?error=Password is required");
        exit();
    } else {
        // Check user table
        $sql =
            "SELECT `user_id`, `user_name`, `user_fname`, `user_lname`, `user_mname`, `user_email`, `user_pass`, `user_type`, `user_image`, `user_accountStat`, `user_code` FROM `user` WHERE user_name = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $uname);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            $hashedPassword = $row["user_pass"];

            if ($row["user_accountStat"] === "Inactive") {
                header(
                    "Location: login?error=Account is inactive. Please contact administrator",
                );
                exit();
            }

            if (password_verify($pass, $hashedPassword)) {
                $_SESSION["user_id"] = $row["user_id"];
                $_SESSION["user_type"] = $row["user_type"];
                $_SESSION["user_name"] = $row["user_name"];
                $_SESSION["user_email"] = $row["user_email"];
                $_SESSION["user_image"] = $row["user_image"];

                header("Location: dashboard");
                exit();
            } else {
                header("Location: login?error=Incorrect username or password");
                exit();
            }
        } else {
            header("Location: login?error=Incorrect username or password");
            exit();
        }
    }
}
?>
