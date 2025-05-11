<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div class="container">
    <h1 class="my-4">Список доставок</h1>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success']) ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <div class="mb-3">
        <a href="/deliveries/create" class="btn btn-primary">Создать новую доставку</a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID</th>
                            <th>Дата</th>
                            <th>Клиент</th>
                            <th>Курьер</th>
                            <th>Адрес</th>
                            <th>Стоимость</th>
                            <th>Статус</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($deliveries as $delivery): ?>
                        <tr>
                            <td><?= htmlspecialchars($delivery['delivery_number']) ?></td>
                            <td><?= date('d.m.Y', strtotime($delivery['delivery_date'])) ?></td>
                            <td>
                                <?= htmlspecialchars($delivery['client_fullname']) ?><br>
                                <small class="text-muted"><?= htmlspecialchars($delivery['client_phonenumber']) ?></small>
                            </td>
                            <td><?= htmlspecialchars($delivery['courier_fullname']) ?></td>
                            <td>
                                г. <?= htmlspecialchars($delivery['delivery_city']) ?>,<br>
                                ул. <?= htmlspecialchars($delivery['delivery_street']) ?> <?= htmlspecialchars($delivery['delivery_house']) ?>
                                <?php if ($delivery['delivery_apartment']): ?>
                                    кв. <?= htmlspecialchars($delivery['delivery_apartment']) ?>
                                <?php endif; ?>
                            </td>
                            <td><?= number_format($delivery['delivery_price'], 2, '.', ' ') ?> ₽</td>
                            <td>
                                <span class="badge badge-<?= 
                                    $delivery['delivery_status'] === 'completed' ? 'success' : 
                                    ($delivery['delivery_status'] === 'in_progress' ? 'warning' : 'secondary') 
                                ?>">
                                    <?= $this->getStatusText($delivery['delivery_status']) ?>
                                </span>
                            </td>
                            <td>
                                <a href="/deliveries/<?= $delivery['delivery_number'] ?>" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="/deliveries/<?= $delivery['delivery_number'] ?>/edit" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($totalPages > 1): ?>
            <nav aria-label="Page navigation">
                <ul class="pagination">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= $currentPage == $i ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                    <?php endfor; ?>
                </ul>
            </nav>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>