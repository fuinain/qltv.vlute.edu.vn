import './adminlteJs/jquery.js';
import './adminlteJs/bootstrap.js';
import './adminlteJs/adminlte.js';
import Swal from 'sweetalert2';
import * as bootstrap from 'bootstrap';
import axios from 'axios';

window.Swal = Swal;
window.bootstrap = bootstrap;
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
