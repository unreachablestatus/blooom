</main>

        <?php if (!isset($hide_footer) || !$hide_footer): ?>
        <footer class="main-footer">
            <div class="container">
                <p>Sevgi ile hazırlandı ❤️ <?php echo date('Y'); ?></p>
            </div>
        </footer>
        <?php endif; ?>

        <script src="/assets/js/main.js"></script>
        <?php if (isset($extra_js)): echo $extra_js; endif; ?>
    </body>
</html>
