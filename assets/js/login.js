document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('login-form');
    const nisInput = document.getElementById('nis');
    const passwordInput = document.getElementById('password');
    const loginBtn = document.getElementById('login-btn');
    const feedbackMessage = document.getElementById('feedback-message');

    loginForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        feedbackMessage.textContent = '';
        feedbackMessage.classList.remove('text-red-500', 'text-green-500');

        loginBtn.disabled = true;
        loginBtn.textContent = 'Memproses...';

        if (nisInput.value.trim() === '' || passwordInput.value.trim() === '') {
            feedbackMessage.textContent = 'NIS dan Password tidak boleh kosong.';
            feedbackMessage.classList.add('text-red-500');
            loginBtn.disabled = false;
            loginBtn.textContent = 'LOGIN';
            return;
        }

        const formData = new FormData(loginForm);

        try {
            const response = await fetch('../api/login_siswa.php', {
                method: 'POST',
                body: formData
            });

            if (!response.ok) {
                throw new Error('Jaringan bermasalah atau server error');
            }

            const data = await response.json();

            if (data.status === 'success' && data.redirect) {
                window.location.href = data.redirect;
            } else {
                feedbackMessage.textContent = data.message || 'Terjadi kesalahan tidak dikenal.';
                feedbackMessage.classList.add('text-red-500');
            }
        } catch (error) {
            feedbackMessage.textContent = 'Terjadi kesalahan saat menghubungi server. Silakan coba lagi.';
            feedbackMessage.classList.add('text-red-500');
        } finally {
            loginBtn.disabled = false;
            loginBtn.textContent = 'LOGIN';
        }
    });
});