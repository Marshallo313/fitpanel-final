<?php
session_start();
require_once "../php/db.php";
$pageTitle = "Calories";
include "header.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$product = "";
$grams = 100;
$kcal_per_100g = null;
$kcal_total = null;
$message = "";

$selected_date = $_GET['date'] ?? date("Y-m-d");

// Obsługa wyszukiwania
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["search"])) {
    $product = trim($_POST["product"]);
    $grams = intval($_POST["grams"]);

    if (!empty($product)) {
        $query = urlencode($product);
        $url = "https://world.openfoodfacts.org/cgi/search.pl?search_terms=$query&search_simple=1&action=process&json=1";

        $response = @file_get_contents($url);
        $data = json_decode($response, true);

        if (!empty($data["products"][0]["nutriments"]["energy-kcal_100g"])) {
            $kcal_per_100g = floatval($data["products"][0]["nutriments"]["energy-kcal_100g"]);
            $kcal_total = round($kcal_per_100g * $grams / 100, 2);
        } else {
            $message = "Nie znaleziono kalorii dla: <strong>$product</strong>";
        }
    }
}

// Obsługa dodawania
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["add"])) {
    $product = trim($_POST["product"]);
    $grams = intval($_POST["grams"]);
    $kcal_total = floatval($_POST["kcal_total"]);

    $stmt = $pdo->prepare("INSERT INTO calorie_log (user_id, product_name, grams, kcal) VALUES (?, ?, ?, ?)");
    $stmt->execute([$_SESSION["user_id"], $product, $grams, $kcal_total]);

    $message = "Dodano do dzienniczka: <strong>$product</strong> ($grams g, $kcal_total kcal)";
}

// Pobieranie danych z wybranego dnia
$stmt = $pdo->prepare("SELECT * FROM calorie_log WHERE user_id = ? AND log_date = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION["user_id"], $selected_date]);
$entries = $stmt->fetchAll();

$total_kcal_today = array_sum(array_column($entries, 'kcal'));
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Dzienniczek kalorii</title>
</head>
<body>
    <h2>Dzienniczek kalorii</h2>

    <form method="post">
        <label>Produkt:<br>
            <input type="text" name="product" required value="<?= htmlspecialchars($product) ?>">
        </label><br><br>

        <label>Ilość (g):<br>
            <input type="number" name="grams" required min="1" max="1000" value="<?= $grams ?>">
        </label><br><br>

        <button type="submit" name="search">Szukaj</button>
    </form>

    <?php if ($kcal_per_100g): ?>
        <p><strong><?= htmlspecialchars($product) ?></strong> ma <strong><?= $kcal_per_100g ?></strong> kcal / 100g.</p>
        <p>Dla <?= $grams ?>g to będzie: <strong><?= $kcal_total ?> kcal</strong></p>

        <form method="post">
            <input type="hidden" name="product" value="<?= htmlspecialchars($product) ?>">
            <input type="hidden" name="grams" value="<?= $grams ?>">
            <input type="hidden" name="kcal_total" value="<?= $kcal_total ?>">
            <button type="submit" name="add">Dodaj do dzienniczka</button>
        </form>
    <?php endif; ?>

    <?php if ($message): ?>
        <p style="color:green"><?= $message ?></p>
    <?php endif; ?>

    <hr>
    <h3>Wybierz dzień</h3>
    <form method="get">
        <input type="date" name="date" value="<?= $selected_date ?>">
        <button type="submit">Pokaż</button>
    </form>

    <h3>Kalorie dla: <?= date("d.m.Y", strtotime($selected_date)) ?></h3>

    <?php if (count($entries) > 0): ?>
        <table border="1" cellpadding="5">
            <tr><th>Produkt</th><th>Ilość (g)</th><th>Kalorie</th><th>Godzina</th></tr>
            <?php foreach ($entries as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row["product_name"]) ?></td>
                    <td><?= $row["grams"] ?></td>
                    <td><?= $row["kcal"] ?></td>
                    <td>
                        <?= date("H:i", strtotime($row["created_at"])) ?>
                        <a href="php/delete_entry.php?id=<?= $row["id"] ?>" style="color:red; margin-left:10px;" onclick="return confirm('Na pewno usunąć?')">🗑️</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <tr><td colspan="2"><strong>Łącznie</strong></td><td colspan="2"><strong><?= round($total_kcal_today, 2) ?> kcal</strong></td></tr>
        </table>
    <?php else: ?>
        <p>Brak wpisów na wybrany dzień.</p>
    <?php endif; ?>

<div class="d-flex justify-content-end mt-3">
  <a href="profile.php" class="btn btn-outline-primary btn-sm">Powrót do profilu</a>
</div>
    <!-- (opcjonalnie) Autocomplete – JS -->
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const input = document.querySelector('input[name="product"]');
        const list = document.createElement("ul");
        list.style.border = "1px solid #ccc";
        list.style.position = "absolute";
        list.style.backgroundColor = "#fff";
        list.style.zIndex = "1000";
        list.style.listStyle = "none";
        list.style.padding = "0";
        list.style.margin = "0";
        list.style.maxHeight = "200px";
        list.style.overflowY = "auto";
        list.style.width = input.offsetWidth + "px";
        list.hidden = true;
        input.parentNode.appendChild(list);

        input.addEventListener("input", function() {
            const term = input.value.trim();
            if (term.length < 2) {
                list.innerHTML = "";
                list.hidden = true;
                return;
            }

            fetch("autocomplete.php?term=" + encodeURIComponent(term))
                .then(res => res.json())
                .then(data => {
                    list.innerHTML = "";
                    if (data.length > 0) {
                        data.forEach(item => {
                            const li = document.createElement("li");
                            li.textContent = item;
                            li.style.padding = "5px";
                            li.style.cursor = "pointer";
                            li.addEventListener("click", () => {
                                input.value = item;
                                list.innerHTML = "";
                                list.hidden = true;
                            });
                            list.appendChild(li);
                        });
                        list.hidden = false;
                    } else {
                        list.hidden = true;
                    }
                });
        });

        document.addEventListener("click", function(e) {
            if (!list.contains(e.target) && e.target !== input) {
                list.innerHTML = "";
                list.hidden = true;
            }
        });
    });
    </script>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php include "footer.php"; ?>

</html>
