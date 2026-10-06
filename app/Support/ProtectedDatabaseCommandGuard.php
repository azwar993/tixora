<?php

namespace App\Support;

use RuntimeException;

class ProtectedDatabaseCommandGuard
{
    public const PROTECTED_DATABASE = 'tixora';

    public const EMERGENCY_OVERRIDE = 'ALLOW_DESTRUCTIVE_TIXORA';

    private const DESTRUCTIVE_COMMANDS = [
        'migrate:fresh',
        'migrate:refresh',
        'migrate:reset',
        'migrate:rollback',
        'db:wipe',
    ];

    public static function assertAllowed(?string $command, ?string $database, ?string $override): void
    {
        if (! self::shouldBlock($command, $database, $override)) {
            return;
        }

        throw new RuntimeException(
            'Protected database guard blocked [' . $command . '] for database [' . self::PROTECTED_DATABASE . ']. ' .
            'Normal migrations remain allowed. Emergency override requires ' .
            'TIXORA_EMERGENCY_OVERRIDE=' . self::EMERGENCY_OVERRIDE . '.'
        );
    }

    public static function shouldBlock(?string $command, ?string $database, ?string $override): bool
    {
        return in_array($command, self::DESTRUCTIVE_COMMANDS, true)
            && strtolower(trim((string) $database)) === self::PROTECTED_DATABASE
            && $override !== self::EMERGENCY_OVERRIDE;
    }
}
