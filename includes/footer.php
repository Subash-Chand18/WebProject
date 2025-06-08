    </main>
    <footer class="footer">
        <p>&copy; 2025 E-Clothing Store. All Rights Reserved.</p>
    </footer>

    <script>
        // Dropdown toggle for categories and products
        document.querySelectorAll('.dropdown-toggle').forEach(function(el) {
            el.addEventListener('click', function(e) {
                e.preventDefault();
                this.parentElement.classList.toggle('open');
            });
        });
    </script>
</body>
</html>
