<template>
    <div class="row text-center align-content-center">

        <div class="col-md-4 text-center">
            <span v-if="initiated" style="color: #209d51;"><i
                class="fa-solid fa-phone fa-2xl"></i></span>
            <i v-if="!initiated" class="fa-solid fa-phone fa-2xl"></i>
            <p>Initiated</p>
        </div>
        <div class="col-md-4 text-center">
            <span v-if="ringing" style="color: #209d51;"><i class="fa-solid fa-phone-volume fa-2xl"></i></span>
            <i v-if="!ringing" class="fa-solid fa-phone-volume fa-2xl"></i>
            <p>Ringing</p>
        </div>
        <div class="col-md-4 text-center">
            <span v-if="busy" style="color: #209d51;"><i
                class="fa-solid fa-phone-slash fa-2xl"></i></span>
            <i v-if="!busy" class="fa-solid fa-phone-slash fa-2xl"></i>
            <p>Busy</p>
        </div>
        <div class="col-md-12 text-center my-5">
            <Button @click="makeCallToGates()" label="Call" class="col-12 my-3 p-button-success"
                    icon="fa-solid fa-phone"
                    iconPos="left"></Button>
        </div>
    </div>
</template>

<script>

import Button from 'primevue/button';

export default {
    components: {
        'Button': Button,
    },
    name: "GateOpener",
    props: {
        text: String,
    },
    data() {
        return {
            initiated: false,
            ringing: false,
            busy: false,
        }
    },
    created() {
        Echo.channel('GatesCallbackEventChannel').listen('GatesCallbackEvent', (event) => {
            if (event.requestData.CallStatus === 'initiated') {
                this.initiated = true;
            } else if (event.requestData.CallStatus === 'ringing') {
                this.ringing = true;
            } else if (event.requestData.CallStatus === 'busy') {
                this.busy = true;
            } else if (event.requestData.CallStatus === 'failed') {
                this.$toast.add({
                    severity: 'error',
                    summary: 'Error',
                    detail: 'Call failed',
                    life: 8000
                });
            }
        });
    },
    methods: {
        makeCallToGates() {
            this.initiated = false;
            this.ringing = false;
            this.busy = false;
            axios.post(route('makeCallToGates'))
                .then(response => {
                    //
                })
                .catch(error => {
                    this.$root.errorDisplay(error);
                });
        }
    },
}
</script>

<style scoped>

</style>
