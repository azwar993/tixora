<?php

namespace Tests\Unit;

use App\Support\ProtectedDatabaseCommandGuard;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class ProtectedDatabaseCommandGuardTest extends TestCase
{
    public function test_destructive_command_is_blocked_for_protected_database(): void
    {
        $this->assertTrue(
            ProtectedDatabaseCommandGuard::shouldBlock('migrate:fresh', 'tixora', null)
        );
    }

    public function test_normal_migrate_is_allowed_for_protected_database(): void
    {
        $this->assertFalse(
            ProtectedDatabaseCommandGuard::shouldBlock('migrate', 'tixora', null)
        );
    }

    public function test_other_database_is_not_blocked(): void
    {
        $this->assertFalse(
            ProtectedDatabaseCommandGuard::shouldBlock('db:wipe', 'tixora_recovery', null)
        );
    }

    public function test_exact_emergency_override_allows_destructive_command(): void
    {
        $this->assertFalse(
            ProtectedDatabaseCommandGuard::shouldBlock(
                'migrate:refresh',
                'tixora',
                ProtectedDatabaseCommandGuard::EMERGENCY_OVERRIDE
            )
        );
    }

    public function test_block_message_identifies_protected_database_and_override(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Protected database guard blocked');
        $this->expectExceptionMessage('TIXORA_EMERGENCY_OVERRIDE=ALLOW_DESTRUCTIVE_TIXORA');

        ProtectedDatabaseCommandGuard::assertAllowed('db:wipe', 'tixora', null);
    }
}