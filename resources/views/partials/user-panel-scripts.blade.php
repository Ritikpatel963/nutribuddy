<script>
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('open');
        if (document.getElementById('overlay')) {
            document.getElementById('overlay').classList.toggle('show');
        }
    }

    function closeSidebar() {
        if (document.getElementById('sidebar')) {
            document.getElementById('sidebar').classList.remove('open');
        }
        if (document.getElementById('overlay')) {
            document.getElementById('overlay').classList.remove('show');
        }
    }

    function setActive(el) {
        document.querySelectorAll('.nav-item').forEach(i => i.classList.remove('active'));
        el.classList.add('active');
        if (window.innerWidth <= 900) closeSidebar();
    }

    document.querySelectorAll('[data-sidebar-group-toggle]').forEach(button => {
        button.addEventListener('click', function () {
            const expanded = this.getAttribute('aria-expanded') === 'true';
            const group = document.getElementById(this.getAttribute('aria-controls'));
            this.setAttribute('aria-expanded', String(!expanded));
            if (group) group.hidden = expanded;
        });
    });
</script>
