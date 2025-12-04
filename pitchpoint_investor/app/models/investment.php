<?php // Opening PHP tag to start PHP code execution

class Investment extends Model { // Define the Investment class that extends the base Model class

    public function create(int $investorId, int $projectId, float $amount, string $method='card') { // Define method to create a new investment record

        $sql="INSERT INTO investments(investor_id,project_id,amount,payment_method) VALUES(?,?,?,?)"; // Build SQL INSERT statement to insert investment data

        $this->db->prepare($sql)->execute([$investorId,$projectId,$amount,$method]); // Prepare and execute the SQL statement with parameter values

        return $this->db->lastInsertId(); // Return the ID of the last inserted row

    }

}
