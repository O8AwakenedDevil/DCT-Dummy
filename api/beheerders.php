<?php
// GET /api/beheerders.php — returns all beheerders as JSON array
declare(strict_types=1);
require_once __DIR__ . '/_config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendJson(['error' => 'Method not allowed'], 405);
}

$stmt = $pdo->query(
    'SELECT id, naam, woonplaats, volgorde, foto, begonnen,
            eerste_pijlen AS eerstePijlen,
            huidige_pijlen AS huidigePijlen,
            favoriete_merk AS favorieteMerk,
            favoriete_pro AS favorietePro,
            tip
     FROM beheerders
     ORDER BY volgorde ASC, id ASC'
);

sendJson($stmt->fetchAll());
