<?php

class ProductController
{
    private ProductGateway $gateway;

    public function __construct(ProductGateway $gateway)
    {
        $this->gateway = $gateway;
    }

    public function processRequest(string $method, ?string $value): void
    {
        if (is_numeric($value)) {
            $id = $value;
            $category = null;
        } else {
            $id = null;
            $category = $value;
        }

        if ($id) {
            $this->processResourceRequest($method, $id);
        } elseif ($category) {
            $this->processCallectionByCategoryRequest($method, $category);
        } else {
            $this->processCollectionRequest($method);
        }
    }

    private function processResourceRequest(string $method, string $id): void
    {
        $product = $this->gateway->get($id);

        if (!$product) {
            http_response_code(404);
            echo json_encode([
                'message' => 'Product not found'
            ]);
            return;
        }

        switch ($method) {
            case 'GET':
                echo json_encode($product);
                break;
            case 'PATCH':
                $data = (array) json_decode(file_get_contents('php://input'), true);
                $errors = $this->getValidationError($data, false);

                if (!empty($errors)) {
                    http_response_code(422);
                    echo json_encode([
                        'errors' => $errors
                    ]);
                    break;
                }

                $rows = $this->gateway->update($product, $data);
                echo json_encode([
                    'message' => "Product $id updated",
                    'rows' => $rows
                ]);
                break;
            case 'DELETE':
                $rows = $this->gateway->delete($id);

                echo json_encode([
                    'message' => "Product $id deleted",
                    'rows' => $rows
                ]);
                break;
            default:
                http_response_code(405);
                header('Allow: GET, PATCH, DELETE');
                break;
        }
    }

    private function processCollectionRequest(string $method): void
    {
        switch ($method) {
            case 'GET':
                echo json_encode($this->gateway->getAll());
                break;
            case 'POST':
                $data = (array) json_decode(file_get_contents('php://input'), true);
                $errors = $this->getValidationError($data);

                if (!empty($errors)) {
                    http_response_code(422);
                    echo json_encode([
                        'errors' => $errors
                    ]);
                    break;
                }

                $id = $this->gateway->create($data);
                http_response_code(201);
                if (!empty($data['category']))
                    $message = "Product $id created with category {$data['category']}";
                else
                    $message = "Product $id created without category";
                echo json_encode([
                    'message' => $message,
                    'id' => $id
                ]);
                break;
            default:
                http_response_code(405);
                header('Allow: GET, POST');
                break;
        }
    }

    private function processCallectionByCategoryRequest(string $method, string $category): void
    {
        switch ($method) {
            case 'GET':
                echo json_encode($this->gateway->getAllByCategory($category));
                break;
            default:
                http_response_code(405);
                header('Allow: GET');
                break;
        }
    }

    private function getValidationError(array $data, bool $is_new = true): array
    {
        $errors = [];
        if ($is_new && empty($data['name'])) {
            $errors['name'] = 'Name is required';
        }

        if (array_key_exists('price', $data)) {
            if (filter_var($data['price'], FILTER_VALIDATE_FLOAT) === false) {
                $errors['price'] = 'Price must be a float';
            }
        }

        return $errors;
    }
}