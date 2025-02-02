<!-- templates/footer.php -->
<footer
    style="margin-left: var(--sidebar-width); padding: 20px; text-align: center; color: #6c757d; transition: margin-left 0.3s ease-in-out;">
    <p>&copy; 2024 Parfum Shop. All rights reserved.</p>
</footer>

<!-- jQuery -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<!-- Custom JS -->
<script>
$(document).ready(function() {
    // Sidebar toggle for desktop
    $('#sidebar-toggle').click(function(e) {
        e.preventDefault();
        if (window.innerWidth >= 992) {
            $('body').toggleClass('sidebar-mini');
        } else {
            $('body').toggleClass('sidebar-open');
        }
    });

    // Close sidebar when clicking outside on mobile
    $(document).click(function(e) {
        if (window.innerWidth < 992) {
            if (!$(e.target).closest('.sidebar, #sidebar-toggle').length) {
                $('body').removeClass('sidebar-open');
            }
        }
    });

    // Handle window resize
    $(window).resize(function() {
        if (window.innerWidth >= 992) {
            $('body').removeClass('sidebar-open');
        }
    });
});
</script>
</body>

</html>