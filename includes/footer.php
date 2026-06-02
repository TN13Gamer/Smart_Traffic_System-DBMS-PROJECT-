        </div> <!-- End .page-container -->
    </main> <!-- End .main-content -->

    <!-- Basic Vanilla JavaScript for UI polish and micro-interactions -->
    <script>
        // 1. Dynamic Live Clock
        function updateClock() {
            const clockEl = document.getElementById('live-clock');
            if (clockEl) {
                const now = new Date();
                const year = now.getFullYear();
                const month = String(now.getMonth() + 1).padStart(2, '0');
                const day = String(now.getDate()).padStart(2, '0');
                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');
                const seconds = String(now.getSeconds()).padStart(2, '0');
                
                clockEl.textContent = `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
            }
        }
        setInterval(updateClock, 1000);
        updateClock(); // Initial run

        // 2. Auto-fade alert notifications after 5 seconds to keep the UI clean
        document.addEventListener("DOMContentLoaded", function() {
            const alerts = document.querySelectorAll(".alert");
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    alert.style.transition = "opacity 0.6s ease, transform 0.6s ease";
                    alert.style.opacity = "0";
                    alert.style.transform = "translateY(-10px)";
                    setTimeout(function() {
                        alert.remove();
                    }, 600);
                }, 5000);
            });
        });
    </script>
</body>
</html>
<?php
// Close the database connection if it was opened
if (isset($conn) && $conn instanceof mysqli) {
    $conn->close();
}
?>
