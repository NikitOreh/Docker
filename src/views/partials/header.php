<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Система управления доставками</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/assets/css/style.css">
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand" href="/">ДоставкаЭкспресс</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/deliveries"><i class="fas fa-truck me-1"></i> Доставки</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/clients"><i class="fas fa-users me-1"></i> Клиенты</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/couriers"><i class="fas fa-user-tie me-1"></i> Курьеры</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/products"><i class="fas fa-box-open me-1"></i> Товары</a>
                    </li>
                </ul>
                <div class="d-flex">
                    <a href="/import" class="btn btn-light me-2"><i class="fas fa-file-import me-1"></i> Импорт</a>
                    <a href="/logout" class="btn btn-outline-light"><i class="fas fa-sign-out-alt me-1"></i> Выход</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container">
        <!-- Flash сообщения -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?= htmlspecialchars($_SESSION['success']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?= htmlspecialchars($_SESSION['error']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>