
document.addEventListener("DOMContentLoaded", function() {
    const passForm = document.getElementById('changePasswordForm');
    
    function showCustomToast(message, type = 'success') {
        const oldToast = document.getElementById('pos-custom-toast');
        if (oldToast) oldToast.remove();

        const toastDiv = document.createElement('div');
        toastDiv.id = 'pos-custom-toast';
        let bgColor = '#10b981';
        let icon = '<i class="bi bi-check-circle-fill me-2"></i>';
        if (type === 'error') {
            bgColor = '#ef4444';
            icon = '<i class="bi bi-exclamation-circle-fill me-2"></i>';
        }

        toastDiv.style.cssText = `
            position: fixed; top: 24px; right: 24px; background-color: ${bgColor}; color: #ffffff;
            padding: 12px 24px; border-radius: 8px; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            z-index: 99999; font-weight: 600; font-size: 14px; display: flex; align-items: center;
            opacity: 0; transform: translateY(-20px); transition: all 0.3s ease;
        `;
        toastDiv.innerHTML = icon + message;
        document.body.appendChild(toastDiv);

        setTimeout(() => {
            toastDiv.style.opacity = '1'; toastDiv.style.transform = 'translateY(0)';
        }, 50);
        setTimeout(() => {
            toastDiv.style.opacity = '0'; toastDiv.style.transform = 'translateY(-20px)';
            setTimeout(() => toastDiv.remove(), 300);
        }, 4000);
    }

    if (passForm) {
        passForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const btnSubmit = document.getElementById('btnSubmitChangePass');
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Đang xử lý...`;

            const formData = new FormData(passForm);
            
            fetch('/user/changePassword', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                btnSubmit.disabled = false;
                btnSubmit.innerText = 'Cập nhật mật khẩu';
                
                if (data.success) {
                    showCustomToast(data.message, 'success');
                    passForm.reset();
                } else {
                    showCustomToast(data.message, 'error');
                }
            })
            .catch(error => {
                btnSubmit.disabled = false;
                btnSubmit.innerText = 'Cập nhật mật khẩu';
                showCustomToast('Có lỗi hệ thống xảy ra!', 'error');
            });
        });
    }
});