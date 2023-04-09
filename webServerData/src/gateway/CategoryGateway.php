<?php

class CategoryGateway
{
    private Pdo $conn;

    public function __construct(private Database $db)
    {
        $this->conn = $db->getConnection();
    }

    public function getAll(): array 
    {
        $sql = 'SELECT * FROM categories';
        $stmp = $this->conn->query($sql);
        $data = [];
        while ($row = $stmp->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }
        return $data;
    }

    public function get(string $id): array | false 
    {
        $sql = 'SELECT * FROM categories WHERE id = :id';
        $statement = $this->conn->prepare($sql);
        $statement->bindValue(":id", $id, PDO::PARAM_INT);
        $statement->execute();
        $data = $statement->fetch(PDO::FETCH_ASSOC);
        return $data;
    }
}