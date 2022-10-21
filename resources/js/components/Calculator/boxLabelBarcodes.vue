<template>
    <div class="col-12 barcodes text-center align-items-center">
        <div class="col-12">
            <qrcode-vue :value="boxId" :size="qrSize" level="H"></qrcode-vue>
        </div>
        <div class="col-12">
            <vue-barcode v-bind:value="boxId" :width="3" :height="60">
                ...
            </vue-barcode>
        </div>
    </div>

</template>

<script>
import Button from 'primevue/button';
import Card from 'primevue/card';
import QrcodeVue from 'qrcode.vue'
import VueBarcode from 'vue-barcode';


export default {
    components: {
        'Card': Card,
        'QrcodeVue': QrcodeVue,
        'VueBarcode': VueBarcode,
        'print': print,
        'Button': Button,
    },
    name: "BoxLabels",
    props: {
        boxId: String,
    },
    data() {
        return {
            qrSize: 120,
        }
    },
    methods: {},
}
</script>

<style scoped>

</style>
