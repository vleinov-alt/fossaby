<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

function json_response(int $code, array $payload): void
{
    http_response_code($code);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

function load_env(string $path): array
{
    if (!is_file($path) || !is_readable($path)) {
        return [];
    }

    $env = [];
    foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || substr($line, 0, 1) === '#' || strpos($line, '=') === false) {
            continue;
        }

        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);

        if (
            (substr($value, 0, 1) === '"' && substr($value, -1) === '"') ||
            (substr($value, 0, 1) === "'" && substr($value, -1) === "'")
        ) {
            $value = substr($value, 1, -1);
        }

        $env[$key] = $value;
    }

    return $env;
}

function env_value(array $env, string $key, ?string $default = null): ?string
{
    $value = getenv($key);
    if ($value !== false && $value !== '') {
        return $value;
    }

    return $env[$key] ?? $default;
}

function clean_header(string $value): string
{
    return trim(str_replace(["\r", "\n"], ' ', $value));
}

function smtp_read($socket): string
{
    $response = '';
    while (($line = fgets($socket, 515)) !== false) {
        $response .= $line;
        if (strlen($line) >= 4 && $line[3] === ' ') {
            break;
        }
    }

    return $response;
}

function smtp_command($socket, string $command, array $expectedCodes): string
{
    fwrite($socket, $command . "\r\n");
    $response = smtp_read($socket);
    $code = (int) substr($response, 0, 3);

    if (!in_array($code, $expectedCodes, true)) {
        $safeCommand = preg_match('/^(AUTH|EHLO|STARTTLS|MAIL|RCPT|DATA|QUIT)\b/i', $command)
            ? preg_replace('/\s+.*/', ' ***', $command)
            : '[credentials]';
        throw new RuntimeException('SMTP error after "' . $safeCommand . '": ' . trim($response));
    }

    return $response;
}

function smtp_send(array $config, string $subject, string $body, ?string $replyTo): void
{
    $host = $config['host'];
    $port = (int) $config['port'];
    $timeout = 15;

    $socket = stream_socket_client(
        'tcp://' . $host . ':' . $port,
        $errno,
        $errstr,
        $timeout,
        STREAM_CLIENT_CONNECT
    );

    if (!$socket) {
        throw new RuntimeException('SMTP connection failed: ' . $errstr . ' (' . $errno . ')');
    }

    stream_set_timeout($socket, $timeout);
    smtp_read($socket);
    smtp_command($socket, 'EHLO fossa.by', [250]);

    if (($config['secure'] ?? 'tls') === 'tls') {
        smtp_command($socket, 'STARTTLS', [220]);
        if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
            throw new RuntimeException('SMTP TLS handshake failed');
        }
        smtp_command($socket, 'EHLO fossa.by', [250]);
    }

    smtp_command($socket, 'AUTH LOGIN', [334]);
    smtp_command($socket, base64_encode($config['username']), [334]);
    smtp_command($socket, base64_encode($config['password']), [235]);
    smtp_command($socket, 'MAIL FROM:<' . $config['fromEmail'] . '>', [250]);
    smtp_command($socket, 'RCPT TO:<' . $config['toEmail'] . '>', [250, 251]);
    smtp_command($socket, 'DATA', [354]);

    $headers = [
        'From: ' . clean_header($config['fromName']) . ' <' . $config['fromEmail'] . '>',
        'To: ' . $config['toEmail'],
        'Subject: =?UTF-8?B?' . base64_encode($subject) . '?=',
        'MIME-Version: 1.0',
        'Content-Type: text/plain; charset=UTF-8',
        'Content-Transfer-Encoding: 8bit',
    ];

    if ($replyTo && filter_var($replyTo, FILTER_VALIDATE_EMAIL)) {
        $headers[] = 'Reply-To: ' . $replyTo;
    }

    $message = implode("\r\n", $headers) . "\r\n\r\n" . str_replace(["\r\n.\r\n", "\n.\n"], "\n..\n", $body);
    fwrite($socket, $message . "\r\n.\r\n");
    $response = smtp_read($socket);
    $code = (int) substr($response, 0, 3);
    if ($code !== 250) {
        throw new RuntimeException('SMTP send failed: ' . trim($response));
    }

    smtp_command($socket, 'QUIT', [221]);
    fclose($socket);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(405, ['status' => 'error', 'message' => 'Method not allowed']);
}

$env = load_env(__DIR__ . '/.env');
$username = env_value($env, 'MAILER_USERNAME');
$password = env_value($env, 'MAILER_PASSWORD');

if (!$username || !$password) {
    json_response(500, ['status' => 'error', 'message' => 'Mailer credentials are not configured']);
}

$name = trim((string) ($_POST['name'] ?? ''));
$contact = trim((string) ($_POST['contact'] ?? $_POST['phone'] ?? $_POST['email'] ?? ''));

if ($name === '' || $contact === '') {
    json_response(422, ['status' => 'error', 'message' => 'Заполните имя и контакт']);
}

$labels = [
    'clinic' => 'Клиника',
    'name' => 'Имя',
    'contact' => 'Контакт',
    'phone' => 'Телефон',
    'email' => 'Email',
    'task' => 'Задача',
    'message' => 'Сообщение',
    '_page' => 'Страница',
    '_goal' => 'Форма',
];

$lines = [];
foreach ($labels as $key => $label) {
    $value = trim((string) ($_POST[$key] ?? ''));
    if ($value !== '') {
        $lines[] = $label . ': ' . $value;
    }
}

$lines[] = 'IP: ' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
$lines[] = 'User-Agent: ' . ($_SERVER['HTTP_USER_AGENT'] ?? 'unknown');

$replyTo = trim((string) ($_POST['email'] ?? ''));
$subject = 'Заявка с fossa.by';
if (!empty($_POST['_goal'])) {
    $subject .= ': ' . clean_header((string) $_POST['_goal']);
}

$config = [
    'host' => env_value($env, 'MAILER_HOST', 'mailbe01.hoster.by'),
    'port' => env_value($env, 'MAILER_PORT', '587'),
    'secure' => env_value($env, 'MAILER_SECURE', 'tls'),
    'username' => $username,
    'password' => $password,
    'fromEmail' => env_value($env, 'MAIL_FROM', filter_var($username, FILTER_VALIDATE_EMAIL) ? $username : 'fossaby_guest@fossa.by'),
    'fromName' => env_value($env, 'MAIL_FROM_NAME', 'Поступила заявка на fossa.by'),
    'toEmail' => env_value($env, 'MAIL_TO', 'it@fossa.by'),
];

try {
    smtp_send($config, $subject, implode("\n", $lines), $replyTo);
    json_response(200, ['status' => 'ok']);
} catch (Throwable $e) {
    error_log('[send_mail] ' . $e->getMessage());
    json_response(500, ['status' => 'error', 'message' => 'Не удалось отправить заявку']);
}
