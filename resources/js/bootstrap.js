import './adminlteJs/jquery.js';
import './adminlteJs/bootstrap.js';
import './adminlteJs/adminlte.js';
import Swal from 'sweetalert2';
window.Swal = Swal;

import axios from 'axios';
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
