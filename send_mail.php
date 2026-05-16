<?php
header('Content-Type: application/json; charset=utf-8');

// ===== PHPMailer =====
require_once __DIR__ . '/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/phpmailer/src/SMTP.php';
require_once __DIR__ . '/phpmailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// ===== Ayarlar =====
$to_email       = "arifbuyukkose02@gmail.com";
$gmail_user     = "arifbuyukkose02@gmail.com";       // Gmail adresiniz
$gmail_app_pass = "kqqf svuz kcug mren";              // Gmail Uygulama Şifresi (App Password)
$subject_prefix = "[Portföy İletişim]";

// Sadece POST isteklerini kabul et
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Geçersiz istek metodu.']);
    exit;
}

// Form verilerini al ve temizle
$name    = isset($_POST['name'])    ? trim(strip_tags($_POST['name']))    : '';
$email   = isset($_POST['email'])   ? trim(strip_tags($_POST['email']))   : '';
$message = isset($_POST['message']) ? trim(strip_tags($_POST['message'])) : '';

// Validasyon
if (empty($name) || empty($email) || empty($message)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Lütfen tüm alanları doldurunuz.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Geçersiz e-posta adresi.']);
    exit;
}

// ===== E-posta Gönder =====
$mail = new PHPMailer(true);

try {
    // SMTP Ayarları
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = $gmail_user;
    $mail->Password   = $gmail_app_pass;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;
    $mail->CharSet    = 'UTF-8';

    // Gönderen & Alıcı
    $mail->setFrom($gmail_user, 'Portföy İletişim');
    $mail->addReplyTo($email, $name);
    $mail->addAddress($to_email);

    // İçerik
    $mail->isHTML(true);
    $mail->Subject = "$subject_prefix $name tarafından yeni mesaj";
    $mail->Body    = "
        <div style='font-family:Arial,sans-serif; max-width:600px; margin:0 auto; padding:24px; background:#f8f9fa; border-radius:12px;'>
            <h2 style='color:#6c5ce7; margin-bottom:20px;'>📩 Yeni İletişim Mesajı</h2>
            <table style='width:100%; border-collapse:collapse;'>
                <tr>
                    <td style='padding:10px 12px; font-weight:bold; color:#555; border-bottom:1px solid #e0e0e0; width:120px;'>Ad Soyad</td>
                    <td style='padding:10px 12px; color:#333; border-bottom:1px solid #e0e0e0;'>" . htmlspecialchars($name) . "</td>
                </tr>
                <tr>
                    <td style='padding:10px 12px; font-weight:bold; color:#555; border-bottom:1px solid #e0e0e0;'>E-Posta</td>
                    <td style='padding:10px 12px; color:#333; border-bottom:1px solid #e0e0e0;'>
                        <a href='mailto:" . htmlspecialchars($email) . "' style='color:#6c5ce7;'>" . htmlspecialchars($email) . "</a>
                    </td>
                </tr>
            </table>
            <div style='margin-top:20px; padding:16px; background:#fff; border-radius:8px; border:1px solid #e0e0e0;'>
                <p style='margin:0 0 8px; font-weight:bold; color:#555;'>Mesaj:</p>
                <p style='margin:0; color:#333; line-height:1.6;'>" . nl2br(htmlspecialchars($message)) . "</p>
            </div>
            <p style='margin-top:20px; font-size:12px; color:#999; text-align:center;'>
                " . date('d.m.Y H:i:s') . " tarihinde portföy sitesinden gönderildi.
            </p>
        </div>
    ";
    $mail->AltBody = "Ad: $name\nE-Posta: $email\n\nMesaj:\n$message";

    $mail->send();
    echo json_encode(['success' => true, 'message' => 'Mesajınız başarıyla gönderildi!']);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Mesaj gönderilemedi. Lütfen daha sonra tekrar deneyiniz.'
    ]);
}
