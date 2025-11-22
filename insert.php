<?php
require_once __DIR__ . "/db.php";

$db = (new Database())->getConnection();

if (isset($_POST['name'])) {
    $name = trim($_POST['name']);
    $parentName = trim($_POST['parentName']);

    // Validate name
    if ($name == "" || !preg_match("/^[A-Za-z ]+$/", $name)) {
        echo json_encode(["success" => false, "message" => "Invalid name"]);
        exit;
    }
    $parentId = null;
    if ($parentName != "") {
        $stmt = $db->prepare("SELECT Id FROM Members WHERE Name = :pname LIMIT 1");
        $stmt->bindParam(":pname", $parentName);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $parentId = $row['Id'];
        }
    }

   //inserting  the member 
    $query = "INSERT INTO Members (Name, ParentId, CreatedDate) VALUES (:name, :parentId, NOW())";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":name", $name);
    $stmt->bindParam(":parentId", $parentId);

    if ($stmt->execute()) {
        echo json_encode([
            "success" => true,
            "id" => $db->lastInsertId(),
            "name" => $name,
            "parentId" => $parentId
        ]);
    } else {
        echo json_encode(["success" => false, "message" => "Insert failed"]);
    }
}
?>