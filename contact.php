<?php
// contact.php
// Simple contact form handler. Expects POST with name, email, message.
// Saves messages to messages.json (append) and attempts to send email with mail().
// Return JSON: { success: bool, message: string }

header('Content-Type: application/json; charset=utf-8');

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Helper: sanitize
function clean($str) {
    return trim(filter_var($str, FILTER_SANITIZE_FULL_SPECIAL_CHARS));
}

$name = isset($_POST['name']) ? clean($_POST['name']) : '';
$email = isset($_POST['email']) ? filter_var($_POST['email'], FILTER_SANITIZE_EMAIL) : '';
$message = isset($_POST['message']) ? clean($_POST['message']) : '';

// Basic validation
if (strlen($name) < 2) {
    echo json_encode(['success' => false, 'message' => 'Name is too short']);
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email']);
    exit;
}
if (strlen($message) < 5) {
    echo json_encode(['success' => false, 'message' => 'Message is too short']);
    exit;
}

// Prepare message entry
$entry = [
    'id' => uniqid('', true),
    'name' => $name,
    'email' => $email,
    'message' => $message,
    'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
    'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
    'created_at' => date('c'),
];

// Save to messages.json (create if missing)
$storageFile = __DIR__ . '/messages.json';
$maxSize = 5 * 1024 * 1024; // 5MB safety limit

// Initialize file if missing
if (!file_exists($storageFile)) {
    file_put_contents($storageFile, json_encode([] , JSON_PRETTY_PRINT));
}

// Protect against huge files
if (filesize($storageFile) > $maxSize) {
    echo json_encode(['success' => false, 'message' => 'Message storage full']);
    exit;
}

// Read/append safely with flock
$fp = fopen($storageFile, 'c+');
if (!$fp) {
    echo json_encode(['success' => false, 'message' => 'Unable to open storage']);
    exit;
}

flock($fp, LOCK_EX);
$contents = stream_get_contents($fp);
$items = [];
if ($contents) {
    $decoded = json_decode($contents, true);
    if (is_array($decoded)) $items = $decoded;
}
$items[] = $entry;

// Rewind and write
ftruncate($fp, 0);
rewind($fp);
fwrite($fp, json_encode($items, JSON_PRETTY_PRINT));
fflush($fp);
flock($fp, LOCK_UN);
fclose($fp);

// Attempt to send an email notification (may require server mail config)
$to = 'your-email@kamsimozie267'; // change this
$subject = 'New contact message from ' . $name;
$body = "Name: {$name}\nEmail: {$email}\n\nMessage:\n{$message}\n\nSent: {$entry['created_at']}\nIP: {$entry['ip']}\n";
$headers = "From: no-reply@" . ($_SERVER['SERVER_NAME'] ?? 'localhost') . "\r\n";
$headers .= "Reply-To: {$email}\r\n";

// Use mail() if available — failure is non-fatal
@mail($to, $subject, $body, $headers);

// Success
echo json_encode(['success' => true, 'message' => 'Message received']);
exit;
?>
