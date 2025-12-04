<?php // Opening PHP tag to start PHP code execution



class Project extends Model // Define the Project class that extends the base Model class

{ // Opening brace for the class

    /** // Start of docblock comment

     * Fetch categories for the top bar / filter

     * Table: categories (category_id, category_name, description)

     */ // End of docblock comment

    public function categories(): array // Define method to get all categories, returns an array

    { // Opening brace for the method

        $sql = "SELECT category_id, category_name 
                FROM categories 
                ORDER BY category_name"; // Order results by category name alphabetically

        return $this->db->query($sql)->fetchAll(); // Execute the query and return all results as an array of associative arrays

    } // Closing brace for the method



    /** // Start of docblock comment

     * Explore projects for investor/guest view

     *

     * - $search: optional search text

     * - $limit:  optional max number of projects (null = all)

     * - $categoryId: optional category filter

     *

     * Uses your real schema:

     *   projects (status, visibility, title, summary, category_id, ...)

     */ // End of docblock comment

    public function explore(?string $search = null, ?int $limit = null, ?int $categoryId = null): array // Define method to explore projects with optional filters, returns an array

    { // Opening brace for the method

        $sql = " 
            SELECT p.*, c.category_name 
            FROM projects p 
            LEFT JOIN categories c 
              ON p.category_id = c.category_id 
            WHERE p.status = 'published' 
              AND p.visibility = 'public' 
        "; // End of base SQL query string

        $params = []; // Initialize empty array for SQL parameters



        if ($search !== null && $search !== '') { // Check if search parameter is provided and not empty
            $sql .= " AND ( 
                        p.title   LIKE :q 
                     OR p.summary LIKE :q 
                     OR p.problem LIKE :q 
                     OR p.solution LIKE :q 
                     )"; // End of search condition
            $params[':q'] = '%' . $search . '%'; // Add search parameter with wildcards for partial matching
        } // End of search condition block



        if (!empty($categoryId)) { // Check if category ID is provided
            $sql .= " AND p.category_id = :cat"; // Append category filter to SQL query
            $params[':cat'] = $categoryId; // Add category ID to parameters array
        } // End of category filter block



        $sql .= " ORDER BY p.created_at DESC"; // Append ORDER BY clause to sort by creation date (newest first)



        if ($limit !== null) { // Check if limit parameter is provided
            $sql .= " LIMIT " . (int) $limit; // Append LIMIT clause to restrict number of results
        } // End of limit block



        $stmt = $this->db->prepare($sql); // Prepare the SQL statement

        $stmt->execute($params); // Execute the prepared statement with parameters

        $projects = $stmt->fetchAll(); // Fetch all results as an array of associative arrays



        // Fetch cover images for each project
        foreach ($projects as &$project) { // Loop through each project using reference to modify array

            $imgStmt = $this->db->prepare(" 
                SELECT storage_path, mime_type 
                FROM project_files 
                WHERE project_id = ? 
                  AND mime_type LIKE 'image/%' 
                  AND storage_path LIKE '%cover_image%' 
                ORDER BY created_at ASC 
                LIMIT 1 
            "); // End of SQL query

            $imgStmt->execute([$project['project_id']]); // Execute the prepared statement with project ID parameter

            $image = $imgStmt->fetch(); // Fetch the first result as an associative array

            $project['cover_image'] = $image ? $image['storage_path'] : null; // Add cover image path to project array, or null if not found

        } // End of foreach loop

        unset($project); // break reference - Remove reference to prevent accidental modification

        return $projects; // Return the projects array with cover images added

    } // Closing brace for the method



    /** // Start of docblock comment

     * Find one project by ID (for detail page).

     */ // End of docblock comment

    public function find(int $projectId) // Define method to find a single project by ID

    { // Opening brace for the method

        $sql = "SELECT p.*, c.category_name, e.company_name 
                FROM projects p 
                LEFT JOIN categories    c ON c.category_id = p.category_id 
                LEFT JOIN entrepreneurs e ON e.entrepreneur_id = p.entrepreneur_id 
                WHERE p.project_id = :id"; // Filter where project_id matches

        $q = $this->db->prepare($sql); // Prepare the SQL statement

        $q->bindValue(':id', $projectId, PDO::PARAM_INT); // Bind project ID parameter as integer

        $q->execute(); // Execute the prepared statement

        return $q->fetch(); // Return the first row as an associative array, or false if not found

    } // Closing brace for the method

} // Closing brace for the class
