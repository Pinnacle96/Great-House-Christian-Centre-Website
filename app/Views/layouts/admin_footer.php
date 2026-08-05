            </main>
        </div>
    </div>

    <script>
        window.GHCC_CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('form[method="POST"], form[method="post"]').forEach(function(form) {
                if (!form.querySelector('input[name="_csrf_token"]')) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = '_csrf_token';
                    input.value = window.GHCC_CSRF_TOKEN;
                    form.appendChild(input);
                }
            });
        });

        // Mobile menu functionality
        function toggleMobileMenu() {
            const mobileSidebar = document.getElementById('mobileSidebar');
            const mobileOverlay = document.getElementById('mobileOverlay');
            
            if (mobileSidebar.classList.contains('-translate-x-full')) {
                mobileSidebar.classList.remove('-translate-x-full');
                mobileOverlay.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            } else {
                closeMobileMenu();
            }
        }
        
        function closeMobileMenu() {
            const mobileSidebar = document.getElementById('mobileSidebar');
            const mobileOverlay = document.getElementById('mobileOverlay');
            
            mobileSidebar.classList.add('-translate-x-full');
            mobileOverlay.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
        
        // Close menu when clicking on overlay
        document.getElementById('mobileOverlay').addEventListener('click', closeMobileMenu);
        
        // Close menu when pressing Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeMobileMenu();
            }
        });
        
        // Responsive table handling
        function initResponsiveTables() {
            const tables = document.querySelectorAll('table');
            tables.forEach(table => {
                if (table.parentElement.classList.contains('overflow-x-auto')) {
                    return;
                }
                const wrapper = document.createElement('div');
                wrapper.className = 'overflow-x-auto';
                table.parentNode.insertBefore(wrapper, table);
                wrapper.appendChild(table);
            });
        }
        
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            initResponsiveTables();
            
            // Add loading states to buttons
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    const buttons = this.querySelectorAll('button[type="submit"]');
                    buttons.forEach(button => {
                        button.disabled = true;
                        const originalText = button.innerHTML;
                        button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Processing...';
                        button.dataset.originalText = originalText;
                    });
                });
            });
        });
        
        // Toast notifications
        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg transform transition-all duration-300 translate-y-8 opacity-0 ${
                type === 'success' ? 'bg-green-500 text-white' : 
                type === 'error' ? 'bg-red-500 text-white' : 
                'bg-blue-500 text-white'
            }`;
            toast.innerHTML = `
                <div class="flex items-center">
                    <i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'} mr-3"></i>
                    <span>${message}</span>
                </div>
            `;
            
            document.body.appendChild(toast);
            
            // Animate in
            setTimeout(() => {
                toast.classList.remove('translate-y-8', 'opacity-0');
                toast.classList.add('translate-y-0', 'opacity-100');
            }, 10);
            
            // Animate out and remove
            setTimeout(() => {
                toast.classList.remove('translate-y-0', 'opacity-100');
                toast.classList.add('translate-y-8', 'opacity-0');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }
        
        // Auto-dismiss alerts
        function initAutoDismissAlerts() {
            const alerts = document.querySelectorAll('.alert-auto-dismiss');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateY(-10px)';
                    setTimeout(() => alert.remove(), 300);
                }, 5000);
            });
        }
        
        // Initialize auto-dismiss alerts
        document.addEventListener('DOMContentLoaded', initAutoDismissAlerts);
        
        // Responsive card grid
        function initResponsiveCardGrid() {
            const grids = document.querySelectorAll('.responsive-card-grid');
            grids.forEach(grid => {
                const observer = new ResizeObserver(entries => {
                    entries.forEach(entry => {
                        const width = entry.contentRect.width;
                        if (width < 640) {
                            grid.classList.add('grid-cols-1');
                            grid.classList.remove('grid-cols-2', 'grid-cols-3', 'grid-cols-4');
                        } else if (width < 768) {
                            grid.classList.add('grid-cols-2');
                            grid.classList.remove('grid-cols-1', 'grid-cols-3', 'grid-cols-4');
                        } else if (width < 1024) {
                            grid.classList.add('grid-cols-3');
                            grid.classList.remove('grid-cols-1', 'grid-cols-2', 'grid-cols-4');
                        } else {
                            grid.classList.add('grid-cols-4');
                            grid.classList.remove('grid-cols-1', 'grid-cols-2', 'grid-cols-3');
                        }
                    });
                });
                
                observer.observe(grid);
            });
        }
        
        // Initialize responsive card grid
        document.addEventListener('DOMContentLoaded', initResponsiveCardGrid);
    </script>
</body>
</html>
