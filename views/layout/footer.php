</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<?php if (isset($_SESSION['flash_success']) || isset($_SESSION['flash_error'])):
    $is_success = isset($_SESSION['flash_success']);
    $msg_text = $is_success ? $_SESSION['flash_success'] : $_SESSION['flash_error'];
    $msg_type = $is_success ? 'success' : 'error';
    $msg_title = $is_success ? 'Thành công' : 'Thất bại';
?>
<script>
document.addEventListener("DOMContentLoaded", function() {
    Swal.fire({
        icon: '<?php echo $msg_type; ?>',
        title: '<?php echo $msg_title; ?>',
        text: '<?php echo $msg_text; ?>',
        timer: 2000,
        showConfirmButton: false,
        timerProgressBar: true,
        didOpen: () => {
            Swal.showLoading();
        }
    });
});
</script>
<?php
    unset($_SESSION['flash_success']);
    unset($_SESSION['flash_error']);
endif;
?>
</body>

</html>