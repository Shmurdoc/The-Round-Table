<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== FIXING CLEANUP PROCEDURE ===\n\n";

try {
    // Drop existing procedure
    echo "1. Dropping existing procedure (if exists)...\n";
    DB::statement('DROP PROCEDURE IF EXISTS cleanup_expired_payment_sessions');
    echo "   ✓ Dropped successfully\n\n";
    
    // Create new procedure without DELIMITER statements
    echo "2. Creating improved stored procedure...\n";
    
    $sql = "
    CREATE DEFINER=`root`@`localhost` PROCEDURE `cleanup_expired_payment_sessions`()
    BEGIN
        -- Variables to capture row counts and error details
        DECLARE expired_count INT DEFAULT 0;
        DECLARE deleted_count INT DEFAULT 0;
        DECLARE table_exists INT DEFAULT 0;
        DECLARE error_msg VARCHAR(255) DEFAULT '';
        DECLARE CONTINUE HANDLER FOR SQLEXCEPTION
          SET error_msg = 'SQL error occurred during cleanup';

        -- Check if payment_sessions table exists
        SELECT COUNT(*)
        INTO table_exists
        FROM information_schema.TABLES
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = 'payment_sessions';

        -- If table doesn't exist, return informative message
        IF table_exists = 0 THEN
          SELECT 'Cleanup skipped: payment_sessions table does not exist' AS result,
                 DATABASE() AS current_database;
        ELSE
          -- Table exists, proceed with cleanup
          START TRANSACTION;

          -- Mark expired sessions that are still pending
          UPDATE payment_sessions
          SET session_status = 'expired', updated_at = NOW()
          WHERE session_status = 'pending'
            AND expires_at < NOW();
          SET expired_count = ROW_COUNT();

          -- Delete very old expired sessions (older than 30 days)
          DELETE FROM payment_sessions
          WHERE session_status = 'expired'
            AND created_at < DATE_SUB(NOW(), INTERVAL 30 DAY);
          SET deleted_count = ROW_COUNT();

          -- Check for errors
          IF error_msg != '' THEN
            ROLLBACK;
            SELECT CONCAT('Cleanup failed: ', error_msg) AS result;
          ELSE
            COMMIT;
            SELECT CONCAT('Cleanup completed successfully: Expired ', expired_count, ' pending session(s), Deleted ', deleted_count, ' old session(s)') AS result,
                   'success' AS status;
          END IF;
        END IF;
    END
    ";
    
    DB::statement($sql);
    echo "   ✓ Procedure created successfully\n\n";
    
    // Test the procedure
    echo "3. Testing the procedure...\n";
    $result = DB::select('CALL cleanup_expired_payment_sessions()');
    
    if (!empty($result)) {
        echo "   ✓ Test result: " . $result[0]->result . "\n";
    } else {
        echo "   ✓ Procedure executed (no result returned)\n";
    }
    
    echo "\n";
    echo "=== IMPROVEMENTS MADE ===\n";
    echo "✓ Fixed ROW_COUNT() issue - now captures both UPDATE and DELETE counts\n";
    echo "✓ Added transaction support for data integrity\n";
    echo "✓ Added error handling with SQLEXCEPTION handler\n";
    echo "✓ Improved result message with detailed counts\n";
    echo "✓ Proper rollback on errors\n";
    echo "\n";
    
    echo "✅ PROCEDURE FIXED AND APPLIED TO DATABASE!\n\n";
    
    echo "You can now run it anytime with:\n";
    echo "  CALL cleanup_expired_payment_sessions();\n";
    
} catch (\Exception $e) {
    echo "\n✗ Error: " . $e->getMessage() . "\n";
    echo "\nNote: If 'payment_sessions' table doesn't exist, create it first.\n";
}
