<?php // Opening PHP tag to start PHP code execution

class Interest extends Model { // Define the Interest class that extends the base Model class

    public function has(int $investorId, int $projectId): bool { // Define method to check if investor has interest in a project

        $q=$this->db->prepare("SELECT interest_id FROM project_interests WHERE investor_id=? AND project_id=?"); // Prepare SQL query to check for existing interest record

        $q->execute([$investorId,$projectId]); // Execute the prepared statement with investor ID and project ID parameters

        return (bool)$q->fetch(); // Return true if interest record exists, false otherwise (cast to boolean)

    }

    public function toggle(int $investorId, int $projectId) { // Define method to toggle interest status (add if not exists, remove if exists)

        if ($this->has($investorId,$projectId)) { // Check if interest already exists

            $q=$this->db->prepare("DELETE FROM project_interests WHERE investor_id=? AND project_id=?"); // Prepare SQL DELETE statement to remove interest

            $q->execute([$investorId,$projectId]); // Execute the prepared statement with investor ID and project ID parameters

            return false; // Return false to indicate interest was removed

        } else { // If interest does not exist

            $q=$this->db->prepare("INSERT INTO project_interests(project_id,investor_id) VALUES(?,?)"); // Prepare SQL INSERT statement to add interest

            $q->execute([$projectId,$investorId]); // Execute the prepared statement with project ID and investor ID parameters
            return true; // Return true to indicate interest was added

        }

    }

}
