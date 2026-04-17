</div>

<script>
(function () {
    const btn = document.getElementById('mobileSidebarBtn');
    const sidebar = document.getElementById('adminSidebar');
    if (btn && sidebar) {
        btn.addEventListener('click', function () {
            sidebar.classList.toggle('open');
        });
        document.addEventListener('click', function (e) {
            if (window.innerWidth > 992) return;
            if (!sidebar.contains(e.target) && !btn.contains(e.target)) {
                sidebar.classList.remove('open');
            }
        });
    }
})();
</script>

<?php $flash = show_flash_message(); if ($flash): ?>
<script>alert(<?= json_encode(strip_tags($flash), JSON_UNESCAPED_UNICODE) ?>);</script>
<?php endif; ?>

</body>
</html>
