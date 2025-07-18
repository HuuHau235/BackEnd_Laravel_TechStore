<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailerService
{
    protected $mail;

    public function __construct()
    {
        $this->mail = new PHPMailer(true);
        $this->mail->isSMTP();
        $this->mail->Host = env('MAIL_HOST'); 
        $this->mail->SMTPAuth = true;
        $this->mail->Username = env('MAIL_USERNAME'); 
        $this->mail->Password = env('MAIL_PASSWORD'); 
        $this->mail->SMTPSecure = env('MAIL_ENCRYPTION'); 
        $this->mail->Port = env('MAIL_PORT'); 
        $this->mail->setFrom(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));

        $this->mail->isHTML(true);
    }

     public function send($to, $subject, $body, $attachmentPath = null)
    {
        try {
            $this->mail->addAddress($to);
            $this->mail->Subject = $subject;
            $this->mail->Body    = $body;

            if ($attachmentPath) {
                $this->mail->addAttachment($attachmentPath);
            }

            $this->mail->send();

            $this->mail->clearAddresses();

            return true;
        } catch (Exception $e) {
            \Log::error('Mailer Error: ' . $e->getMessage());
            return false;
        }
    }
}
