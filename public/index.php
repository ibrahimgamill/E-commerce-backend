<?php

if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_SERVER['REQUEST_URI'] === '/health.txt') {
    header('Content-Type: text/plain');
    http_response_code(200);
    echo 'OK';
    exit;
}
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: *");

// Only load Dotenv if .env file exists (for local dev)
$envPath = __DIR__ . '/../.env';
if (file_exists($envPath)) {
    require_once __DIR__ . '/../vendor/autoload.php';
    $dotenv = Dotenv\Dotenv::createImmutable(dirname($envPath));
    $dotenv->load();
} else {
    require_once __DIR__ . '/../vendor/autoload.php';
}

// CORS preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

use App\GraphQL\Schema;
use GraphQL\GraphQL;
use GraphQL\Error\DebugFlag;
use GraphQL\Error\FormattedError;

try {
    $rawInput = file_get_contents('php://input');
    $input = json_decode($rawInput, true);


    // Temporary fallback query for testing
    $query = $input['query'] ?? '{ products { id name } }';  // Change this query to match your schema
    $variables = $input['variables'] ?? null;

    $schema = Schema::build();

    $result = GraphQL::executeQuery($schema, $query, null, null, $variables);
    $output = $result->toArray(DebugFlag::INCLUDE_DEBUG_MESSAGE | DebugFlag::INCLUDE_TRACE);
} catch (\Exception $e) {
    $output = [
        'errors' => [
            FormattedError::createFromException($e)
        ]
    ];
}

header('Content-Type: application/json');
echo json_encode($output);
