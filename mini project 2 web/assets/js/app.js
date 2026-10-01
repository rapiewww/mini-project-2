/**
 * KICKSVAULT - Interactive Client Script
 * File: assets/js/app.js
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Auto dismiss alert
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        const closeBtn = alert.querySelector('.alert-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => {
                alert.style.transition = 'opacity 0.25s ease, transform 0.25s ease';
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-10px)';
                setTimeout(() => alert.remove(), 250);
            });
        }

        // Auto close after 5s
        setTimeout(() => {
            if (alert && alert.parentElement) {
                alert.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-10px)';
                setTimeout(() => alert.remove(), 300);
            }
        }, 5000);
    });

    // 2. Delete Confirmation Modal Handling
    const deleteModal = document.getElementById('deleteConfirmModal');
    const deleteForm = document.getElementById('deleteForm');
    const deleteTargetName = document.getElementById('deleteTargetName');
    const deleteTargetId = document.getElementById('deleteTargetId');
    const cancelDeleteBtn = document.getElementById('cancelDeleteBtn');

    if (deleteModal && deleteForm) {
        document.querySelectorAll('.btn-delete-trigger').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const id = btn.getAttribute('data-id');
                const name = btn.getAttribute('data-name');

                if (deleteTargetId) deleteTargetId.value = id;
                if (deleteTargetName) deleteTargetName.textContent = name;

                deleteModal.classList.add('active');
            });
        });

        if (cancelDeleteBtn) {
            cancelDeleteBtn.addEventListener('click', () => {
                deleteModal.classList.remove('active');
            });
        }

        deleteModal.addEventListener('click', (e) => {
            if (e.target === deleteModal) {
                deleteModal.classList.remove('active');
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && deleteModal.classList.contains('active')) {
                deleteModal.classList.remove('active');
            }
        });
    }

    // 3. Image File Upload Preview
    const imageInput = document.getElementById('imageInput');
    const imagePreview = document.getElementById('imagePreview');

    if (imageInput && imagePreview) {
        imageInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (event) => {
                    imagePreview.src = event.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // 4. Live filter auto-submit on change for selects
    const autoSubmitSelects = document.querySelectorAll('.filter-auto-submit');
    autoSubmitSelects.forEach(select => {
        select.addEventListener('change', () => {
            select.closest('form').submit();
        });
    });
});
