<?php
/**
 * API Endpoint untuk Form Kontak
 * SMP Bina Informatika
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// CORS preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed'
    ]);
    exit();
}

try {
    // Get POST data
    $postData = $_POST;
    
    // Validate required fields
    $requiredFields = ['contact_name', 'contact_email', 'contact_subject', 'contact_message'];
    
    foreach ($requiredFields as $field) {
        if (empty($postData[$field])) {
            throw new Exception("Field $field is required");
        }
    }
    
    // Validate email
    if (!filter_var($postData['contact_email'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Invalid email format");
    }
    
    // Sanitize input
    $name = htmlspecialchars(strip_tags($postData['contact_name']));
    $email = filter_var($postData['contact_email'], FILTER_SANITIZE_EMAIL);
    $subject = htmlspecialchars(strip_tags($postData['contact_subject']));
    $message = htmlspecialchars(strip_tags($postData['contact_message']));
    
    // Validate message length
    if (strlen($message) < 10) {
        throw new Exception("Message must be at least 10 characters long");
    }
    
    if (strlen($message) > 1000) {
        throw new Exception("Message is too long (maximum 1000 characters)");
    }
    
    // Prepare email content
    $to = "info.sdpjointaro@spj.sch.id";
    $emailSubject = "Pesan dari Website SMP Bina Informatika: " . $subject;
    
    $emailBody = "
    <html>
    <head>
        <title>Pesan dari Website</title>
    </head>
    <body>
        <h2>Pesan Baru dari Website SMP Bina Informatika</h2>
        <table>
            <tr>
                <td><strong>Nama:</strong></td>
                <td>$name</td>
            </tr>
            <tr>
                <td><strong>Email:</strong></td>
                <td>$email</td>
            </tr>
            <tr>
                <td><strong>Subjek:</strong></td>
                <td>$subject</td>
            </tr>
            <tr>
                <td><strong>Pesan:</strong></td>
                <td>" . nl2br($message) . "</td>
            </tr>
            <tr>
                <td><strong>Tanggal:</strong></td>
                <td>" . date('Y-m-d H:i:s') . "</td>
            </tr>
            <tr>
                <td><strong>IP Address:</strong></td>
                <td>" . $_SERVER['REMOTE_ADDR'] . "</td>
            </tr>
        </table>
    </body>
    </html>
    ";
    
    // Email headers
    $headers = array(
        'MIME-Version: 1.0',
        'Content-type: text/html; charset=UTF-8',
        'From: ' . $email,
        'Reply-To: ' . $email,
        'X-Mailer: PHP/' . phpversion()
    );
    
    // Send email
    $mailSent = mail($to, $emailSubject, $emailBody, implode("\r\n", $headers));
    
    if ($mailSent) {
        // Log successful contact
        error_log("Contact form submitted: $name ($email) - $subject");
        
        // Send auto-reply to sender
        $autoReplySubject = "Terima Kasih - SMP Bina Informatika";
        $autoReplyBody = "
        <html>
        <body>
            <h2>Terima Kasih Telah Menghubungi Kami</h2>
            <p>Halo $name,</p>
            <p>Terima kasih telah mengirim pesan kepada SMP Bina Informatika. Kami telah menerima pesan Anda dan akan segera menghubungi Anda kembali.</p>
            <p><strong>Detail pesan Anda:</strong></p>
            <ul>
                <li><strong>Subjek:</strong> $subject</li>
                <li><strong>Pesan:</strong> $message</li>
            </ul>
            <p>Untuk informasi lebih lanjut, Anda dapat menghubungi kami melalui:</p>
            <ul>
                <li>Telepon: 083896226790 (Rizam Nuruzaman)</li>
                <li>Email: info.sdpjointaro@spj.sch.id</li>
                <li>Alamat: Jl. Tegal Rotan Raya No.8 A, Sawah Baru, Kec. Ciputat, Kota Tangerang Selatan, Banten 15412</li>
            </ul>
            <p>Salam,<br>Tim SMP Bina Informatika</p>
        </body>
        </html>
        ";
        
        $autoReplyHeaders = array(
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=UTF-8',
            'From: SMP Bina Informatika <noreply@smpbinainformatika.sch.id>',
            'X-Mailer: PHP/' . phpversion()
        );
        
        mail($email, $autoReplySubject, $autoReplyBody, implode("\r\n", $autoReplyHeaders));
        
        // Save to database (optional)
        $this->saveContactMessage($name, $email, $subject, $message);
        
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Pesan Anda telah berhasil dikirim. Kami akan segera menghubungi Anda kembali.'
        ]);
    } else {
        throw new Exception("Failed to send email. Please try again later.");
    }
    
} catch (Exception $e) {
    // Log error
    error_log("Contact form error: " . $e->getMessage());
    
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} catch (Error $e) {
    // Log system error
    error_log("System error in contact form: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Internal server error. Please try again later.'
    ]);
}

/**
 * Save contact message to database (optional)
 */
function saveContactMessage($name, $email, $subject, $message) {
    try {
        require_once '../config/database.php';
        
        $database = new Database();
        $db = $database->getConnection();
        
        $query = "INSERT INTO contact_messages (name, email, subject, message, created_at) 
                  VALUES (:name, :email, :subject, :message, NOW())";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':subject', $subject);
        $stmt->bindParam(':message', $message);
        
        $stmt->execute();
        
    } catch (Exception $e) {
        // Log database error but don't fail the contact form
        error_log("Database error saving contact message: " . $e->getMessage());
    }
}
?> 