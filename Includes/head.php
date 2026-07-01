<?php
/** @var string $page_title */
/** @var string $active_page */
/** @var string $asset_path defaults ../assets */
/** @var string $dash_path path prefix to Dashboard/ */
/** @var string $user_path path prefix to user/ */
/** @var bool $include_chart */
$asset_path = $asset_path ?? '../assets';
$dash_path = $dash_path ?? '';
$user_path = $user_path ?? '../user/';
$include_chart = $include_chart ?? false;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> - Smart Finance</title>
    <script>
        try { if (localStorage.getItem('smartfinance_theme') === 'dark') document.documentElement.classList.add('dark-mode'); } catch(e){}
    </script>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <?php if ($include_chart): ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <?php endif; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo $asset_path; ?>/css/app.css">
    <script src="<?php echo $asset_path; ?>/js/theme.js"></script>
    <?php if (!empty($extra_head)) echo $extra_head; ?>
</head>
<body class="min-h-screen">
<div class="flex min-h-screen">
