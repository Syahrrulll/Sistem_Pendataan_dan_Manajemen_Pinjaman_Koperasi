<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Koperasi Digital' ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/core.css">
    <?php if (isset($css)): ?>
        <?php if (is_array($css)): ?>
            <?php foreach ($css as $file): ?>
                <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/<?= $file ?>">
            <?php endforeach; ?>
        <?php else: ?>
            <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/<?= $css ?>">
        <?php endif; ?>
    <?php endif; ?>
</head>
<body>