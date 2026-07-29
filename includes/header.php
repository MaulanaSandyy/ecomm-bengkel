<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo isset($title) ? htmlspecialchars($title) . ' — ' : ''; ?>Bengkel Mobil Jaya Abadi</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
  <noscript><style>.reveal{opacity:1!important;transform:none!important;}</style></noscript>
</head>
<body>

<div class="loading-spinner" id="loadingSpinner">
  <div class="spinner-box">
    <div class="spinner-border" style="width:2.2rem;height:2.2rem;border-width:3px;color:var(--c-accent);" role="status">
      <span class="visually-hidden">Loading...</span>
    </div>
    <h5 class="mt-4 fw-bold" id="loadingText" style="color:var(--c-ink);font-size:1rem;">Memproses...</h5>
    <p class="small mb-0" style="color:var(--c-ink-muted);font-size:0.82rem;">Mohon tunggu sebentar</p>
  </div>
</div>

<?php include 'navbar.php'; ?>
<?php if (isset($title) && $title === 'Beranda'): ?>
<main class="flex-grow-1">
<?php else: ?>
<div class="container-fluid d-flex flex-column" style="padding-top:100px;padding-bottom:40px;min-height:calc(100vh - 80px);">
<?php endif; ?>
