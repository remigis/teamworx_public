<template>
    <qrcode-vue :value="text" :size="qrSize" level="H"></qrcode-vue>
</template>

<script>
import QrcodeVue from 'qrcode.vue'


export default {
    components: {
        'QrcodeVue': QrcodeVue,
    },
    name: "qrCode",
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
