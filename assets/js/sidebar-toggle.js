document.addEventListener('DOMContentLoaded', function() {
    // Get elements
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const mobileToggle = document.getElementById('mobile-toggle');
    const sidebarOverlay = document.querySelector('.sidebar-overlay');
    const body = document.body;
    
    // Desktop sidebar toggle
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            body.classList.toggle('sidebar-collapsed');
        });
    }
    
    // Mobile sidebar toggle
    if (mobileToggle) {
        mobileToggle.addEventListener('click', function() {
            body.classList.toggle('sidebar-mobile-show');
        });
    }
    
    // Close sidebar when clicking overlay
    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', function() {
            body.classList.remove('sidebar-mobile-show');
        });
    }
    
    // Handle window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 1200) {
            body.classList.remove('sidebar-mobile-show');
        }
    });
    
    // Toggle submenu
    const menuToggles = document.querySelectorAll('.menu-toggle');
    menuToggles.forEach(function(toggle) {
        toggle.addEventListener('click', function(e) {
            const parent = this.parentElement;
            
            if (parent.classList.contains('open')) {
                parent.classList.remove('open');
            } else {
                // Close other open menus
                const openMenus = document.querySelectorAll('.menu-item.open');
                openMenus.forEach(function(menu) {
                    if (menu !== parent && !menu.contains(parent) && !parent.contains(menu)) {
                        menu.classList.remove('open');
                    }
                });
                
                parent.classList.add('open');
            }
        });
    });
});