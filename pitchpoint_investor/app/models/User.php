<?php // Opening PHP tag to start PHP code execution
class User extends Model { // Define the User class that extends the base Model class
    public function find(int $id) { // Define method to find a user by ID
        $q = $this->db->prepare("SELECT * FROM users WHERE user_id=? AND is_active=1"); // Prepare SQL query to select user where ID matches and user is active
        $q->execute([$id]); // Execute the prepared statement with user ID parameter
        return $q->fetch(); // Return the first row as an associative array, or false if not found
    } // End of find method

    public function findByEmail(string $email) { // Define method to find a user by email address
        $q = $this->db->prepare("SELECT * FROM users WHERE email=? AND is_active=1"); // Prepare SQL query to select user where email matches and user is active
        $q->execute([$email]); // Execute the prepared statement with email parameter
        return $q->fetch(); // Return the first row as an associative array, or false if not found
    } // End of findByEmail method

    public function updateProfile(int $id, string $name, ?string $email, ?string $bio) { // Define method to update user profile information
        $sql = "UPDATE users SET name=?, email=?, bio=? WHERE user_id=?"; // Build SQL UPDATE statement to update user name, email, and bio
        $this->db->prepare($sql)->execute([$name, $email, $bio, $id]); // Prepare and execute the SQL statement with parameter values
    } // End of updateProfile method
} // End of User class
