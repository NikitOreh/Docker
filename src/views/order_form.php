<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div class="container py-5">
    <h1 class="mb-4">Создание нового заказа</h1>

    <?php if (!empty($_SESSION['form_errors'])): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($_SESSION['form_errors'] as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php unset($_SESSION['form_errors']); ?>
    <?php endif; ?>

    <form action="/orders" method="POST" class="needs-validation" novalidate>
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h2 class="h5 mb-0">Данные клиента</h2>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="client_name" class="form-label">ФИО клиента *</label>
                        <input type="text" class="form-control" id="client_name" name="client_name" 
                               value="<?= htmlspecialchars($_SESSION['old_input']['client_name'] ?? '') ?>" required>
                        <div class="invalid-feedback">Пожалуйста, укажите ФИО клиента</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="client_phone" class="form-label">Телефон *</label>
                        <input type="tel" class="form-control" id="client_phone" name="client_phone" 
                               value="<?= htmlspecialchars($_SESSION['old_input']['client_phone'] ?? '') ?>" required>
                        <div class="invalid-feedback">Введите корректный номер телефона</div>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="client_mail" class="form-label">Email</label>
                    <input type="email" class="form-control" id="client_mail" name="client_mail" 
                           value="<?= htmlspecialchars($_SESSION['old_input']['client_mail'] ?? '') ?>">
                    <div class="invalid-feedback">Введите корректный email</div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h2 class="h5 mb-0">Курьер</h2>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="courier_id" class="form-label">Выберите курьера *</label>
                    <select class="form-select" id="courier_id" name="courier_id" required>
                        <option value="">-- Выберите курьера --</option>
                        <?php foreach ($couriers as $courier): ?>
                            <option value="<?= $courier['courier_id'] ?>" 
                                <?= isset($_SESSION['old_input']['courier_id']) && $_SESSION['old_input']['courier_id'] == $courier['courier_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($courier['courier_fullname']) ?> 
                                (<?= htmlspecialchars($courier['courier_phone']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback">Пожалуйста, выберите курьера</div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h2 class="h5 mb-0">Товары</h2>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Выберите товары *</label>
                    <div class="row">
                        <?php foreach ($products as $product): ?>
                            <div class="col-md-4 mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" 
                                           name="products[]" value="<?= $product['product_article'] ?>" 
                                           id="product-<?= $product['product_article'] ?>"
                                           <?= isset($_SESSION['old_input']['products']) && in_array($product['product_article'], $_SESSION['old_input']['products']) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="product-<?= $product['product_article'] ?>">
                                        <?= htmlspecialchars($product['product_name']) ?> 
                                        (<?= number_format($product['product_price'], 2, '.', ' ') ?> ₽)
                                    </label>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php if (isset($_SESSION['form_errors']['products'])): ?>
                        <div class="text-danger small"><?= $_SESSION['form_errors']['products'] ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h2 class="h5 mb-0">Адрес доставки</h2>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="city" class="form-label">Город *</label>
                        <input type="text" class="form-control" id="city" name="city" 
                               value="<?= htmlspecialchars($_SESSION['old_input']['city'] ?? '') ?>" required>
                        <div class="invalid-feedback">Пожалуйста, укажите город</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="street" class="form-label">Улица *</label>
                        <input type="text" class="form-control" id="street" name="street" 
                               value="<?= htmlspecialchars($_SESSION['old_input']['street'] ?? '') ?>" required>
                        <div class="invalid-feedback">Пожалуйста, укажите улицу</div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="house" class="form-label">Дом *</label>
                        <input type="text" class="form-control" id="house" name="house" 
                               value="<?= htmlspecialchars($_SESSION['old_input']['house'] ?? '') ?>" required>
                        <div class="invalid-feedback">Пожалуйста, укажите номер дома</div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="apartment" class="form-label">Квартира</label>
                        <input type="text" class="form-control" id="apartment" name="apartment" 
                               value="<?= htmlspecialchars($_SESSION['old_input']['apartment'] ?? '') ?>">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="entrance" class="form-label">Подъезд</label>
                        <input type="text" class="form-control" id="entrance" name="entrance" 
                               value="<?= htmlspecialchars($_SESSION['old_input']['entrance'] ?? '') ?>">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="floor" class="form-label">Этаж</label>
                        <input type="text" class="form-control" id="floor" name="floor" 
                               value="<?= htmlspecialchars($_SESSION['old_input']['floor'] ?? '') ?>">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="intercom_code" class="form-label">Код домофона</label>
                    <input type="text" class="form-control" id="intercom_code" name="intercom_code" 
                           value="<?= htmlspecialchars($_SESSION['old_input']['intercom_code'] ?? '') ?>">
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h2 class="h5 mb-0">Детали доставки</h2>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="delivery_date" class="form-label">Дата доставки *</label>
                        <input type="date" class="form-control" id="delivery_date" name="delivery_date" 
                               value="<?= htmlspecialchars($_SESSION['old_input']['delivery_date'] ?? date('Y-m-d')) ?>" 
                               min="<?= date('Y-m-d') ?>" required>
                        <div class="invalid-feedback">Пожалуйста, выберите дату доставки</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="delivery_price" class="form-label">Стоимость доставки *</label>
                        <input type="number" step="0.01" class="form-control" id="delivery_price" name="delivery_price" 
                               value="<?= htmlspecialchars($_SESSION['old_input']['delivery_price'] ?? '300') ?>" required>
                        <div class="invalid-feedback">Укажите стоимость доставки</div>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="delivery_type" class="form-label">Тип доставки *</label>
                    <select class="form-select" id="delivery_type" name="delivery_type" required>
                        <option value="">-- Выберите тип --</option>
                        <option value="regular" <?= isset($_SESSION['old_input']['delivery_type']) && $_SESSION['old_input']['delivery_type'] == 'regular' ? 'selected' : '' ?>>Обычная</option>
                        <option value="cargo" <?= isset($_SESSION['old_input']['delivery_type']) && $_SESSION['old_input']['delivery_type'] == 'cargo' ? 'selected' : '' ?>>Грузовая</option>
                    </select>
                    <div class="invalid-feedback">Пожалуйста, выберите тип доставки</div>
                </div>
                <div class="mb-3">
                    <label for="delivery_tip" class="form-label">Чаевые курьеру</label>
                    <input type="number" step="0.01" class="form-control" id="delivery_tip" name="delivery_tip" 
                           value="<?= htmlspecialchars($_SESSION['old_input']['delivery_tip'] ?? '0') ?>">
                </div>
            </div>
        </div>

        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
            <button type="submit" class="btn btn-primary btn-lg">Создать заказ</button>
            <a href="/deliveries" class="btn btn-secondary btn-lg">Отмена</a>
        </div>
    </form>
</div>

<script>
// Валидация формы на клиенте
(function () {
    'use strict'
    const forms = document.querySelectorAll('.needs-validation')
    Array.from(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
            }
            form.classList.add('was-validated')
        }, false)
    })
})()
</script>

<?php 
unset($_SESSION['old_input']);
require_once __DIR__ . '/../partials/footer.php'; 
?>