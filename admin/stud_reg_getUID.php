<?php
require_once '../dbcon.php'; // Ensure this file sets up $pdo correctly

try {
    // Prepare SQL query
    // NOTE: ORDER BY should come before LIMIT
    $stmt = $pdo->prepare("
        SELECT id, card_uid 
        FROM students 
        WHERE card_select = 1 
        ORDER BY last_update DESC 
        LIMIT 1
    ");

    $stmt->execute();

    // Fetch the row as an associative array
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    // Set JSON header
    header('Content-Type: application/json; charset=utf-8');

    // Output JSON (empty object if no result)
    echo json_encode($row ?: new stdClass(), JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    // Handle database errors gracefully
    http_response_code(500);
    echo json_encode([
        'error' => 'Database query failed',
        'details' => $e->getMessage()
    ]);
}
?>