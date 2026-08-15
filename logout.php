<?php
session_start();
session_unset();
session_destroy();
?>

<!-- Panggil SweetAlert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
window.onload = function() {
  Swal.fire({
    title: 'Berhasil Logout!',
    text: 'Anda Berhasil Keluar.',
    icon: 'success',
    confirmButtonText: 'OK',
    confirmButtonColor: '#009688'
  }).then(() => {
    window.location.href = 'login.php';
  });
};
</script>
