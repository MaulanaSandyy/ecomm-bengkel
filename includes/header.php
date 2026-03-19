<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title . ' - ' : ''; ?>Bengkel Mobil Jaya Abadi</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    
    <link rel="stylesheet" href="/ecomm-bengkel/assets/css/style.css"> 
</head>
<body class="bg-light">

<div class="loading-spinner" id="loadingSpinner">
    <div class="spinner-content animate__animated animate__zoomIn">
        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <h5 class="mt-4 fw-bold text-dark" id="loadingText">Memproses...</h5>
        <p class="text-muted small mb-0">Mohon tunggu sebentar</p>
    </div>
</div>

<?php include 'navbar.php'; ?>

<div class="container-fluid min-vh-100 d-flex flex-column" style="padding-top: 100px; padding-bottom: 40px;">