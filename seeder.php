<?php
require_once __DIR__ . '/vendor/autoload.php';

use App\Database\Database;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$pdo = Database::getConnection();

$json = file_get_contents(__DIR__ . '/data.json');
$data = json_decode($json, true);

if (!$data) {
    die("Could not decode JSON.");
}

// Disable foreign key checks for safe truncation
$pdo->exec("SET FOREIGN_KEY_CHECKS=0;");
$pdo->exec("TRUNCATE TABLE attribute_items;");
$pdo->exec("TRUNCATE TABLE attributes;");
$pdo->exec("TRUNCATE TABLE prices;");
$pdo->exec("TRUNCATE TABLE galleries;");
$pdo->exec("TRUNCATE TABLE products;");
$pdo->exec("SET FOREIGN_KEY_CHECKS=1;");

// Seed products and related tables
foreach ($data['data']['products'] as $product) {
    // Insert product
    $stmt = $pdo->prepare(
        "INSERT INTO products (id, name, description, category, brand) VALUES (?, ?, ?, ?, ?)"
    );
    $stmt->execute([
        $product['id'],
        $product['name'],
        $product['description'],
        $product['category'],
        $product['brand'],
    ]);
    foreach ($product['prices'] as $price) {
        echo "Seeding price: " . $price['amount'] . " for product: " . $product['name'] . "\n"; // DEBUG
        $stmt = $pdo->prepare(
            "INSERT INTO prices (product_id, currency_label, currency_symbol, amount) VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([
            $product['id'],
            $price['currency']['label'],
            $price['currency']['symbol'],
            $price['amount'],
        ]);
    }

    // Insert prices
    foreach ($product['prices'] as $price) {
        $stmt = $pdo->prepare(
            "INSERT INTO prices (product_id, currency_label, currency_symbol, amount) VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([
            $product['id'],
            $price['currency']['label'],
            $price['currency']['symbol'],
            $price['amount'],
        ]);
    }

    // Insert gallery images
    foreach ($product['gallery'] as $img) {
        $stmt = $pdo->prepare(
            "INSERT INTO galleries (product_id, image_url) VALUES (?, ?)"
        );
        $stmt->execute([
            $product['id'],
            $img,
        ]);
    }

    // Insert attributes and items
    foreach ($product['attributes'] as $attr) {
        $stmt = $pdo->prepare(
            "INSERT INTO attributes (id, product_id, name, type) VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([
            $attr['id'],
            $product['id'],
            $attr['name'],
            $attr['type'],
        ]);

        foreach ($attr['items'] as $item) {
            $stmt = $pdo->prepare(
                "INSERT INTO attribute_items (attribute_id, display_value, value, item_id) VALUES (?, ?, ?, ?)"
            );
            $stmt->execute([
                $attr['id'],
                $item['displayValue'],
                $item['value'],
                $item['id'],
            ]);
        }
    }
}

echo "Seeding complete!\n";
