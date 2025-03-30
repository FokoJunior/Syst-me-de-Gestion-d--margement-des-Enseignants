<footer class="footer mt-auto py-3 bg-light">
    <div class="container text-center">
        <span class="text-muted">Système de gestion des enseignants &copy; <?php echo date('Y'); ?></span>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Gestion des sous-menus du sidebar
    document.querySelectorAll('[data-bs-toggle="collapse"]').forEach(item => {
        item.addEventListener('click', event => {
            const submenuIcon = item.querySelector('.bx-chevron-down');
            if (submenuIcon) {
                submenuIcon.style.transform = 
                    item.getAttribute('aria-expanded') === 'true' 
                    ? 'rotate(0deg)' 
                    : 'rotate(180deg)';
            }
        });
    });

    // Notifications flash
    const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.classList.add('fade');
            setTimeout(() => alert.remove(), 150);
        }, 3000);
    });
</script>
</body>
</html>