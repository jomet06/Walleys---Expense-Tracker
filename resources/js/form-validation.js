const requiredMessages = {
    name: 'Enter your name.',
    title: 'Enter a transaction title.',
    email: 'Enter your email address.',
    category_id: 'Choose a category.',
    type: 'Choose income or expense.',
    icon: 'Choose an icon.',
    amount: 'Enter an amount.',
    transaction_date: 'Choose a transaction date.',
    current_password: 'Enter your current password.',
    password: 'Enter your password.',
    password_confirmation: 'Confirm your password.',
};

function labelFor(field) {
    const label = field.dataset.validationLabel
        || field.labels?.[0]?.textContent
        || field.closest('fieldset')?.querySelector('legend')?.textContent
        || field.name.replaceAll('_', ' ');
    return label.replace(/\s*\(optional\)\s*/gi, '').replace(/\s+/g, ' ').trim();
}

export function validationMessage(field, peers = [field]) {
    if (field.disabled || field.readOnly) return '';
    const value = field.value;
    const label = labelFor(field);
    const validity = field.validity;
    const empty = field.type === 'radio' ? !peers.some((peer) => peer.checked)
        : field.type === 'checkbox' ? !field.checked
            : (field.type === 'password' || field.autocomplete?.includes('password') ? value === '' : value.trim() === '');

    if (validity.badInput) return field.name === 'amount' ? 'Enter a whole number for the amount.' : `Enter a valid ${label.toLowerCase()}.`;
    if ((field.required || peers.some((peer) => peer.required)) && empty) {
        return field.dataset.requiredMessage || requiredMessages[field.name] || `Enter ${label.toLowerCase()}.`;
    }
    if (empty) return '';
    if (validity.typeMismatch) return field.type === 'email' ? 'Enter a valid email address, like you@example.com.' : `Enter a valid ${label.toLowerCase()}.`;
    if (validity.patternMismatch) return field.dataset.patternMessage || `Check the format of ${label.toLowerCase()}.`;
    if (validity.tooShort || (field.minLength > 0 && value.length < field.minLength)) return `Use at least ${field.minLength} characters.`;
    if (validity.tooLong || (field.maxLength > 0 && value.length > field.maxLength)) return `Use ${field.maxLength} characters or fewer.`;
    if (validity.rangeUnderflow) return field.name === 'amount' ? `Enter an amount of at least Rp ${Number(field.min).toLocaleString('id-ID')}.` : `Use a value of at least ${field.min}.`;
    if (validity.rangeOverflow) return field.name === 'amount' ? `Use an amount up to Rp ${Number(field.max).toLocaleString('id-ID')}.` : `Use a value no higher than ${field.max}.`;
    if (validity.stepMismatch) return field.name === 'amount' ? 'Enter the amount as a whole number, without decimals.' : `Check the value of ${label.toLowerCase()}.`;
    if (field.name === 'amount' && !/^[1-9]\d*$/.test(value)) return 'Enter a whole number, for example 50000.';
    if (field.name === 'password_confirmation') {
        const password = field.form?.elements.namedItem('password');
        if (password && value !== password.value) return 'The passwords do not match. Enter the same password again.';
    }
    if (validity.customError) return field.validationMessage;
    return '';
}

function appendDescription(field, id) {
    const ids = new Set((field.getAttribute('aria-describedby') || '').split(/\s+/).filter(Boolean));
    ids.add(id);
    field.setAttribute('aria-describedby', [...ids].join(' '));
}

function initializeForm(form, index) {
    if (form.dataset.validationReady) return;
    if (!form.id) form.id = `validated-form-${index + 1}`;

    const fields = [...form.querySelectorAll('input, select, textarea')]
        .filter((field) => field.form === form && field.willValidate && field.type !== 'file'
            && !['button', 'submit', 'reset'].includes(field.type)
            && (field.required || ['email', 'url', 'number', 'date', 'radio'].includes(field.type)
                || field.minLength > 0 || field.maxLength > 0 || field.pattern || field.name === 'password_confirmation'));
    if (!fields.length) return;

    const states = [];
    const byField = new Map();
    fields.forEach((field, fieldIndex) => {
        if (byField.has(field)) return;
        if (!field.id) field.id = `${form.id}-field-${fieldIndex + 1}`;
        const elements = field.type === 'radio' ? fields.filter((peer) => peer.type === 'radio' && peer.name === field.name) : [field];
        const container = field.closest('[data-field]') || field.closest('fieldset') || field.parentElement;
        const serverErrors = [...form.querySelectorAll('[data-server-error]')].filter((error) => {
            const target = error.dataset.errorFor;
            return (target && elements.some((element) => element.id === target || element.name === target))
                || (error.id && elements.some((element) => (element.getAttribute('aria-describedby') || '').split(/\s+/).includes(error.id)));
        });
        const serverInvalid = serverErrors.length > 0 || elements.some((element) => element.getAttribute('aria-invalid') === 'true');
        const error = document.createElement('p');
        error.id = `${field.id}-client-error`;
        error.className = 'ui-client-error mt-2';
        error.hidden = true;
        error.setAttribute('aria-live', 'polite');
        container.append(error);
        const state = { field, elements, container, serverErrors, serverInvalid, error, message: '', touched: serverInvalid };
        state.lastValue = field.type === 'radio' ? elements.filter((element) => element.checked).map((element) => element.value).join('|') : field.value;
        elements.forEach((element) => {
            byField.set(element, state);
            appendDescription(element, error.id);
            serverErrors.forEach((serverError) => { if (serverError.id) appendDescription(element, serverError.id); });
            if (serverInvalid) element.setAttribute('aria-invalid', 'true');
        });
        if (serverInvalid && container.tagName === 'FIELDSET') container.dataset.invalid = 'true';
        states.push(state);
    });

    let summary = form.querySelector('[data-validation-summary]');
    if (!summary) {
        summary = document.createElement('div');
        summary.className = 'ui-form-summary mb-5';
        summary.dataset.validationSummary = '';
        summary.hidden = true;
        summary.setAttribute('role', 'alert');
        const heading = document.createElement('p');
        heading.className = 'font-semibold';
        heading.textContent = 'Please check the highlighted fields.';
        summary.append(heading);
        form.prepend(summary);
    }
    let summaryList = summary.querySelector('[data-client-summary]');
    if (!summaryList) {
        summaryList = document.createElement('ul');
        summaryList.className = 'mt-2 space-y-1';
        summaryList.dataset.clientSummary = '';
        summary.append(summaryList);
    }
    const hasServerSummary = () => [...summary.querySelectorAll('[data-server-summary] li')].some((item) => !item.hidden);
    let summaryKey = null;

    function updateSummary() {
        const key = states.map((state) => state.message).join('\u0000');
        if (key === summaryKey) return;
        summaryKey = key;
        summaryList.replaceChildren();
        states.filter((state) => state.message).forEach((state) => {
            const item = document.createElement('li');
            const link = document.createElement('a');
            link.href = `#${state.field.id}`;
            link.className = 'underline decoration-rose-300 underline-offset-2';
            link.textContent = state.message;
            link.addEventListener('click', (event) => {
                event.preventDefault();
                (state.focusTarget || state.field).focus();
            });
            item.append(link);
            summaryList.append(item);
        });
        summaryList.hidden = !summaryList.children.length;
        summary.hidden = !hasServerSummary() && !summaryList.children.length;
    }

    function validate(state) {
        const enabled = state.elements.filter((field) => !field.disabled && field.willValidate);
        state.focusTarget = enabled[0];
        state.message = enabled.length ? validationMessage(enabled[0], enabled) : '';
        state.error.textContent = state.message;
        state.error.hidden = !state.message;
        const invalid = Boolean(state.message || state.serverInvalid);
        state.elements.forEach((field) => {
            if (invalid) field.setAttribute('aria-invalid', 'true');
            else field.removeAttribute('aria-invalid');
        });
        if (state.container.tagName === 'FIELDSET') state.container.dataset.invalid = String(invalid);
        return !state.message;
    }

    function onFieldEdit(event) {
        const state = byField.get(event.target);
        if (state) {
            const currentValue = state.field.type === 'radio' ? state.elements.filter((field) => field.checked).map((field) => field.value).join('|') : state.field.value;
            if (currentValue !== state.lastValue && state.serverInvalid) {
                state.serverInvalid = false;
                state.serverErrors.forEach((error) => { error.hidden = true; });
                const staleDescriptions = new Set(state.serverErrors.map((error) => error.id).filter(Boolean));
                state.elements.forEach((field) => {
                    field.removeAttribute('data-server-invalid');
                    const descriptions = (field.getAttribute('aria-describedby') || '').split(/\s+/).filter((id) => id && !staleDescriptions.has(id));
                    if (descriptions.length) field.setAttribute('aria-describedby', descriptions.join(' '));
                    else field.removeAttribute('aria-describedby');
                });
                [...summary.querySelectorAll('[data-server-summary-field]')].forEach((item) => {
                    if (item.dataset.serverSummaryField === state.field.name) item.hidden = true;
                });
                summaryKey = null;
            }
            state.lastValue = currentValue;
        }
        if (state?.touched) validate(state);
        if (event.target.name === 'password') {
            states.filter((entry) => entry.field.name === 'password_confirmation' && entry.touched).forEach(validate);
        }
        updateSummary();
    }

    form.addEventListener('input', onFieldEdit);
    form.addEventListener('change', onFieldEdit);
    form.addEventListener('focusout', (event) => {
        const state = byField.get(event.target);
        if (!state) return;
        // Moving between choices in one radio group should not announce an empty group.
        if (state.elements.includes(event.relatedTarget)) return;
        state.touched = true;
        validate(state);
        updateSummary();
    });
    form.addEventListener('submit', (event) => {
        states.forEach((state) => { state.touched = true; validate(state); });
        updateSummary();
        const invalid = states.find((state) => state.message);
        if (!invalid) return;
        event.preventDefault();
        (invalid.focusTarget || invalid.field).focus();
    }, true);

    // Keep native validation as a fallback when this module cannot initialize.
    form.dataset.validationReady = 'true';
    form.noValidate = true;
}

export function initializeFormValidation() {
    document.querySelectorAll('form[data-validate-form]').forEach(initializeForm);
}

if (typeof document !== 'undefined') {
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initializeFormValidation, { once: true });
    else initializeFormValidation();
}
