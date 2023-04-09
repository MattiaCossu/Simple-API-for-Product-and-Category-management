<?php

class ProductGateway
{
    private Pdo $conn;

    public function __construct(private Database $db)
    {
        $this->conn = $db->getConnection();
    }

    public function getAll(): array 
    {
        $sql = 'SELECT * FROM products';
        $stmp = $this->conn->query($sql);
        $data = [];
        while ($row = $stmp->fetch(PDO::FETCH_ASSOC)) {
            $row['is_available'] = (bool) $row['is_available'];
            $data[] = $row;
        }
        return $data;
    }

    public function getAllByCategory(string $category): array 
    {
        $sql = "SELECT p.* 
                FROM products p 
                JOIN product_categories pc ON p.id = pc.product_id 
                JOIN categories c ON c.id = pc.category_id 
                WHERE c.name = :category";
        $statement = $this->conn->prepare($sql);
        $statement->bindValue(":category", $category, PDO::PARAM_STR);
        $statement->execute();
        $data =  [];
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $row['is_available'] = (bool) $row['is_available'];
            $data[] = $row;
        }
        return $data;
    }

    public function get(string $id): array | false 
    {
        $sql = 'SELECT * FROM products WHERE id = :id';
        $statement = $this->conn->prepare($sql);
        $statement->bindValue(":id", $id, PDO::PARAM_INT);
        $statement->execute();
        $data = $statement->fetch(PDO::FETCH_ASSOC);
        
        if ($data !== false)
            $data['is_available'] = (bool) $data['is_available'];
            
        return $data;
    }

    public function create(array $data): string
    {
        $sql = 'INSERT INTO products (name, price, is_available, image) 
                VALUES (:name, :price, :is_available, :image)';
        $statement = $this->conn->prepare($sql);
        $statement->bindValue(":name", $data['name'], PDO::PARAM_STR);
        $statement->bindValue(":price", $data['price'] ?? 0, PDO::PARAM_STR);
        $statement->bindValue(":is_available", $data['is_available'] ?? false, PDO::PARAM_BOOL);
        $statement->bindValue(":image", $data['image'] ?? "https://via.placeholder.com/300x300", PDO::PARAM_STR);

        $statement->execute();
        $id = $this->conn->lastInsertId();
        if (!empty($data['category'])) {
            $this->addToCategory($this->conn->lastInsertId(), $data['category']);
        }
        return $id;
    }

    public function update(array $corrent, array $new): int
    {
        $sql = 'UPDATE products 
                SET name = :name, price = :price, is_available = :is_available, image = :image 
                WHERE id = :id';
        
        $statement = $this->conn->prepare($sql);
        $statement->bindValue(":name", $new['name'] ?? $corrent['name'], PDO::PARAM_STR);
        $statement->bindValue(":price", $new['price'] ?? $corrent['price'], PDO::PARAM_STR);
        $statement->bindValue(":is_available", $new['is_available'] ?? $corrent['is_available'], PDO::PARAM_BOOL);
        $statement->bindValue(":image", $new['image'] ?? $corrent['image'], PDO::PARAM_STR);
        $statement->bindValue(":id", $corrent['id'], PDO::PARAM_INT);

        $statement->execute();
        $row = $statement->rowCount();
        
        if (!empty($new['category'])) 
            $this->addToCategory($corrent['id'], $new['category']);
            $row = $corrent['id'];
        return $row;
    }

    public function delete(string $id): int
    {
        $sql = 'DELETE FROM products
                WHERE id = :id';
        $statement = $this->conn->prepare($sql);
        $statement->bindValue(":id", $id, PDO::PARAM_INT);
        $statement->execute();
        return $statement->rowCount();
    }

    public function addToCategory(string $productId, string $categoryName): void 
    {
        $sql = 'INSERT INTO product_categories (product_id, category_id) 
                SELECT :product_id, id 
                FROM categories 
                WHERE name = :category_name'; 
        $statement = $this->conn->prepare($sql);
        $statement->bindValue(":product_id", $productId, PDO::PARAM_INT);
        $statement->bindValue(":category_name", $categoryName, PDO::PARAM_STR);
        $statement->execute();
        $count = $statement->rowCount();
        if ($count > 0)
            return;
        else 
            throw new Exception("Category not found");
    }   
    

}