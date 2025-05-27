<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Étiquettes EAN13</title>
    <style>
        body { font-family: Arial, sans-serif; }
        h1 { margin-bottom: 20px; }
        .article { margin-bottom: 10px; }
        input[type="text"] {
            width: 250px;
            padding: 5px;
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <h1>Générateur d’étiquettes (EAN13) – 24 articles</h1>

    <form action="generate.php" method="post">
        <?php for ($i = 1; $i <= 24; $i++): ?>
            <div class="article">
                <label>Article <?= $i ?> :</label>
                <input type="text" name="nom[]" placeholder="Nom de l’article">
                <input type="text" name="code[]" placeholder="Code EAN13 (12 ou 13 chiffres)">
            </div>
        <?php endfor; ?>

        <br>
        <button type="submit">Générer le PDF</button>
    </form>
</body>
</html>
