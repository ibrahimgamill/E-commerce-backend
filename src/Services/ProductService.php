<?php

namespace App\Services;

use App\Database\Database;

class ProductService
{
    public function getAllProducts()
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->query("SELECT * FROM products");
        $products = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return array_map([$this, 'hydrateProduct'], $products);
    }

    public function getProductsByCategory($category)
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM products WHERE category = ?");
        $stmt->execute([$category]);
        $products = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return array_map([$this, 'hydrateProduct'], $products);
    }

    public function getProductById($id)
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $product = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $product ? $this->hydrateProduct($product) : null;
    }

    public function getCategories()
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->query("SELECT DISTINCT category as name FROM products");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function hydrateProduct($product)
    {
        $product['gallery'] = $this->getProductGallery($product['id']);
        $product['inStock'] = (bool)($product['in_stock'] ?? true);
        $product['description'] = $product['description'];
        $product['category'] = $product['category'];
        $product['brand'] = $product['brand'];

        // Optionally, get attributes from a table or set as []
        $product['attributes'] = [];

        $product['prices'] = [
            [
                'amount' => $product['price'],
                'currency' => [
                    'label' => 'USD',
                    'symbol' => '$'
                ]
            ]
        ];

        return $product;
    }


    private function getProductGallery($productId)
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT url FROM product_images WHERE product_id = ?");
        $stmt->execute([$productId]);
        $urls = $stmt->fetchAll(\PDO::FETCH_COLUMN);
        // If no images, show a placeholder
        return $urls ?: ['https://via.placeholder.com/200x140?text=No+Image'];
    }
}
