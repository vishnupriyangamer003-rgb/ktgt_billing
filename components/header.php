<?php
/**
 * Shared page shell — set these variables before including this file:
 *   $pageTitle  (string) e.g. 'Dashboard'  -> becomes "Dashboard · KTG Invoice Suite"
 *   $pageKey    (string) data-page value used for sidebar active-state + submenu logic
 *   $extraHead  (string, optional) raw HTML injected just before </head> (page-specific <style> etc.)
 */
if (!isset($pageTitle)) { $pageTitle = 'KTG Invoice Suite'; }
if (!isset($pageKey))   { $pageKey = ''; }
if (!isset($extraHead)) { $extraHead = ''; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo $pageTitle; ?> &middot; KTG Invoice Suite</title>
<link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><rect width=%22100%22 height=%22100%22 rx=%2224%22 fill=%22%232563eb%22/><text x=%2250%22 y=%2264%22 font-size=%2244%22 fill=%22white%22 font-family=%22Arial%22 text-anchor=%22middle%22 font-weight=%22bold%22>K</text></svg>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="assets/css/style.css" rel="stylesheet">
<?php echo $extraHead; ?>
</head>
<body data-page="<?php echo $pageKey; ?>">

<div class="app-shell">
  <div class="no-print">
    <?php include __DIR__ . '/sidebar.php'; ?>
  </div>
  <div class="main-wrap" id="mainWrap">
    <div class="no-print">
      <?php include __DIR__ . '/topbar.php'; ?>
    </div>

    <main class="page-content">
