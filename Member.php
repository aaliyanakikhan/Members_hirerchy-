<?php
class Member {
    private $conn;
    private $table_name = "Members";

    public function __construct($db) {
        $this->conn = $db;
    }

    // recursive function to build tree
    public function buildTree($parentId = null) {
        
        $query = "SELECT Id, Name FROM " . $this->table_name . " WHERE ParentId " . 
                 ($parentId === null ? "IS NULL" : "= :parentId") . " ORDER BY Name ASC";
        $stmt = $this->conn->prepare($query);

        if ($parentId !== null) {
            $stmt->bindParam(":parentId", $parentId, PDO::PARAM_INT);
        }

        $stmt->execute();
        $members = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($members) > 0) {
            echo "<ul>";
            foreach ($members as $member) {
               
                echo "<li id='member-" . $member['Id'] . "'>" . htmlspecialchars($member['Name']);
                
                // recursive call for children of this member
                $this->buildTree($member['Id']);
                
                echo "</li>";
            }
            echo "</ul>";
        }
    }
}
?>