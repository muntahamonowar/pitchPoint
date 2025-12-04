<?php
// Start of the PHP file.

declare(strict_types=1);
// Enable strict types in PHP.
// This means PHP will be more strict about type declarations (e.g. string, int).

abstract class BaseModel
{
    // This class is "abstract", which means you cannot create
    // an object directly from BaseModel.
    // Other model classes (like Administrator) will "extend" this
    // and reuse the db() method.

    protected static function db(): PDO {
        // A protected static method, available to child classes.
        // The return type is PDO, which is PHP's database connection class.

        return db();
        // Calls a global helper function db() (defined elsewhere)
        // that returns a PDO object connected to your database.
    }
}
