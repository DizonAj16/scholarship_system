function toggleNav() {
    const sideNav = document.getElementById('sideNav');
    const toggleIcon = document.getElementById('toggle-icon');
    const content = document.querySelector('.content');
    
    if (sideNav.classList.contains('expanded')) {
        sideNav.classList.remove('expanded');
        toggleIcon.className = 'fas fa-bars';
        // Remove overlay
        if (content) {
            content.classList.remove('sidebar-open');
        }
    } else {
        sideNav.classList.add('expanded');
        toggleIcon.className = 'fas fa-times';
        // Add overlay for mobile
        if (content && window.innerWidth <= 768) {
            content.classList.add('sidebar-open');
        }
    }
}

// Close sidebar when clicking outside on mobile
document.addEventListener('click', function(event) {
    const sideNav = document.getElementById('sideNav');
    const toggleBtn = document.querySelector('.toggle-btn');
    
    if (window.innerWidth <= 768 && 
        sideNav.classList.contains('expanded') &&
        !sideNav.contains(event.target) &&
        !toggleBtn.contains(event.target)) {
        
        sideNav.classList.remove('expanded');
        document.getElementById('toggle-icon').className = 'fas fa-bars';
        document.querySelector('.content')?.classList.remove('sidebar-open');
    }
});

// Handle window resize
window.addEventListener('resize', function() {
    const sideNav = document.getElementById('sideNav');
    const toggleIcon = document.getElementById('toggle-icon');
    
    if (window.innerWidth > 768) {
        // On desktop, always show sidebar (collapsed state)
        sideNav.classList.remove('expanded');
        toggleIcon.className = 'fas fa-bars';
    } else {
        // On mobile, hide sidebar by default
        sideNav.classList.remove('expanded');
        toggleIcon.className = 'fas fa-bars';
    }
});