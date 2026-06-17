<?php
// GET /api/wedstrijden.php?week=N — returns all matches for that week as JSON array
declare(strict_types=1);
require_once __DIR__ . '/_config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendJson(['error' => 'Method not allowed'], 405);
}

$week = filter_input(INPUT_GET, 'week', FILTER_VALIDATE_INT, [
    'options' => ['min_range' => 1, 'max_range' => 9],
]);

if ($week === null || $week === false) {
    sendJson(['error' => 'Invalid or missing "week" parameter (must be 1-9)'], 400);
}

$stmt = $pdo->prepare(
    'SELECT id, klasse, speler1, speler2, datumTijd, uur
     FROM wedstrijden
     WHERE week = :week
     ORDER BY klasse ASC, datumTijd ASC, uur ASC'
);
$stmt->execute(['week' => $week]);

sendJson($stmt->fetchAll());
