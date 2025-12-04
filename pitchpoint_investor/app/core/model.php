<?php // Opening PHP tag to start PHP code execution
abstract class Model { // Define an abstract base model class that all models will extend
    protected PDO $db; // Declare a protected PDO property to store the database connection

    public function __construct() { // Define the constructor method that runs when a model is instantiated
        // uses the global helper from app/config/database.php
        $this->db = db(); // Initialize the database connection using the global db() helper function
    }
}


