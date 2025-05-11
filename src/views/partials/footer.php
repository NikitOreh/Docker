</div> <!-- закрытие container -->

<footer class="bg-light mt-5 py-4">
    <div class="container text-center">
        <p class="mb-0">&copy; <?= date('Y') ?> ДоставкаЭкспресс. Все права защищены.</p>
    </div>
</footer>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

<!-- Mask Input -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

<!-- Custom JS -->
<script src="/assets/js/main.js"></script>

<!-- Инициализация DataTables -->
<script>
    $(document).ready(function() {
        $('.datatable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/ru.json'
            },
            responsive: true
        });
        
        // Маска для телефона
        $('.phone-input').mask('+7 (000) 000-00-00');
    });
</script>
</body>
</html>