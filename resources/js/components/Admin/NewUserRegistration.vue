<template>
    <div class="row my-3">
        <div class="col-md-12">
            <Panel class="left">
                <template #header>
                    <i class="fa-solid fa-id-card"></i> Register new user
                </template>
                <div class="container">
                    <div class="row">
                        <span class="p-float-label col-md-6 my-4">
	                        <InputText id="name" type="text" v-model="name"/>
	                        <label for="name" class="ml-4">Name</label>
                        </span>
                        <span class="p-float-label col-md-6 my-4">
	                        <InputText id="email" type="text" v-model="email"/>
	                        <label for="email" class="ml-4">Email</label>
                        </span>
                        <Button :disabled="!name || !email" label="Send invitation" @click="sendRegisterInvitation()"
                                icon="fa-solid fa-paper-plane" iconPos="left" class="col-md-12"/>
                    </div>
                </div>
            </Panel>
        </div>

    </div>
</template>

<script>
import InputText from "primevue/inputtext";
import SplitButton from "primevue/splitbutton";
import Button from "primevue/button";
import Dialog from "primevue/dialog";
import InputNumber from "primevue/inputnumber";
import Panel from "primevue/panel";
import Chip from "primevue/chip";
import ProgressSpinner from "primevue/progressspinner";
import TabView from "primevue/tabview";
import TabPanel from "primevue/tabpanel";
import ConfirmDialog from "primevue/confirmdialog";
import Listbox from "primevue/listbox";
import ToggleButton from "primevue/togglebutton";
import DataTable from "primevue/datatable";
import Column from "primevue/column";
import Card from "primevue/card";
import AutoComplete from "primevue/autocomplete";
import Slider from "primevue/slider";
import Dropdown from "primevue/dropdown";

export default {
    components: {
        'InputText': InputText,
        'SplitButton': SplitButton,
        'Button': Button,
        'Dialog': Dialog,
        'InputNumber': InputNumber,
        'Panel': Panel,
        'Chip': Chip,
        'ProgressSpinner': ProgressSpinner,
        'TabView': TabView,
        'TabPanel': TabPanel,
        'ConfirmDialog': ConfirmDialog,
        'Listbox': Listbox,
        'ToggleButton': ToggleButton,
        'DataTable': DataTable,
        'Column': Column,
        'Card': Card,
        'AutoComplete': AutoComplete,
        'Slider': Slider,
        'Dropdown': Dropdown,

    },
    name: "NewUserRegistration",
    data() {
        return {
            email: null,
            name: null,
        }
    },
    created() {
    },
    mounted() {

    },
    methods: {
        sendRegisterInvitation() {
            axios.post(route('sendRegisterInvitation'), {name: this.name, email: this.email})
                .then(response => {
                    this.email = null;
                    this.name = null;
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
        }
    }
}
</script>

<style scoped>

</style>
