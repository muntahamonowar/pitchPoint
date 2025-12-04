<?php
// Start of the PHP file.

declare(strict_types=1);
// Strict typing enabled for safer, clearer code.

require_once __DIR__ . '/BaseModel.php';
// Include the BaseModel file from the same directory.
// __DIR__ gives the current folder path.
// This lets Administrator extend BaseModel and use BaseModel::db().

class Administrator extends BaseModel
{
    // Administrator is a model class representing rows in the "administrator" table.
    // It extends BaseModel, so it can use the protected static db() method.

    public static function findActiveByEmail(string $email): ?array
    {
        // Public static method: can be called as Administrator::findActiveByEmail($email).
        // It returns either an associative array (row data) or null.
        // The parameter $email must be a string.

        $stmt = self::db()->prepare("
            SELECT * FROM administrator
            WHERE email = ? AND is_active = 1
            LIMIT 1
        ");
        // self::db() calls the db() method we inherited from BaseModel.
        // ->prepare(...) prepares a SQL statement with placeholders (here a single "?").
        // This SQL selects one active administrator with the given email address.

        $stmt->execute([$email]);
        // Execute the prepared statement, passing an array of values
        // to replace the "?" placeholder. Here, the first "?" becomes $email.

        $row = $stmt->fetch();
        // Fetch the first matching row from the database as an associative array.
        // If no row is found, $row will be false.

        return $row ?: null;
        // If $row is truthy (an array), return it.
        // If $row is false (no row found), return null instead.
    }
}
