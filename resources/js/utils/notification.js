import Swal from 'sweetalert2';

/**
 * Show success toast notification at bottom-right
 */
export const showSuccessToast = (message, title = 'Berhasil!') => {
  const Toast = Swal.mixin({
    toast: true,
    position: 'bottom-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
      toast.addEventListener('mouseenter', Swal.stopTimer);
      toast.addEventListener('mouseleave', Swal.resumeTimer);
    },
  });

  return Toast.fire({
    icon: 'success',
    title: title,
    text: message,
  });
};

/**
 * Show error toast notification at bottom-right
 */
export const showErrorToast = (message, title = 'Gagal!') => {
  const Toast = Swal.mixin({
    toast: true,
    position: 'bottom-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
      toast.addEventListener('mouseenter', Swal.stopTimer);
      toast.addEventListener('mouseleave', Swal.resumeTimer);
    },
  });

  return Toast.fire({
    icon: 'error',
    title: title,
    text: message,
  });
};

/**
 * Show warning toast notification at bottom-right
 */
export const showWarningToast = (message, title = 'Peringatan!') => {
  const Toast = Swal.mixin({
    toast: true,
    position: 'bottom-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
      toast.addEventListener('mouseenter', Swal.stopTimer);
      toast.addEventListener('mouseleave', Swal.resumeTimer);
    },
  });

  return Toast.fire({
    icon: 'warning',
    title: title,
    text: message,
  });
};

/**
 * Show info toast notification at bottom-right
 */
export const showInfoToast = (message, title = 'Info') => {
  const Toast = Swal.mixin({
    toast: true,
    position: 'bottom-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
      toast.addEventListener('mouseenter', Swal.stopTimer);
      toast.addEventListener('mouseleave', Swal.resumeTimer);
    },
  });

  return Toast.fire({
    icon: 'info',
    title: title,
    text: message,
  });
};

/**
 * Show confirmation dialog (modal)
 */
export const showConfirmDialog = (title, message, confirmText = 'Ya', cancelText = 'Batal', confirmColor = '#3b82f6') => {
  return Swal.fire({
    icon: 'warning',
    title: title,
    text: message,
    showCancelButton: true,
    confirmButtonColor: confirmColor,
    cancelButtonColor: '#6b7280',
    confirmButtonText: confirmText,
    cancelButtonText: cancelText,
  });
};
