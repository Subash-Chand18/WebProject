<?php
session_start();
require_once 'vendor/autoload.php'; // Autoload Google API classes

$con = mysqli_connect("localhost", "root", "", "E_Clothing_Store");
if (!$con) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit;
}

// Initialize Google Client
$client = new Google_Client();
$client->setClientId('731513726294-dtfa773a7fpbuhc4d543f20a36m7pt7n.apps.googleusercontent.com'); // Your actual Client ID

// Get JSON body (token from frontend)
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['token'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Token not provided.']);
    exit;
}

$token = $data['token'];

try {
    $payload = $client->verifyIdToken($token);

    if ($payload) {
        $email = $payload['email'];
        $name = $payload['name'];
        $picture = isset($payload['picture']) ? $payload['picture'] : '';

        // Check if user exists
        $query = "SELECT * FROM users WHERE email = ? AND deleted_at IS NULL";
        $stmt = $con->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            $user = $result->fetch_assoc();
        } else {
            // New User
            $insert = $con->prepare("INSERT INTO users (name, email, image, user_type) VALUES (?, ?, ?, 'user')");
            $insert->bind_param("sss", $name, $email, $picture);
            $insert->execute();

            $user_id = $insert->insert_id;
            $insert->close();

            $fetch = $con->prepare("SELECT * FROM users WHERE id = ?");
            $fetch->bind_param("i", $user_id);
            $fetch->execute();
            $result = $fetch->get_result();
            $user = $result->fetch_assoc();
            $fetch->close();
        }
        $stmt->close();

        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['user_type'] = $user['user_type'];
        $_SESSION['user_image'] = $user['image'];

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid ID token.']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Token verification failed.', 'error' => $e->getMessage()]);
}
?>
