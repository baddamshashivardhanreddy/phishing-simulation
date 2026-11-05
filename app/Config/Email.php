<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Email extends BaseConfig
{
    public string $fromEmail  = '';
    public string $fromName   = '';
    public string $recipients = '';

    /**
     * The "user agent"
     */
    public string $userAgent = 'CodeIgniter';

    /**
     * The mail sending protocol: mail, sendmail, smtp
     */
    /**
     * The mail sending protocol: mail, sendmail, smtp
     */
    public string $protocol = 'smtp';

    /**
     * The server path to Sendmail.
     */
    public string $mailPath = '/usr/sbin/sendmail';

    /**
     * SMTP Server Hostname
     */
    // GMAIL CONFIGURATION (Default)
    public string $SMTPHost = 'smtp.gmail.com';
    
    // OUTLOOK CONFIGURATION (Uncomment below and comment above to use)
    // public string $SMTPHost = 'smtp.office365.com';

    /**
     * SMTP Username
     */
    public string $SMTPUser = 'nobitanobi0272@gmail.com'; // <--- Enter your email address

    /**
     * SMTP Password
     */
    public string $SMTPPass = 'bbajqzhbaclrzbux'; // <--- Enter your App Password (NOT your login password)

    /**
     * SMTP Port
     */
    public int $SMTPPort = 587;

    /**
     * SMTP Timeout (in seconds) - Reduced to prevent hanging and timeouts
     */
    public int $SMTPTimeout = 10;

    /**
     * Enable persistent SMTP connections for high-speed multi-user sending
     */
    public bool $SMTPKeepAlive = true;

    /**
     * SMTP Encryption.
     *
     * @var string '', 'tls' or 'ssl'. 'tls' will issue a STARTTLS command
     *             to the server. 'ssl' means implicit SSL. Connection on port
     *             465 should set this to ''.
     */
    public string $SMTPCrypto = 'tls';

    /**
     * Enable word-wrap
     */
    public bool $wordWrap = true;

    /**
     * Character count to wrap at
     */
    public int $wrapChars = 76;

    /**
     * Type of mail, either 'text' or 'html'
     */
    public string $mailType = 'text';

    /**
     * Character set (utf-8, iso-8859-1, etc.)
     */
    public string $charset = 'UTF-8';

    /**
     * Whether to validate the email address
     */
    public bool $validate = false;

    /**
     * Email Priority. 1 = highest. 5 = lowest. 3 = normal
     */
    public int $priority = 3;

    /**
     * Newline character. (Use “\r\n” to comply with RFC 822)
     */
    public string $CRLF = "\r\n";

    /**
     * Newline character. (Use “\r\n” to comply with RFC 822)
     */
    public string $newline = "\r\n";

    /**
     * Enable BCC Batch Mode.
     */
    public bool $BCCBatchMode = false;

    /**
     * Number of emails in each BCC batch
     */
    public int $BCCBatchSize = 200;

    /**
     * Enable notify message from server
     */
    public bool $DSN = false;

    public function __construct()
    {
        parent::__construct();

        $settingsPath = WRITEPATH . 'settings.json';
        if (file_exists($settingsPath)) {
            $settings = json_decode(file_get_contents($settingsPath), true);
            if ($settings) {
                if (!empty($settings['smtp_host'])) $this->SMTPHost = $settings['smtp_host'];
                if (!empty($settings['smtp_user'])) $this->SMTPUser = $settings['smtp_user'];
                if (!empty($settings['smtp_pass'])) $this->SMTPPass = $settings['smtp_pass'];
                if (!empty($settings['smtp_port'])) $this->SMTPPort = (int)$settings['smtp_port'];
                if (!empty($settings['smtp_crypto'])) $this->SMTPCrypto = $settings['smtp_crypto'];
            }
        }
    }
}
