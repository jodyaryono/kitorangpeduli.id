
    // Only allow numeric input in phone number field
    document.getElementById('no_hp').addEventListener('input', function(e) {
        // Remove any non-numeric characters
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    // Prevent paste of non-numeric content
    document.getElementById('no_hp').addEventListener('paste', function(e) {
        e.preventDefault();
        const pastedText = (e.clipboardData || window.clipboardData).getData('text');
        const numericOnly = pastedText.replace(/[^0-9]/g, '');
        this.value = numericOnly.substring(0, 13); // Max 13 digits
    });

