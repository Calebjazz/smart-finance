</main>
</div>
<?php if (!empty($include_advisor)): require __DIR__ . '/../Dashboard/financial_button.php'; endif; ?>
<?php if (!empty($page_scripts)) echo $page_scripts; ?>

<!-- Global chart auto-init: ensures all <canvas> elements with a data-* config
     are rendered inside DOMContentLoaded and stay in sync with form actions. -->
<?php if (!empty($include_chart)): ?>
<script src="<?php echo $asset_path ?? '../assets'; ?>/js/sf_charts.js"></script>
<?php endif; ?>

<!-- Auto-dismiss toast / alert notifications.
     - Waits for DOMContentLoaded so dynamic alerts inserted by inline scripts
       also get caught via the MutationObserver below.
     - Removes server-rendered .alert-success / .alert-error / .toast blocks
       after 4s, including a smooth fade-out.
     - Listens for newly inserted notifications (e.g. inserted via JS after an
       AJAX call) and schedules them for dismissal as well. -->
<script>
(function () {
    var DISMISS_MS = 4000;
    var FADE_MS    = 450;
    var SELECTOR   = '.alert-success, .alert-error, .toast, .sf-toast';

    function dismiss(el) {
        if (!el || el.dataset.sfDismissed === '1') return;
        el.dataset.sfDismissed = '1';
        setTimeout(function () {
            el.style.transition = 'opacity ' + FADE_MS + 'ms ease, transform ' + FADE_MS + 'ms ease';
            el.style.opacity = '0';
            el.style.transform = 'translateY(-8px)';
            setTimeout(function () { el.remove(); }, FADE_MS + 20);
        }, DISMISS_MS);
    }

    function init(root) {
        (root || document).querySelectorAll(SELECTOR).forEach(dismiss);
    }

    document.addEventListener('DOMContentLoaded', function () { init(document); });

    // Also pick up any alerts/toasts that get inserted later (e.g. after fetch()).
    var observer = new MutationObserver(function (mutations) {
        mutations.forEach(function (m) {
            m.addedNodes.forEach(function (n) {
                if (!(n instanceof Element)) return;
                if (n.matches && n.matches(SELECTOR)) dismiss(n);
                init(n);
            });
        });
    });
    if (document.body) {
        observer.observe(document.body, { childList: true, subtree: true });
    } else {
        document.addEventListener('DOMContentLoaded', function () {
            observer.observe(document.body, { childList: true, subtree: true });
        });
    }
})();
</script>

<?php
// Clear flash messages from session so a page refresh doesn't re-show them.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (isset($_SESSION['flash_messages'])) {
    unset($_SESSION['flash_messages']);
}
if (isset($_SESSION['success'])) {
    unset($_SESSION['success']);
}
if (isset($_SESSION['error'])) {
    unset($_SESSION['error']);
}
?>
</body>
</html>
