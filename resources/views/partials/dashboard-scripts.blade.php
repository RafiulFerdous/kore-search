<script>
(function() {
    var layout = document.querySelector('.dashboard-layout');
    var sidebar = document.getElementById('dashboardSidebar');
    var toggle = document.getElementById('sidebarToggle');
    var links = document.querySelectorAll('.dash-nav-link');
    var sections = {};

    if (toggle && layout) {
        toggle.addEventListener('click', function() {
            layout.classList.toggle('sidebar-collapsed');
            toggle.textContent = layout.classList.contains('sidebar-collapsed') ? '▶' : '◀';
        });
    }

    links.forEach(function(link) {
        var sectionId = link.getAttribute('data-section');
        if (!sectionId) return;
        var section = document.getElementById(sectionId);
        if (section) sections[sectionId] = section;

        link.addEventListener('click', function(e) {
            e.preventDefault();
            links.forEach(function(l) { l.classList.remove('active'); });
            link.classList.add('active');
            Object.keys(sections).forEach(function(id) {
                sections[id].classList.add('hidden');
            });
            if (sections[sectionId]) sections[sectionId].classList.remove('hidden');

            if (layout && window.innerWidth <= 1024) {
                layout.classList.add('sidebar-collapsed');
                if (toggle) toggle.textContent = '▶';
            }
        });
    });
})();
</script>
