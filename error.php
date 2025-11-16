<?php
http_response_code(404);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error Found</title>


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center vh-100">

    <div class="text-center p-5 bg-white rounded shadow-sm">
        <h1 class="display-1 text-danger fw-bold">404</h1>
        <h2 class="mb-3">Page Not Found</h2>
        <p class="text-muted">
            The page '<strong><?= htmlspecialchars($_SERVER['REQUEST_URI']); ?></strong>' was not found On Our Server OR There is an error in the URL.
        </p>
        <a href="/" class="btn btn-primary mt-3">Go Home</a>
    </div>

 
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
