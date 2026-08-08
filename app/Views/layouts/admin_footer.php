            </main>
        </div>
    </div>

    <div id="confirmModal" class="fixed inset-0 z-[80] hidden items-center justify-center px-4 py-6" aria-labelledby="confirmModalTitle" role="dialog" aria-modal="true">
        <div class="absolute inset-0 bg-gray-900/55 backdrop-blur-sm" data-confirm-cancel></div>
        <div class="relative w-full max-w-md rounded-xl bg-white shadow-2xl ring-1 ring-black/5">
            <div class="p-6">
                <div class="flex items-start gap-4">
                    <div id="confirmModalIcon" class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-red-50 text-red-600">
                        <i class="fas fa-triangle-exclamation"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <h3 id="confirmModalTitle" class="text-lg font-bold text-gray-900">Confirm action</h3>
                        <p id="confirmModalMessage" class="mt-2 text-sm leading-6 text-gray-600">Are you sure you want to continue?</p>
                    </div>
                </div>
            </div>
            <div class="flex flex-col-reverse gap-3 border-t border-gray-100 bg-gray-50 px-6 py-4 sm:flex-row sm:justify-end">
                <button type="button" data-confirm-cancel class="inline-flex justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-bold text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-brand-green-500 focus:ring-offset-2">
                    Cancel
                </button>
                <button type="button" id="confirmModalProceed" class="inline-flex justify-center rounded-lg bg-red-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                    Continue
                </button>
            </div>
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

            initConfirmableForms();
            initRichTextTextareas();
        });

        function initRichTextTextareas() {
            document.querySelectorAll('textarea.richtext-editor').forEach(textarea => {
                if (textarea.dataset.richtextReady === '1') {
                    return;
                }

                textarea.dataset.richtextReady = '1';
                textarea.classList.add('hidden');

                const wrapper = document.createElement('div');
                wrapper.className = 'rounded-lg border border-gray-300 bg-white shadow-sm focus-within:border-brand-green-500 focus-within:ring-2 focus-within:ring-brand-green-500/20';

                const toolbar = document.createElement('div');
                toolbar.className = 'flex flex-wrap items-center gap-1 border-b border-gray-200 bg-gray-50 p-2';

                const actions = [
                    { label: 'Bold', icon: 'fa-bold', command: 'bold' },
                    { label: 'Italic', icon: 'fa-italic', command: 'italic' },
                    { label: 'Underline', icon: 'fa-underline', command: 'underline' },
                    { label: 'Bullet list', icon: 'fa-list-ul', command: 'insertUnorderedList' },
                    { label: 'Numbered list', icon: 'fa-list-ol', command: 'insertOrderedList' },
                    { label: 'Clear formatting', icon: 'fa-eraser', command: 'removeFormat' },
                ];

                const editor = document.createElement('div');
                editor.className = 'min-h-[180px] w-full overflow-y-auto rounded-b-lg bg-white px-4 py-3 text-sm leading-7 text-gray-800 outline-none prose prose-sm max-w-none';
                editor.contentEditable = 'true';
                editor.innerHTML = normalizeEditorHtml(textarea.value);
                editor.setAttribute('role', 'textbox');
                editor.setAttribute('aria-multiline', 'true');

                const sync = () => {
                    textarea.value = sanitizeEditorHtml(editor.innerHTML);
                };

                actions.forEach(action => {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'inline-flex h-9 w-9 items-center justify-center rounded-md border border-gray-200 bg-white text-gray-600 shadow-sm hover:border-brand-green-300 hover:text-brand-green-700 focus:outline-none focus:ring-2 focus:ring-brand-green-500 focus:ring-offset-1';
                    button.title = action.label;
                    button.setAttribute('aria-label', action.label);
                    button.innerHTML = `<i class="fas ${action.icon} text-xs"></i>`;
                    button.addEventListener('click', () => {
                        editor.focus();
                        document.execCommand(action.command, false, null);
                        sync();
                    });
                    toolbar.appendChild(button);
                });

                editor.addEventListener('input', sync);
                editor.addEventListener('blur', sync);
                editor.addEventListener('paste', function(e) {
                    e.preventDefault();
                    const text = (e.clipboardData || window.clipboardData).getData('text/plain');
                    document.execCommand('insertText', false, text);
                    sync();
                });

                const form = textarea.closest('form');
                if (form) {
                    form.addEventListener('submit', sync);
                }

                wrapper.appendChild(toolbar);
                wrapper.appendChild(editor);
                textarea.parentNode.insertBefore(wrapper, textarea);
                sync();
            });
        }

        function normalizeEditorHtml(value) {
            const raw = String(value || '').trim();
            if (raw === '') {
                return '';
            }

            if (/<[a-z][\s\S]*>/i.test(raw)) {
                return sanitizeEditorHtml(raw);
            }

            return raw
                .split(/\n{2,}/)
                .map(part => `<p>${escapeHtml(part).replace(/\n/g, '<br>')}</p>`)
                .join('');
        }

        function sanitizeEditorHtml(html) {
            const template = document.createElement('template');
            template.innerHTML = html;
            const allowedTags = new Set(['A', 'B', 'BR', 'DIV', 'EM', 'I', 'LI', 'OL', 'P', 'SPAN', 'STRONG', 'U', 'UL']);

            const cleanNode = (node) => {
                [...node.childNodes].forEach(child => {
                    if (child.nodeType === Node.TEXT_NODE) {
                        return;
                    }

                    if (child.nodeType !== Node.ELEMENT_NODE || !allowedTags.has(child.tagName)) {
                        child.replaceWith(document.createTextNode(child.textContent || ''));
                        return;
                    }

                    [...child.attributes].forEach(attribute => {
                        const name = attribute.name.toLowerCase();
                        if (child.tagName === 'A' && name === 'href') {
                            const value = attribute.value.trim();
                            if (/^(https?:|mailto:|tel:|\/|#)/i.test(value)) {
                                child.setAttribute('href', value);
                                child.setAttribute('rel', 'noopener');
                                continue;
                            }
                        }
                        child.removeAttribute(attribute.name);
                    });

                    cleanNode(child);
                });
            };

            cleanNode(template.content);
            return template.innerHTML.trim();
        }

        function escapeHtml(value) {
            const div = document.createElement('div');
            div.textContent = value;
            return div.innerHTML;
        }

        function initConfirmableForms() {
            const modal = document.getElementById('confirmModal');
            const title = document.getElementById('confirmModalTitle');
            const message = document.getElementById('confirmModalMessage');
            const proceed = document.getElementById('confirmModalProceed');
            const icon = document.getElementById('confirmModalIcon');
            let pendingForm = null;

            if (!modal || !title || !message || !proceed || !icon) {
                return;
            }

            const closeModal = () => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.classList.remove('overflow-hidden');
                pendingForm = null;
            };

            const applyVariant = (variant) => {
                const danger = variant !== 'primary';
                proceed.className = danger
                    ? 'inline-flex justify-center rounded-lg bg-red-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2'
                    : 'inline-flex justify-center rounded-lg bg-brand-green-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-brand-green-700 focus:outline-none focus:ring-2 focus:ring-brand-green-500 focus:ring-offset-2';
                icon.className = danger
                    ? 'flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-red-50 text-red-600'
                    : 'flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-brand-green-50 text-brand-green-700';
                icon.innerHTML = danger ? '<i class="fas fa-triangle-exclamation"></i>' : '<i class="fas fa-circle-check"></i>';
            };

            document.querySelectorAll('form[data-confirm]').forEach(form => {
                form.addEventListener('submit', function(e) {
                    if (form.dataset.confirmed === '1') {
                        delete form.dataset.confirmed;
                        return;
                    }

                    e.preventDefault();
                    e.stopImmediatePropagation();
                    pendingForm = form;

                    title.textContent = form.dataset.confirmTitle || 'Confirm action';
                    message.textContent = form.dataset.confirm || 'Are you sure you want to continue?';
                    proceed.textContent = form.dataset.confirmButton || 'Continue';
                    applyVariant(form.dataset.confirmVariant || 'danger');

                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                    document.body.classList.add('overflow-hidden');
                    proceed.focus();
                });
            });

            proceed.addEventListener('click', function() {
                if (!pendingForm) {
                    closeModal();
                    return;
                }

                const form = pendingForm;
                form.dataset.confirmed = '1';
                closeModal();
                form.requestSubmit();
            });

            modal.querySelectorAll('[data-confirm-cancel]').forEach(button => {
                button.addEventListener('click', closeModal);
            });

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                    closeModal();
                }
            });
        }

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
