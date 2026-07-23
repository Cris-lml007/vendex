import '../css/app.css';
import './bootstrap';
import Swal from "sweetalert2";
import {Html5QrcodeScanner} from "html5-qrcode";
import L from 'leaflet';
import 'leaflet-draw';
import 'leaflet-draw/dist/leaflet.draw.css';
import 'leaflet/dist/leaflet.css';

import ApexCharts from "apexcharts";

window.Swal = Swal;
window.Html5QrcodeScanner = Html5QrcodeScanner;
window.ApexCharts = ApexCharts;
window.L = L;
