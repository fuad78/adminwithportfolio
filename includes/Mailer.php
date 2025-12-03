<?php

require_once __DIR__ . '/../config.php';

class MailerException extends Exception {}

/**
 * Lightweight SMTP mailer to avoid external dependencies.
 */
class SmtpMailer
{
    private string $host;
    private int $port;
    private string $username;
    private string $password;
    private string $encryption;
    private string $fromAddress;
    private string $fromName;

    public function __construct(array $overrides = [])
    {
        $this->host = $overrides['host'] ?? SMTP_HOST;
        $this->port = (int) ($overrides['port'] ?? SMTP_PORT);
        $this->username = $overrides['username'] ?? SMTP_USERNAME;
        $this->password = $overrides['password'] ?? SMTP_PASSWORD;
        $this->encryption = in_array(strtolower($overrides['encryption'] ?? SMTP_ENCRYPTION), ['tls', 'ssl'], true)
            ? strtolower($overrides['encryption'] ?? SMTP_ENCRYPTION)
            : 'tls';
        $this->fromAddress = $overrides['fromAddress'] ?? MAIL_FROM_ADDRESS;
        $this->fromName = $overrides['fromName'] ?? MAIL_FROM_NAME;

        if (!$this->host || !$this->username || !$this->password) {
            throw new MailerException('SMTP credentials are not configured. Set SMTP_HOST/SMTP_USERNAME/SMTP_PASSWORD.');
        }
    }

    /**
     * Sends an email via SMTP.
     *
     * @throws MailerException
     */
    public function send(string $toEmail, string $toName, string $subject, string $htmlBody, ?string $textBody = null): void
    {
        if (!filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
            throw new MailerException('Invalid recipient email address.');
        }

        $socket = $this->openConnection();

        try {
            $this->expectCode($socket, 220);
            $this->sayHello($socket);
            $this->authenticate($socket);

            $this->sendCommand($socket, 'MAIL FROM:<' . $this->fromAddress . '>', 250);
            $this->sendCommand($socket, 'RCPT TO:<' . $toEmail . '>', 250);
            $this->sendCommand($socket, 'DATA', 354);

            $message = $this->composeMessage($toEmail, $toName, $subject, $htmlBody, $textBody);
            $this->write($socket, $message . "\r\n.\r\n");
            $this->expectCode($socket, 250);

            $this->sendCommand($socket, 'QUIT', 221);
        } finally {
            fclose($socket);
        }
    }

    private function openConnection()
    {
        $transportHost = $this->encryption === 'ssl' ? 'ssl://' . $this->host : $this->host;
        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => true,
                'verify_peer_name' => true,
                'allow_self_signed' => false,
            ],
        ]);

        $socket = @stream_socket_client(
            sprintf('%s:%d', $transportHost, $this->port),
            $errno,
            $errstr,
            30,
            STREAM_CLIENT_CONNECT,
            $context
        );

        if (!$socket) {
            throw new MailerException("Unable to connect to SMTP server: {$errstr} ({$errno})");
        }

        stream_set_timeout($socket, 30);
        return $socket;
    }

    private function sayHello($socket): void
    {
        $ehloHost = gethostname() ?: 'localhost';
        $this->sendCommand($socket, 'EHLO ' . $ehloHost, 250);

        if ($this->encryption === 'tls') {
            $this->sendCommand($socket, 'STARTTLS', 220);
            if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                throw new MailerException('Failed to initiate STARTTLS.');
            }
            $this->sendCommand($socket, 'EHLO ' . $ehloHost, 250);
        }
    }

    private function authenticate($socket): void
    {
        $this->sendCommand($socket, 'AUTH LOGIN', 334);
        $this->sendCommand($socket, base64_encode($this->username), 334);
        $this->sendCommand($socket, base64_encode($this->password), 235);
    }

    private function composeMessage(string $toEmail, string $toName, string $subject, string $htmlBody, ?string $textBody): string
    {
        $boundary = 'b' . bin2hex(random_bytes(16));
        $headers = [
            'From: ' . $this->formatAddress($this->fromAddress, $this->fromName),
            'To: ' . $this->formatAddress($toEmail, $toName),
            'Subject: ' . $this->encodeHeader($subject),
            'MIME-Version: 1.0',
            'Content-Type: multipart/alternative; boundary="' . $boundary . '"',
        ];

        $textPart = $textBody ?: $this->generatePlainText($htmlBody);

        $body = [];
        $body[] = '--' . $boundary;
        $body[] = 'Content-Type: text/plain; charset=UTF-8';
        $body[] = 'Content-Transfer-Encoding: 8bit';
        $body[] = '';
        $body[] = $textPart;
        $body[] = '';
        $body[] = '--' . $boundary;
        $body[] = 'Content-Type: text/html; charset=UTF-8';
        $body[] = 'Content-Transfer-Encoding: 8bit';
        $body[] = '';
        $body[] = $htmlBody;
        $body[] = '';
        $body[] = '--' . $boundary . '--';

        return implode("\r\n", $headers) . "\r\n\r\n" . implode("\r\n", $body);
    }

    private function formatAddress(string $email, string $name): string
    {
        $sanitizedName = addcslashes($name, '"\\');
        return sprintf('"%s" <%s>', $sanitizedName ?: $email, $email);
    }

    private function generatePlainText(string $html): string
    {
        $text = strip_tags($html);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        return trim(preg_replace('/[\r\n]{2,}/', "\n\n", $text));
    }

    private function encodeHeader(string $value): string
    {
        return '=?UTF-8?B?' . base64_encode($value) . '?=';
    }

    private function sendCommand($socket, string $command, int $expectedCode): array
    {
        $this->write($socket, $command . "\r\n");
        [$code, $message] = $this->readResponse($socket);

        if ($code !== $expectedCode) {
            throw new MailerException("Unexpected SMTP response ({$code}): {$message}");
        }

        return [$code, $message];
    }

    private function write($socket, string $data): void
    {
        $result = fwrite($socket, $data);
        if ($result === false) {
            throw new MailerException('Failed to write to SMTP connection.');
        }
    }

    private function expectCode($socket, int $code): array
    {
        [$responseCode, $message] = $this->readResponse($socket);
        if ($responseCode !== $code) {
            throw new MailerException("Unexpected SMTP response ({$responseCode}): {$message}");
        }
        return [$responseCode, $message];
    }

    private function readResponse($socket): array
    {
        $data = '';
        while (($line = fgets($socket, 512)) !== false) {
            $data .= $line;
            if (strlen($line) >= 4 && $line[3] === ' ') {
                $code = (int) substr($line, 0, 3);
                return [$code, trim($data)];
            }
        }

        throw new MailerException('No response from SMTP server.');
    }
}

