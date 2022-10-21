<template>
    <div class="row my-3">
        <div class="col-md-12">
            <Panel class="left">
                <template #header>
                    <i class="fa-solid fa-id-card"></i> Set Gate Phone Number
                </template>
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <p>Virtual phone number: <span style="font-weight: bold">{{ virtualPhone }}</span></p>
                        </div>
                        <div class="col-md-6">
                            <div class="p-inputgroup">
                                <span class="p-inputgroup-addon">+370</span>
                                <InputText placeholder="61759956" v-model="phone"/>
                            </div>
                        </div>
                        <Button :disabled="!phone" label="Set phone number" @click="setGatePhoneNumber()"
                                icon="fa-solid fa-plus" iconPos="left" class="col-md-3"/>
                        <Button label="Delete phone number" @click="deleteGatePhoneNumber()"
                                icon="fa-solid fa-trash" iconPos="left" class="col-md-3 p-button-danger"/>
                    </div>
                </div>
            </Panel>
        </div>

    </div>
</template>

<script>
import InputText from "primevue/inputtext";
import Button from "primevue/button";
import Panel from "primevue/panel";
import Card from "primevue/card";

export default {
    components: {
        'InputText': InputText,
        'Button': Button,
        'Panel': Panel,
        'Card': Card,

    },
    name: "GatePhoneNumberSetter",
    data() {
        return {
            phone: null,
            virtualPhone: null,
        }
    },
    created() {
        this.getGatePhoneNumber();
        this.getVirtualPhoneNumber();
    },
    mounted() {

    },
    methods: {
        setGatePhoneNumber() {
            axios.post(route('setGatePhoneNumber'), {phone: this.phone})
                .then(response => {
                    this.getGatePhoneNumber();
                    this.$toast.add({
                        severity: 'success',
                        summary: 'Success',
                        detail: response.data.message,
                        life: 6000
                    });
                })
                .catch(error => {
                    this.$root.errorDisplay(error);
                });
        },
        deleteGatePhoneNumber() {
            axios.get(route('deleteGatePhoneNumber'))
                .then(response => {
                    this.getGatePhoneNumber();
                    this.$toast.add({
                        severity: 'success',
                        summary: 'Success',
                        detail: response.data.message,
                        life: 6000
                    });
                })
                .catch(error => {
                    this.$root.errorDisplay(error);
                });
        },
        getVirtualPhoneNumber() {
            axios.get(route('getVirtualPhoneNumber'))
                .then(response => {
                    this.virtualPhone = response.data.virtual_phone_number;
                })
                .catch(error => {
                    this.$root.errorDisplay(error);
                });
        },
        getGatePhoneNumber() {
            axios.get(route('getGatePhoneNumber'))
                .then(response => {
                    this.phone = response.data.phone;
                })
                .catch(error => {
                    this.$root.errorDisplay(error);
                });
        },
    }
}
</script>

<style scoped>

</style>
