    <!-- JS -->
    <script src="assets/js/core.js"></script>
    
    <?php if (!empty($js)): ?>
        <?php if (is_array($js)): ?>
            <?php foreach ($js as $jsFile): ?>
                <script src="assets/js/<?= $jsFile ?>"></script>
            <?php endforeach; ?>
        <?php else: ?>
            <script src="assets/js/<?= $js ?>"></script>
        <?php endif; ?>
    <?php endif; ?>
    
    <!-- Inline JS -->
    <?php if (!empty($inlineJs)): ?>
        <script>
            <?= $inlineJs ?>
        </script>
    <?php endif; ?>
</body>
</html>