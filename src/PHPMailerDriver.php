<?php

namespace PHPAuth\Mailer;

use PHPAuth\Exceptions\PHPAuthException;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class PHPMailerDriver implements MailerInterface
{
    private PHPMailer $mailer;
    private array $config;

    /**
     * @throws Exception
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
        $this->initMailer();
    }

    /**
     * @throws Exception
     */
    private function initMailer(): void
    {
        if (!class_exists('\PHPMailer\PHPMailer\PHPMailer')) {
            throw new \RuntimeException('PHPMailer not installed');
        }

        $this->mailer = new PHPMailer(true);

        if ($this->config['debug']) {
            $this->mailer->SMTPDebug = $this->config['debug'];
        }

        $this->mailer->isSMTP();

        $this->mailer->Host = $this->config['host'];
        $this->mailer->SMTPAuth = $this->config['auth'] ?? true;

        if ($this->mailer->SMTPAuth) {
            $this->mailer->Username = $this->config['username'] ?? '';
            $this->mailer->Password = $this->config['password'] ?? '';
        }

        if ($this->config['secure']) {
            $this->mailer->SMTPSecure = $this->config['secure'];
        }

        $this->mailer->Port = $this->config['port'];

        $this->mailer->CharSet = 'UTF-8';
        $this->mailer->isHTML(true);

        $this->mailer->setFrom($this->config['site_email'], $this->config['site_name']);
    }


    /**
     * @throws Exception
     * @throws PHPAuthException
     */
    public function send(string $to, string $subject = '', string $body = '', string $altbody = '', ?string $from = null, ?string $fromName = null): bool
    {
        $this->mailer->addAddress($to);
        $this->mailer->Subject = $subject;
        $this->mailer->Body = $body;
        $this->mailer->AltBody = $altbody;

        if ($from && $fromName) {
            $this->mailer->setFrom($from, $fromName);
        }

        if (!$this->mailer->send()) {
            throw new PHPAuthException($this->mailer->ErrorInfo);
        }

        return true;
    }

    public function sendPasswordReset(string $email, string $resetKey): bool
    {
        return true;
    }

    public function sendActivation(string $email, string $activationKey): bool
    {
        return true;
    }
}