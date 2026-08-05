// GHCC Main JavaScript
document.addEventListener('DOMContentLoaded', function() {
    console.log('GHCC Main JS loaded');

    // Newsletter Form
    const newsletterForm = document.getElementById('newsletter-form');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const emailInput = newsletterForm.querySelector('input[name="email"]');
            const message = document.getElementById('newsletter-message');
            const button = newsletterForm.querySelector('button');

            const email = emailInput.value;
            button.disabled = true;
            button.textContent = '...';

            try {
                const formData = new FormData();
                formData.append('email', email);

                const response = await fetch(`${window.GHCC_APP_URL || ''}/subscribe`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-Token': window.GHCC_CSRF_TOKEN || ''
                    },
                    body: formData
                });

                const result = await response.json();
                
                message.textContent = result.message;
                message.className = `text-xs italic ${result.status === 'success' ? 'text-brand-gold' : 'text-red-400'}`;
                
                if (result.status === 'success') {
                    emailInput.value = '';
                }
            } catch (error) {
                message.textContent = 'An error occurred. Please try again.';
                message.className = 'text-xs italic text-red-400';
            } finally {
                button.disabled = false;
                button.textContent = 'JOIN';
            }
        });
    }
});
