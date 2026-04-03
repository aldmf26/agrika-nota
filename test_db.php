<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=agrika_nota', 'root', '');

    $result = $pdo->query('SELECT COUNT(*) as cnt FROM users');
    $row = $result->fetch(PDO::FETCH_ASSOC);
    echo "Users: " . $row['cnt'] . "\n";

    $result = $pdo->query('SELECT COUNT(*) as cnt FROM notas');
    $row = $result->fetch(PDO::FETCH_ASSOC);
    echo "Notas: " . $row['cnt'] . "\n";

    $result = $pdo->query('SELECT email FROM users LIMIT 5');
    echo "Users in DB:\n";
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        echo "  - " . $row['email'] . "\n";
    }
} catch (Exception $e) {
    echo 'Database Error: ' . $e->getMessage();
}
