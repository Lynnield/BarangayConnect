document.addEventListener('DOMContentLoaded', () => {
    // Duplicate Submission Prevention
    const forms = document.querySelectorAll('form:not([data-allow-multiple])');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('[type="submit"]');
            if (submitBtn && !submitBtn.hasAttribute('data-no-loading')) {
                if (submitBtn.disabled) {
                    e.preventDefault();
                    return;
                }
                
                // Show loading state
                const originalText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = `
                    <div class="flex items-center justify-center gap-2">
                        <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewbox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Processing...</span>
                    </div>
                `;
                
                // Safety timeout
                setTimeout(() => {
                    if (submitBtn.disabled) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    }
                }, 10000);
            }
        });
    });

    // Real-time Field Validation
    const validatedInputs = document.querySelectorAll('[data-validate]');
    validatedInputs.forEach(input => {
        const type = input.dataset.validate;
        input.addEventListener('blur', () => validateField(input, type));
        input.addEventListener('input', () => {
            clearError(input);
            if (type === 'password') updatePasswordStrength(input);
        });
    });
});

function updatePasswordStrength(input) {
    const password = input.value;
    let strength = 0;
    if (password.length >= 8) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^A-Za-z0-9]/.test(password)) strength++;

    let meter = input.parentNode.querySelector('.password-strength-meter');
    if (!meter) {
        meter = document.createElement('div');
        meter.className = 'password-strength-meter mt-2 flex gap-1 h-1 w-full';
        meter.innerHTML = '<div class="h-full rounded-full bg-slate-200 flex-1"></div>'.repeat(4);
        input.parentNode.appendChild(meter);
    }

    const bars = meter.querySelectorAll('div');
    const colors = ['bg-rose-500', 'bg-amber-500', 'bg-emerald-500', 'bg-indigo-500'];
    
    bars.forEach((bar, i) => {
        bar.className = 'h-full rounded-full flex-1 transition-all duration-300 ' + 
            (i < strength ? colors[strength-1] : 'bg-slate-200');
    });
}

function validateField(input, type) {
    const value = input.value.trim();
    let error = '';

    if (input.hasAttribute('required') && !value) {
        error = 'This field is required.';
    } else if (value) {
        switch (type) {
            case 'email':
                if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
                    error = 'Please enter a valid email address.';
                }
                break;
            case 'phone':
                if (!/^\d{10,11}$/.test(value.replace(/\D/g, ''))) {
                    error = 'Please enter a valid phone number (10-11 digits).';
                }
                break;
            case 'password':
                if (value.length < 8) {
                    error = 'Password must be at least 8 characters.';
                }
                break;
        }
    }

    if (error) {
        showError(input, error);
    } else {
        clearError(input);
    }
}

function showError(input, message) {
    clearError(input);
    input.classList.add('border-rose-500', 'ring-rose-500/10');
    input.classList.remove('border-slate-200', 'bg-slate-50');
    
    const errorEl = document.createElement('p');
    errorEl.className = 'mt-1.5 text-[10px] font-bold text-rose-500 validation-error flex items-center gap-1';
    errorEl.innerHTML = `<svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg> ${message}`;
    input.parentNode.appendChild(errorEl);
}

function clearError(input) {
    input.classList.remove('border-rose-500', 'ring-rose-500/10');
    input.classList.add('border-slate-200', 'bg-slate-50');
    
    const error = input.parentNode.querySelector('.validation-error');
    if (error) error.remove();
}
