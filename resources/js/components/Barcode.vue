<template>
    <vue-barcode v-bind:value="text" :width="3" :height="60"></vue-barcode>
</template>

<script>
import VueBarcode from 'vue-barcode';


export default {
    components: {
        'VueBarcode': VueBarcode,
    },
    name: "barcode",
    props: {
        text: String,
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
