<?php
// Test connessione database semplice
$host = '127.0.0.1';
$db = 'regis';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✓ Connessione al database '$db' riuscita!\n";
    
    // Verifica se la tabella users esiste
    $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
    if ($stmt->rowCount() > 0) {
        echo "✓ La tabella 'users' esiste già\n";
    } else {
        echo "✗ La tabella 'users' non esiste ancora\n";
    }
    
    // Mostra tutte le tabelle
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "\nTabelle nel database '$db':\n";
    if (empty($tables)) {
        echo "  (nessuna tabella)\n";
    } else {
        foreach ($tables as $table) {
            echo "  - $table\n";
        }
    }
    
} catch (PDOException $e) {
    echo "✗ Errore connessione: " . $e->getMessage() . "\n";
    
    // Suggerimenti
    if (strpos($e->getMessage(), 'Unknown database') !== false) {
        echo "\nIl database '$db' non esiste. Crealo con:\n";
        echo "mysql -u root -e \"CREATE DATABASE regis CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;\"\n";
    }
}
