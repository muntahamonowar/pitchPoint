// script.js

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('payment_form');
    const errorDiv = document.getElementById('error');

    // Payment method fields
    const cardFields = document.getElementById('card_fields');
    const paypalFields = document.getElementById('paypal_fields');
    const bankFields = document.getElementById('bank_fields');
    const cryptoFields = document.getElementById('crypto_fields');

    // Switch visible fields based on payment method
    const paymentRadios = document.querySelectorAll('input[name="payment_method"]');
    paymentRadios.forEach(radio => {
        radio.addEventListener('change', () => {
            cardFields.style.display = 'none';
            paypalFields.style.display = 'none';
            bankFields.style.display = 'none';
            cryptoFields.style.display = 'none';

            switch (radio.value) {
                case 'card': cardFields.style.display = 'block'; break;
                case 'paypal': paypalFields.style.display = 'block'; break;
                case 'bank': bankFields.style.display = 'block'; break;
                case 'crypto': cryptoFields.style.display = 'block'; break;
            }

            errorDiv.textContent = '';
        });
    });

    // Utility function to display errors
    function setError(msg) {
        errorDiv.textContent = msg || '';
        return !msg;
    }

    // Validation functions
    function validateCreditCard() {
        const card = document.getElementById('card_number').value.replace(/\s+/g,'');
        const expiry = document.getElementById('expiry').value.trim();
        const cvv = document.getElementById('cvv').value.trim();
        const name = document.getElementById('name_on_card').value.trim();

        if (name.length < 2) return setError('Enter the name on the card.');
        if (!/^\d{13,19}$/.test(card)) return setError('Card number must be 13-19 digits.');
        if (!/^(0[1-9]|1[0-2])\/\d{2}$/.test(expiry)) return setError('Expiry must be MM/YY format.');

        // Check expiry not in past
        const [mmStr, yyStr] = expiry.split('/');
        const mm = parseInt(mmStr, 10);
        const yy = parseInt(yyStr, 10) + 2000;
        const expDate = new Date(yy, mm - 1, 1);
        expDate.setMonth(expDate.getMonth() + 1);
        if (expDate <= new Date()) return setError('Card expiry is in the past.');

        if (!/^\d{3,4}$/.test(cvv)) return setError('CVV must be 3 or 4 digits.');

        return setError('');
    }

    function validatePayPal() {
        const email = document.getElementById('paypal_email').value.trim();
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) return setError('Enter a valid email address.');
        return setError('');
    }

    function validateBank() {
        const acct = document.getElementById('account_number').value.replace(/\s+/g,'');
        const routing = document.getElementById('routing_number').value.replace(/\s+/g,'');
        if (!/^\d{8,20}$/.test(acct)) return setError('Account number must be 8-20 digits.');
        if (!/^\d{6,9}$/.test(routing)) return setError('Routing/Sort code must be 6-9 digits.');
        return setError('');
    }

    function validateCrypto() {
        const addr = document.getElementById('wallet_address').value.trim();
        const coin = document.getElementById('coin').value;
        if (!/^[A-Za-z0-9]{20,64}$/.test(addr)) return setError('Enter a plausible wallet address.');
        if (!coin) return setError('Select a coin type.');
        return setError('');
    }

    // Validate before submitting form
    form.addEventListener('submit', (e) => {
        const selectedMethod = document.querySelector('input[name="payment_method"]:checked').value;
        let valid = false;

        switch (selectedMethod) {
            case 'card': valid = validateCreditCard(); break;
            case 'paypal': valid = validatePayPal(); break;
            case 'bank': valid = validateBank(); break;
            case 'crypto': valid = validateCrypto(); break;
        }

        if (!valid) e.preventDefault();
    });
});
