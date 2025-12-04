<?php // Opening PHP tag to start PHP code execution

class Investor extends Model // Define the Investor class that extends the base Model class
{
    /**
     * Find investor row by user_id
     * Table: investors (investor_id, user_id, interest_area, created_at)
     */
    public function byUserId(int $userId) // Define method to find investor by user ID
    {
        $sql = "SELECT * FROM investors WHERE user_id = ?"; // Build SQL query to select all columns from investors table where user_id matches
        $stmt = $this->db->prepare($sql); // Prepare the SQL statement
        $stmt->execute([$userId]); // Execute the prepared statement with user ID parameter
        return $stmt->fetch(); // Return the first row as an associative array, or false if not found
    }

    /**
     * Create investor record for a user_id if it doesn't exist
     * Returns the investor record (newly created or existing)
     */
    public function createIfNotExists(int $userId) // Define method to create investor record if it doesn't exist
    {
        // Check if investor record already exists
        $existing = $this->byUserId($userId); // Check if investor record already exists for this user ID
        if ($existing) { // Check if existing record was found
            return $existing; // Return the existing record if found
        }

        // Create new investor record
        $sql = "INSERT INTO investors (user_id, created_at) VALUES (?, NOW())"; // Build SQL INSERT statement to create new investor record with current timestamp
        $stmt = $this->db->prepare($sql); // Prepare the SQL statement
        $stmt->execute([$userId]); // Execute the prepared statement with user ID parameter

        // Return the newly created record
        return $this->byUserId($userId); // Return the newly created investor record by fetching it again
    }

    /**
     * Projects the investor has already invested in
     * Tables:
     *   investments (investment_id, investor_id, project_id, amount, payment_method, investment_date)
     *   projects    (project_id, title, summary, ...)
     */
   public function projectsInvested(int $investorId): array // Define method to get all projects the investor has invested in
{
    $sql = " 
        SELECT  
            p.*, 
            i.amount,
            i.investment_date, 
            i.payment_method, 
            c.category_name, 
            (SELECT storage_path FROM project_files  
             WHERE project_id = p.project_id 
               AND mime_type LIKE 'image/%'  
               AND storage_path LIKE '%cover_image%'
             ORDER BY created_at ASC LIMIT 1) AS cover_image 
        FROM investments i 
        INNER JOIN projects p ON p.project_id = i.project_id 
        LEFT JOIN categories c ON p.category_id = c.category_id 
        WHERE i.investor_id = ? 
        ORDER BY i.investment_date DESC ";
     // End of SQL query string
    $stmt = $this->db->prepare($sql); // Prepare the SQL statement
    $stmt->execute([$investorId]); // Execute the prepared statement with investor ID parameter
    return $stmt->fetchAll(); // Return all results as an array of associative arrays
}


    /**
     * Projects the investor is interested in (watchlist)
     * Tables:
     *   project_interests (interest_id, project_id, investor_id, note, created_at)
     *   projects          (project_id, title, summary, ...)
     */
    public function projectsInterested(int $investorId): array // Define method to get all projects the investor is interested in
    {
        $sql = " 
            SELECT p.* 
            FROM projects p 
            INNER JOIN project_interests pi 
                ON pi.project_id = p.project_id 
            WHERE pi.investor_id = ? 
            ORDER BY pi.created_at DESC 
        "; // End of SQL query string
        $stmt = $this->db->prepare($sql); // Prepare the SQL statement
        $stmt->execute([$investorId]); // Execute the prepared statement with investor ID parameter
        return $stmt->fetchAll(); // Return all results as an array of associative arrays
    }
}
