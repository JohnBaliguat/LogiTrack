<?php
session_start();

$route = $_GET["route"] ?? "login";
$userType = ucfirst(strtolower((string) ($_SESSION["user_type"] ?? "")));

function isLoggedIn(): bool
{
    return isset($_SESSION["user_id"]);
}

function requireLogin(string $route): void
{
    if (
        $route !== "login" &&
        $route !== "login-handler" &&
        $route !== "microsoft-login" &&
        $route !== "microsoft-callback" &&
        $route !== "logout" &&
        !isLoggedIn()
    ) {
        header("Location: login");
        exit();
    }
}

function redirectByRole(string $route, string $userType): ?string
{
    $dashboardRoutes = [
        "Admin" => "public/Admin/dashboard.php",
        "User" => "public/User/dashboard.php",
        "Billing" => "public/Billing/dashboard.php",
    ];

    $billingRoutes = [
        "Admin" => "public/Admin/billing.php",
        "Billing" => "public/Billing/billing.php",
    ];

    $userSharedRoutes = [
        "entry" => "public/User/abcrv.php",
        "monitoring" => "public/User/monitoring.php",
        "profile" => "public/User/profile.php",
        "cargoTruck" => "public/User/cargoTruck.php",
        "DPC_KDI" => "public/User/DPC_KDI.php",
        "others" => "public/User/others.php",
        "abcrv" => "public/User/abcrv.php",
        "doleRv" => "public/User/doleRv.php",
        "sumiRv" => "public/User/sumiRv.php",
        "tdcRv" => "public/User/tdcRv.php",
        "dryVan" => "public/User/dryVan.php",
    ];

    $billingSharedRoutes = [
        "profile" => "public/Billing/profile.php",
    ];

    if ($route === "dashboard") {
        return $dashboardRoutes[$userType] ?? null;
    }

    if ($route === "user-dashboard") {
        return "public/User/dashboard.php";
    }

    if ($route === "billing-dashboard") {
        return "public/Billing/dashboard.php";
    }

    if ($route === "billing") {
        return $billingRoutes[$userType] ?? null;
    }

    if ($userType === "User" && array_key_exists($route, $userSharedRoutes)) {
        return $userSharedRoutes[$route];
    }

    if (
        $userType === "Billing" &&
        array_key_exists($route, $billingSharedRoutes)
    ) {
        return $billingSharedRoutes[$route];
    }

    return null;
}

function isRouteAllowed(string $route, string $userType): bool
{
    $allowedRoutes = [
        "Admin" => [
            "dashboard",
            "entry",
            "monitoring",
            "payroll",
            "payroll-driver",
            "performance",
            "billing",
            "settings",
            "profile",
            "users",
            "bbhm",
            "cargoTruck",
            "DPC_KDI",
            "others",
            "abcrv",
            "doleRv",
            "sumiRv",
            "tdcRv",
            "dryVan",
            "drivers",
            "records",
        ],
        "User" => [
            "dashboard",
            "user-dashboard",
            "entry",
            "monitoring",
            "profile",
            "cargoTruck",
            "DPC_KDI",
            "others",
            "abcrv",
            "doleRv",
            "sumiRv",
            "tdcRv",
            "dryVan",
        ],
        "Billing" => ["dashboard", "billing-dashboard", "billing", "profile"],
    ];

    return in_array($route, $allowedRoutes[$userType] ?? [], true);
}

requireLogin($route);

// If already logged in and trying to access login, redirect to dashboard
if ($route === "login" && isLoggedIn()) {
    header("Location: dashboard");
    exit();
}

if (
    isLoggedIn() &&
    !in_array(
        $route,
        [
            "login",
            "login-handler",
            "microsoft-login",
            "microsoft-callback",
            "logout",
        ],
        true,
    ) &&
    !isRouteAllowed($route, $userType)
) {
    header("Location: dashboard");
    exit();
}

$routes = [
    "login" => "login.php",
    "login-handler" => "login-php.php",
    "microsoft-login" => "microsoft-login.php",
    "microsoft-callback" => "microsoft-callback.php",
    "logout" => "logout.php",
    "dashboard" => "public/Admin/dashboard.php",
    "entry" => "public/Admin/abcrv.php",
    "monitoring" => "public/Admin/monitoring.php",
    "payroll" => "public/Admin/payroll.php",
    "payroll-driver" => "public/Admin/payroll-driver.php",
    "performance" => "public/Admin/performance.php",
    "billing" => "public/Admin/billing.php",
    "settings" => "public/Admin/settings.php",
    "profile" => "public/Admin/profile.php",
    "users" => "public/Admin/users.php",
    "user-dashboard" => "public/User/dashboard.php",
    "billing-dashboard" => "public/Billing/dashboard.php",
    "bbhm" => "public/Admin/bbhm.php",
    "cargoTruck" => "public/Admin/cargoTruck.php",
    "DPC_KDI" => "public/Admin/DPC_KDI.php",
    "others" => "public/Admin/others.php",
    "abcrv" => "public/Admin/abcrv.php",
    "doleRv" => "public/Admin/doleRv.php",
    "sumiRv" => "public/Admin/sumiRv.php",
    "tdcRv" => "public/Admin/tdcRv.php",
    "dryVan" => "public/Admin/dryVan.php",
    "drivers" => "public/Admin/drivers.php",
    "records" => "public/Admin/records.php",
];

if (isLoggedIn()) {
    $roleTarget = redirectByRole($route, $userType);
    if ($roleTarget !== null) {
        require $roleTarget;
        exit();
    }
}

if (array_key_exists($route, $routes)) {
    require $routes[$route];
} else {
    http_response_code(404);
    echo "404 - Page not found";
}
?>
