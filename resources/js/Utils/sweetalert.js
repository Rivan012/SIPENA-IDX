import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

// Custom IDX SweetAlert configuration
export const swalInstance = Swal.mixin({
    customClass: {
        popup: 'rounded-2xl border border-slate-200 shadow-xl font-sans',
        title: 'text-base font-bold text-slate-900',
        htmlContainer: 'text-xs text-slate-600',
        confirmButton: 'px-4 py-2.5 rounded-xl text-xs font-bold shadow-xs',
        cancelButton: 'px-4 py-2.5 rounded-xl text-xs font-semibold shadow-xs',
    },
    buttonsStyling: true,
    confirmButtonColor: '#9F0E0F',
    cancelButtonColor: '#64748B',
});

// Toast notification helper
export const toastInstance = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3500,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.onmouseenter = Swal.stopTimer;
        toast.onmouseleave = Swal.resumeTimer;
    },
    customClass: {
        popup: 'rounded-xl shadow-lg border border-slate-200 text-xs font-sans p-3',
        title: 'text-xs font-bold text-slate-800',
    },
});

export const showToast = (message, type = 'success') => {
    if (!message) return;
    toastInstance.fire({
        icon: type,
        title: message,
        iconColor: type === 'success' ? '#10B981' : (type === 'error' ? '#9F0E0F' : '#3B82F6'),
    });
};

export const confirmDialog = async ({
    title = 'Apakah Anda yakin?',
    text = '',
    confirmButtonText = 'Ya, Lanjutkan',
    cancelButtonText = 'Batal',
    icon = 'warning',
    confirmButtonColor = '#9F0E0F',
    cancelButtonColor = '#64748B',
}) => {
    const result = await swalInstance.fire({
        title,
        text,
        icon,
        iconColor: icon === 'warning' ? '#9F0E0F' : (icon === 'question' ? '#85131B' : undefined),
        showCancelButton: true,
        confirmButtonColor,
        cancelButtonColor,
        confirmButtonText,
        cancelButtonText,
        reverseButtons: true,
    });
    return result.isConfirmed;
};

export default {
    confirm: confirmDialog,
    toast: showToast,
    swal: swalInstance,
};
