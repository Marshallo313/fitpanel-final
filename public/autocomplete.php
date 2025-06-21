<?php
// Autocomplete dla OpenFoodFacts – język: polski

header('Content-Type: application/json');

if (!isset($_GET['term']) || strlen($_GET['term']) < 2) {
    echo json_encode([]);
    exit;
}

$query = urlencode($_GET['term']);
$url = "https://world.openfoodfacts.org/cgi/search.pl?search_terms=$query&search_simple=1&action=process&json=1";

// Pobieramy dane z API
$response = @file_get_contents($url);
if (!$response) {
    echo json_encode([]);
    exit;
}

$data = json_decode($response, true);
$suggestions = [];

if (!empty($data["products"])) {
    foreach ($data["products"] as $product) {
        // Preferuj nazwę polską, jeśli dostępna
        if (!empty($product["product_name_pl"])) {
            $name = $product["product_name_pl"];
        } elseif (!empty($product["product_name"])) {
            $name = $product["product_name"];
        } else {
            continue;
        }

        // Dodaj do listy, unikaj dupli
        if (!in_array($name, $suggestions)) {
            $suggestions[] = $name;
        }

        // Limit do 10 sugestii
        if (count($suggestions) >= 10) break;
    }
}

echo json_encode($suggestions);
