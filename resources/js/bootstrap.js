import '@/adminlteJs/jquery.js';
import '@/adminlteJs/bootstrap.js';
import '@/adminlteJs/adminlte.js';
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
