const toggleBtn = document.getElementById('toggle-pw');
    const pwInput   = document.getElementById('password');

    toggleBtn.addEventListener('click', () => {
        const isPassword = pwInput.type === 'password';
        pwInput.type = isPassword ? 'text' : 'password';
        toggleBtn.textContent = isPassword ? 'visibility_off' : 'visibility';
    });