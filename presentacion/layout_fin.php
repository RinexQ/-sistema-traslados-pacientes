        </main>
    </div>
</div>
<?php foreach (($scriptsExtra ?? []) as $src): ?>
    <script src="<?= e($src) ?>"></script>
<?php endforeach; ?>
</body>
</html>
