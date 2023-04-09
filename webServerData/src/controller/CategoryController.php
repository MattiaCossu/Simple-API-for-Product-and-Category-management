<?php

class CategoryController
{
    private CategoryGateway $gateway;

    public function __construct(private Database $db)
    {
        $this->gateway = new CategoryGateway($db);
    }

    public function processRequest(string $method, ?string $id): void
    {
        if ($id) {
            $this->processResourceRequest($method, $id);
        } else {
            $this->processCollectionRequest($method);
        }
    }

    private function processResourceRequest(string $method, string $id): void
    {
        $category = $this->gateway->get($id);

        if (!$category) {
            http_response_code(404);
            echo json_encode([
                'message' => 'Category not found'
            ]);
            return;
        }

        switch ($method) {
            case 'GET':
                echo json_encode($category);
                break;
            default:
                http_response_code(405);
                echo json_encode([
                    'message' => 'Method not allowed'
                ]);
                break;
        }
    }

    private function processCollectionRequest(string $method): void
    {
        switch ($method) {
            case 'GET':
                echo json_encode($this->gateway->getAll());
                break;
            default:
                http_response_code(405);
                echo json_encode([
                    'message' => 'Method not allowed'
                ]);
                break;
        }
    }
}