import '@/adminlteJs/jquery.js';
import '@/adminlteJs/bootstrap.js';
import '@/adminlteJs/adminlte.js';
window.$ = window.jQuery = window.jQuery || $;

// Select2 từ adminlteJs
import '@/adminlteJs/select2/js/select2.full.min.js';
import '@/adminlteJs/select2/css/select2.min.css';
import '@/adminlteJs/select2-bootstrap4-theme/select2-bootstrap4.min.css';

// Summernote từ adminlteJs
import '@/adminlteJs/summernote/summernote-bs4.min.js';
import '@/adminlteJs/summernote/summernote-bs4.min.css';

// SweetAlert2
import Swal from 'sweetalert2';
window.Swal = Swal;

// Toastr
import toastr from 'toastr';
import 'toastr/build/toastr.min.css';
window.toastr = toastr;
toastr.options = {
    closeButton: true,
    progressBar: true,
    positionClass: 'toast-top-right',
    timeOut: '3000',
};

// Axios
import axios from 'axios';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
