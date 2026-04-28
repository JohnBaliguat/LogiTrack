<?php

function setUserSession(array $user): void
{
    $_SESSION["user_id"] = $user["user_id"];
    $_SESSION["user_idNumber"] = $user["user_idNumber"] ?? null;
    $_SESSION["user_type"] = $user["user_type"];
    $_SESSION["user_name"] = $user["user_name"];
    $_SESSION["user_email"] = $user["user_email"];
    $_SESSION["user_image"] = $user["user_image"];
}

