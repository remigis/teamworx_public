/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import ClipboardJS from "clipboard";
import Vue from 'vue';
import PrimeVue from 'primevue/config';
import ToastService from 'primevue/toastservice';
import ConfirmationService from 'primevue/confirmationservice';
import VueFileAgent from 'vue-file-agent';
import Tooltip from 'primevue/tooltip';
import {FilterMatchMode} from 'primevue/api';

require('./bootstrap');

Vue.use(PrimeVue, {
    filterMatchModeOptions: {
        text: [
            FilterMatchMode.STARTS_WITH,
            FilterMatchMode.CONTAINS,
            FilterMatchMode.NOT_CONTAINS,
            FilterMatchMode.ENDS_WITH,
            FilterMatchMode.EQUALS,
            FilterMatchMode.NOT_EQUALS
        ],
        numeric: [
            FilterMatchMode.EQUALS,
            FilterMatchMode.NOT_EQUALS,
            FilterMatchMode.LESS_THAN,
            FilterMatchMode.LESS_THAN_OR_EQUAL_TO,
            FilterMatchMode.GREATER_THAN,
            FilterMatchMode.GREATER_THAN_OR_EQUAL_TO
        ],
        date: [
            FilterMatchMode.DATE_IS,
            FilterMatchMode.DATE_IS_NOT,
            FilterMatchMode.DATE_BEFORE,
            FilterMatchMode.DATE_AFTER
        ]
    }
});

Vue.directive('tooltip', Tooltip);
Vue.use(VueFileAgent)
Vue.use(ToastService);
Vue.use(ConfirmationService);
Vue.component('all-goodsflow-item-table', require('./components/AllGoodsflowItemTableComponent.vue').default);
Vue.component('box-goodsflow-item-table', require('./components/Calculator/BoxGoodsflowItemTableComponent.vue').default);
Vue.component('box-search-input-with-autocomplete', require('./components/Calculator/BoxNameAutocomplete.vue').default);
Vue.component('box-item-condition-chart', require('./components/Calculator/BoxConditionChart.vue').default);
Vue.component('box-link-button', require('./components/Calculator/BoxLinkButton.vue').default);
Vue.component('box-action-button', require('./components/Calculator/BoxActionButton.vue').default);
Vue.component('box-label-barcodes', require('./components/Calculator/boxLabelBarcodes.vue').default);
Vue.component('box-scan', require('./components/Scan/BoxScan.vue').default);
Vue.component('new-item-scan', require('./components/NewItemScan/NewItemScan.vue').default);
Vue.component('razer-battery-good-bad', require('./components/RazerBatteryGoodBad.vue').default);
Vue.component('new-item-scanner', require('./components/NewItemScan/Scan.vue').default);
Vue.component('voice-settings', require('./components/Settings/VoiceSettings').default);
Vue.component('new-user-registration', require('./components/Admin/NewUserRegistration').default);
Vue.component('user-privileges', require('./components/Admin/UserPrivileges').default);
Vue.component('error-display', require('./components/ErrorDisplay.vue').default);
Vue.component('required-list-maker', require('./components/RequiredList/RequiredList.vue').default);
Vue.component('registration-with-invitation', require('./components/Auth/RegisterWithInvitation').default);
Vue.component('box-build-creator', require('./components/BoxBuild/BoxBuildCreator').default);
Vue.component('box-build-scanner', require('./components/BoxBuild/BoxBuildScan').default);
Vue.component('box-build-list', require('./components/BoxBuild/BoxBuildList').default);
Vue.component('box-build-viewer', require('./components/BoxBuild/BoxBuildViewer').default);
Vue.component('barcode', require('./components/Barcode').default);
Vue.component('qr-code', require('./components/QrCode').default);
Vue.component('item-transfer', require('./components/ItemTransfer/ItemTransfer').default);
Vue.component('gate-opener', require('./components/GateOpener').default);
Vue.component('gate-phone-number-setter', require('./components/Admin/GatePhoneNumberSetter').default);
Vue.component('direct-scan-boxes', require('./components/BoxBuild/DirectScanBoxes').default);
Vue.component('sn-checker', require('./components/Calculator/SNChecker').default);
Vue.component('print-button', require('./components/PrintButton').default);
Vue.component('printer', require('./components/Printer/Printer').default);


const csrf_token = $('meta[name="csrf-token"]').attr('content');
const app = new Vue({
    el: '#app',
    data: {
        csrf_token: csrf_token,
    },
    methods: {
        errorDisplay(error) {
            if (error.response.data.errors) {
                Object.keys(error.response.data.errors).forEach(key => {
                    this.$toast.add({
                        severity: 'error',
                        summary: 'Error',
                        detail: error.response.data.errors[key][0],
                        life: 8000
                    });
                });
            } else {
                this.$toast.add({
                    severity: 'error',
                    summary: 'Error',
                    detail: error.response.data.message,
                    life: 8000
                });
            }

        }
    }
});


require('clipboard');

new ClipboardJS('.btn');
require('popper.js');
require('slick-carousel');
require('./custom.js');
