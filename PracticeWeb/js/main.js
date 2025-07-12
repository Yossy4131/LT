// Simple SNS JavaScript functionality

document.addEventListener('DOMContentLoaded', function() {
    // Character counter for post textarea
    const postTextarea = document.querySelector('.post-textarea');
    if (postTextarea) {
        setupCharacterCounter(postTextarea);
    }
    
    // Password confirmation validation
    const confirmPasswordField = document.getElementById('confirm_password');
    if (confirmPasswordField) {
        setupPasswordConfirmation();
    }
    
    // Auto-resize textarea
    if (postTextarea) {
        setupAutoResize(postTextarea);
    }
    
    // Real-time form validation
    setupFormValidation();
});

// Character counter for textarea
function setupCharacterCounter(textarea) {
    const maxLength = parseInt(textarea.getAttribute('maxlength')) || 1000;
    
    // Create counter element
    const counter = document.createElement('div');
    counter.className = 'character-counter';
    counter.style.cssText = `
        text-align: right;
        font-size: 0.875rem;
        color: #657786;
        margin-top: 0.25rem;
    `;
    
    textarea.parentNode.appendChild(counter);
    
    function updateCounter() {
        const currentLength = textarea.value.length;
        const remaining = maxLength - currentLength;
        
        counter.textContent = `${currentLength}/${maxLength}`;
        
        if (remaining < 50) {
            counter.style.color = '#e0245e';
        } else if (remaining < 100) {
            counter.style.color = '#ffad1f';
        } else {
            counter.style.color = '#657786';
        }
    }
    
    textarea.addEventListener('input', updateCounter);
    updateCounter(); // Initial call
}

// Password confirmation validation
function setupPasswordConfirmation() {
    const passwordField = document.getElementById('password');
    const confirmPasswordField = document.getElementById('confirm_password');
    
    if (!passwordField || !confirmPasswordField) return;
    
    function validatePasswordMatch() {
        const password = passwordField.value;
        const confirmPassword = confirmPasswordField.value;
        
        if (confirmPassword && password !== confirmPassword) {
            confirmPasswordField.setCustomValidity('パスワードが一致しません');
            confirmPasswordField.style.borderColor = '#e0245e';
        } else {
            confirmPasswordField.setCustomValidity('');
            confirmPasswordField.style.borderColor = '';
        }
    }
    
    passwordField.addEventListener('input', validatePasswordMatch);
    confirmPasswordField.addEventListener('input', validatePasswordMatch);
}

// Auto-resize textarea
function setupAutoResize(textarea) {
    function resize() {
        textarea.style.height = 'auto';
        textarea.style.height = Math.min(textarea.scrollHeight, 300) + 'px';
    }
    
    textarea.addEventListener('input', resize);
    resize(); // Initial call
}

// Form validation
function setupFormValidation() {
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const submitButton = form.querySelector('button[type="submit"]');
            
            if (submitButton) {
                // Prevent double submission
                submitButton.disabled = true;
                submitButton.textContent = '処理中...';
                
                // Re-enable after a short delay in case of validation errors
                setTimeout(() => {
                    submitButton.disabled = false;
                    submitButton.textContent = submitButton.getAttribute('data-original-text') || submitButton.textContent.replace('処理中...', '');
                }, 3000);
            }
        });
        
        // Store original button text
        const submitButton = form.querySelector('button[type="submit"]');
        if (submitButton) {
            submitButton.setAttribute('data-original-text', submitButton.textContent);
        }
    });
}

// Utility functions
function showAlert(message, type = 'info') {
    const alert = document.createElement('div');
    alert.className = `alert alert-${type}`;
    alert.textContent = message;
    alert.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 1000;
        max-width: 300px;
        animation: slideIn 0.3s ease-out;
    `;
    
    document.body.appendChild(alert);
    
    setTimeout(() => {
        alert.style.animation = 'slideOut 0.3s ease-in';
        setTimeout(() => {
            document.body.removeChild(alert);
        }, 300);
    }, 3000);
}

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
    
    .character-counter {
        transition: color 0.2s ease;
    }
    
    .form-input:invalid {
        border-color: #e0245e;
    }
    
    .form-input:valid {
        border-color: #17bf63;
    }
`;
document.head.appendChild(style);

// Auto-refresh posts (optional feature for demo)
function setupAutoRefresh() {
    if (window.location.pathname.endsWith('index.php')) {
        setInterval(() => {
            // Check for new posts every 30 seconds
            fetch(window.location.href)
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newPostsList = doc.querySelector('.posts-list');
                    const currentPostsList = document.querySelector('.posts-list');
                    
                    if (newPostsList && currentPostsList) {
                        const newPostsCount = newPostsList.children.length;
                        const currentPostsCount = currentPostsList.children.length;
                        
                        if (newPostsCount > currentPostsCount) {
                            showAlert('新しい投稿があります', 'info');
                        }
                    }
                })
                .catch(error => {
                    console.log('Auto-refresh failed:', error);
                });
        }, 30000);
    }
}

// Initialize auto-refresh (uncomment to enable)
// setupAutoRefresh();
