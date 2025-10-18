<?php
// Simple session fix tool - standalone PHP file
session_start();

// Check if user_id exists in session
if (!isset($_SESSION['user_id'])) {
    echo "❌ No active session found. Please log in first at http://127.0.0.1:8000/login";
    exit;
}

$userId = $_SESSION['user_id'];

// Fetch user data from Auth API
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://127.0.0.1:8000/api/users/{$userId}");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200) {
    echo "❌ Could not fetch user data from Auth service. HTTP Code: {$httpCode}";
    exit;
}

$user = json_decode($response, true);

if (!$user) {
    echo "❌ Invalid response from Auth service.";
    exit;
}

// Update session with missing data
$_SESSION['user_profile_picture'] = $user['profile_picture_url'] ?? null;
$_SESSION['user_department'] = $user['department'] ?? null;
$_SESSION['user_name'] = $user['name'];
$_SESSION['user_role'] = $user['role'];

echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Session Fixed</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
</head>
<body class='bg-light'>
    <div class='container mt-5'>
        <div class='card shadow-sm'>
            <div class='card-body text-center'>
                <h2 class='text-success mb-4'>✅ Session Updated Successfully!</h2>
                <div class='mb-4'>";

if ($user['profile_picture_url'] ?? null) {
    $profileUrl = str_replace('http://localhost', 'http://127.0.0.1:8000', $user['profile_picture_url']);
    echo "<img src='{$profileUrl}' alt='Profile Picture' class='rounded-circle mb-3' style='width: 120px; height: 120px; object-fit: cover; border: 4px solid #00A689;'>";
} else {
    echo "<div class='rounded-circle bg-secondary d-inline-block mb-3' style='width: 120px; height: 120px;'></div>";
}

echo "
                    <h4>{$user['name']}</h4>
                    <p class='text-muted'>" . ucfirst(str_replace('_', ' ', $user['role'])) . "</p>
                    <p class='text-muted'>{$user['department']}</p>
                </div>
                <div class='alert alert-success'>
                    <strong>Your session has been updated with:</strong>
                    <ul class='text-start mt-2'>
                        <li>Profile Picture: " . ($user['profile_picture_url'] ? '✅ Found' : '❌ Not set') . "</li>
                        <li>Department: " . ($user['department'] ?? 'Not set') . "</li>
                        <li>Name: {$user['name']}</li>
                        <li>Role: {$user['role']}</li>
                    </ul>
                </div>
                <a href='/dashboard' class='btn btn-primary btn-lg mt-3'>
                    <i class='bi bi-arrow-left'></i> Go to Dashboard
                </a>
                <p class='mt-3 text-muted small'>Your profile picture should now appear in the sidebar!</p>
            </div>
        </div>
    </div>
</body>
</html>";
?>

