<?php

namespace App\Services;

class EmailService
{
    private $db;
    private $fromEmail = 'noreply@yerevango.com';

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Subscribe an email to the newsletter
     */
    public function subscribe(string $email, ?int $userId = null): array
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['status' => 'error', 'message' => 'Invalid email address'];
        }

        try {
            $sql = "INSERT INTO newsletter_subscribers (email, user_id) 
                    VALUES (?, ?) 
                    ON CONFLICT (email) DO UPDATE 
                    SET is_active = TRUE, unsubscribed_at = NULL";
            $this->db->query($sql, [$email, $userId]);
            
            // Send Welcome Email (Simulated)
            $this->sendWelcomeEmail($email);

            return ['status' => 'success', 'message' => 'Successfully subscribed!'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    /**
     * Unsubscribe an email
     */
    public function unsubscribe(string $email): bool
    {
        $sql = "UPDATE newsletter_subscribers SET is_active = FALSE, unsubscribed_at = CURRENT_TIMESTAMP WHERE email = ?";
        $this->db->query($sql, [$email]);
        return true;
    }

    /**
     * Send welcome email to new subscriber
     */
    public function sendWelcomeEmail(string $to): bool
    {
        $subject = "Welcome to Yerevango! 🇦🇲";
        $message = "
        <html>
        <head>
            <style>
                body { font-family: sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #eee; border-radius: 10px; }
                .header { background: #D90012; color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
                .footer { font-size: 0.8rem; color: #999; margin-top: 20px; text-align: center; }
                .btn { display: inline-block; padding: 10px 20px; background: #D90012; color: white; text-decoration: none; border-radius: 5px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Yerevango</h1>
                </div>
                <h2>Welcome to Yerevan's Social Hub!</h2>
                <p>Thank you for subscribing to our newsletter. You'll now receive weekly updates on:</p>
                <ul>
                    <li>The hottest new restaurants and cafes.</li>
                    <li>Upcoming festivals and cultural events.</li>
                    <li>Personalized tourist routes.</li>
                </ul>
                <p><a href='https://yerevango.com' class='btn'>Explore Yerevan Now</a></p>
                <div class='footer'>
                    &copy; " . date('Y') . " Yerevango. All rights reserved.<br>
                    You are receiving this because you signed up on our website.
                </div>
            </div>
        </body>
        </html>";

        return $this->sendHtmlEmail($to, $subject, $message);
    }

    /**
     * Internal helper to send HTML emails
     */
    private function sendHtmlEmail(string $to, string $subject, string $htmlContent): bool
    {
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: Yerevango <" . $this->fromEmail . ">" . "\r\n";

        // In a real environment, we'd use PHPMailer with SMTP. 
        // For now, we use mail() which is often disabled in dev but standard in PHP.
        // error_log("E-mail to $to: $subject"); // Logging for verification
        return @mail($to, $subject, $htmlContent, $headers);
    }
}
