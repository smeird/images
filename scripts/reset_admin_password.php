#!/usr/bin/env php
<?php

declare(strict_types=1);

const MINIMUM_PASSWORD_LENGTH = 12;
const DEFAULT_ADMIN_USERNAME = 'admin';

function fail(string $message, int $exitCode = 1): void
{
    fwrite(STDERR, $message . PHP_EOL);
    exit($exitCode);
}

function prompt_hidden(string $prompt): string
{
    if (!function_exists('stream_isatty') || !stream_isatty(STDIN)) {
        fail('This command needs an interactive terminal. Run it directly instead of piping input.');
    }

    if (!function_exists('exec')) {
        fail('PHP cannot hide terminal input because the exec function is disabled.');
    }

    $output = [];
    $status = 1;
    exec('stty -echo < /dev/tty 2>/dev/null', $output, $status);
    if ($status !== 0) {
        fail('Unable to hide terminal input; password reset was cancelled.');
    }

    try {
        fwrite(STDOUT, $prompt);
        $value = fgets(STDIN);
    } finally {
        exec('stty echo < /dev/tty 2>/dev/null');
        fwrite(STDOUT, PHP_EOL);
    }

    if ($value === false) {
        fail('Unable to read the password; nothing was changed.');
    }

    return rtrim($value, "\r\n");
}

/**
 * @param array<string, mixed> $metadata
 */
function atomic_replace(string $path, string $contents, array $metadata): void
{
    $directory = dirname($path);
    $temporaryPath = tempnam($directory, '.password-reset-');
    if ($temporaryPath === false) {
        throw new RuntimeException('Unable to create a temporary credential file.');
    }

    try {
        if (file_put_contents($temporaryPath, $contents, LOCK_EX) === false) {
            throw new RuntimeException('Unable to write the updated credential file.');
        }

        $mode = ((int) ($metadata['mode'] ?? 0660)) & 0777;
        if (!chmod($temporaryPath, $mode)) {
            throw new RuntimeException('Unable to preserve credential file permissions.');
        }

        $temporaryMetadata = stat($temporaryPath);
        if ($temporaryMetadata === false) {
            throw new RuntimeException('Unable to inspect temporary credential file ownership.');
        }

        if ((int) $temporaryMetadata['uid'] !== (int) $metadata['uid']
            && !chown($temporaryPath, (int) $metadata['uid'])) {
            throw new RuntimeException('Unable to preserve credential file ownership.');
        }

        if ((int) $temporaryMetadata['gid'] !== (int) $metadata['gid']
            && !chgrp($temporaryPath, (int) $metadata['gid'])) {
            throw new RuntimeException('Unable to preserve credential file group.');
        }

        if (!rename($temporaryPath, $path)) {
            throw new RuntimeException('Unable to install the updated credential file.');
        }

        $temporaryPath = '';
    } finally {
        if ($temporaryPath !== '' && is_file($temporaryPath)) {
            @unlink($temporaryPath);
        }
    }
}

if (PHP_SAPI !== 'cli') {
    fail('This password recovery utility can only run from the command line.');
}

$projectPath = dirname(__DIR__);
$usersPath = $projectPath . '/storage/data/users.json';
$attemptsPath = $projectPath . '/storage/cache/login_attempts.json';
$sessionsPattern = $projectPath . '/storage/sessions/sess_*';

if (!is_file($usersPath) || is_link($usersPath)) {
    fail('Credential file not found at ' . $usersPath);
}

$password = prompt_hidden('New production password (12+ characters): ');
$confirmation = prompt_hidden('Repeat password: ');

if (!hash_equals($password, $confirmation)) {
    fail('Passwords do not match; nothing was changed.');
}

if (strlen($password) < MINIMUM_PASSWORD_LENGTH) {
    fail('Password must contain at least ' . MINIMUM_PASSWORD_LENGTH . ' characters; nothing was changed.');
}

if ($password === 'change-me-now') {
    fail('Choose a unique password instead of the documented development default.');
}

$rawUsers = file_get_contents($usersPath);
if ($rawUsers === false) {
    fail('Unable to read the credential file.');
}

try {
    $users = json_decode($rawUsers, true, 512, JSON_THROW_ON_ERROR);
} catch (JsonException $exception) {
    fail('Credential file contains invalid JSON: ' . $exception->getMessage());
}

if (!is_array($users)) {
    fail('Credential file does not contain an account list.');
}

$updated = false;
foreach ($users as &$user) {
    if (!is_array($user) || ($user['username'] ?? '') !== DEFAULT_ADMIN_USERNAME) {
        continue;
    }

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    if (!is_string($passwordHash)) {
        fail('Unable to hash the new password; nothing was changed.');
    }

    $user['password_hash'] = $passwordHash;
    unset($user['remember_token_hash'], $user['remember_token_expires_at']);
    $updated = true;
    break;
}
unset($user, $password, $confirmation);

if (!$updated) {
    fail('Admin account "' . DEFAULT_ADMIN_USERNAME . '" was not found; nothing was changed.');
}

try {
    $encodedUsers = json_encode(
        $users,
        JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR
    ) . PHP_EOL;
} catch (JsonException $exception) {
    fail('Unable to encode the updated credential file: ' . $exception->getMessage());
}

$usersMetadata = stat($usersPath);
if ($usersMetadata === false) {
    fail('Unable to inspect credential file ownership and permissions.');
}

try {
    atomic_replace($usersPath, $encodedUsers, $usersMetadata);
} catch (RuntimeException $exception) {
    fail($exception->getMessage());
}

if (is_file($attemptsPath) && file_put_contents($attemptsPath, "[]\n", LOCK_EX) === false) {
    fwrite(STDERR, 'Warning: password changed, but failed-login attempts could not be cleared.' . PHP_EOL);
}

$failedSessionDeletes = 0;
foreach (glob($sessionsPattern) ?: [] as $sessionPath) {
    if (!is_file($sessionPath) || !unlink($sessionPath)) {
        $failedSessionDeletes++;
    }
}

if ($failedSessionDeletes > 0) {
    fwrite(STDERR, 'Warning: password changed, but some existing sessions could not be revoked.' . PHP_EOL);
}

fwrite(STDOUT, 'Production admin password reset successfully. Open a fresh login page to sign in.' . PHP_EOL);
