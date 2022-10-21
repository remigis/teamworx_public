<template>
    <div class="row my-3">
        <div class="col-md-12">
            <Panel class="left">
                <template #header>
                    <i class="fa-solid fa-bullhorn"></i> Assistant voice
                </template>
                <div class="row p-3">
                    <div class="col-md-6">
                        <Dropdown @change="disableTestAudio()" class="col-12" v-model="selectedVoice" :options="voices"
                                  optionLabel="label" placeholder="Select a voice"/>
                    </div>
                    <div class="col-md-6">
                        <div class="col-12"><h3>Pitch: {{ pitchValue }}</h3></div>
                        <Slider @change="disableTestAudio()" class="col-12" v-model="pitchValue" :min="-100"
                                :max="100"/>
                    </div>
                    <Button class="mt-5 col-12" :disabled="disableTestAudioButton" @click="playTestAudio"
                            label="Check out my beautiful voice" icon="fa-solid fa-volume-high" iconPos="left"/>
                    <Button class="mt-5 col-12" @click="changeUsersVoiceSettings()" label="Change"
                            icon="fa-solid fa-pen-to-square" iconPos="left"/>
                </div>
            </Panel>
        </div>
        <div class="col-md-8">

        </div>


        <ConfirmDialog></ConfirmDialog>
    </div>
</template>

<script>

import InputText from 'primevue/inputtext';
import Panel from 'primevue/panel';
import SplitButton from "primevue/splitbutton";
import Button from "primevue/button";
import Dialog from "primevue/dialog";
import InputNumber from "primevue/inputnumber";
import Chip from 'primevue/chip';
import ProgressSpinner from 'primevue/progressspinner';
import TabView from 'primevue/tabview';
import TabPanel from 'primevue/tabpanel';
import ConfirmDialog from 'primevue/confirmdialog';
import Listbox from 'primevue/listbox';
import ToggleButton from 'primevue/togglebutton';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Card from 'primevue/card';
import AutoComplete from "primevue/autocomplete";
import Slider from 'primevue/slider';
import Dropdown from 'primevue/dropdown';


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
    name: "VoiceSettings",
    props: {
        userId: String,
    },
    data() {
        return {
            selectedVoice: null,
            voices: [],
            pitchValue: 0,
            disableTestAudioButton: true,
            testAudio: null,
        }
    },
    created() {
        this.checkIfTestAudioExists();
        this.getVoiceList();

        this.getUsersVoiceSettings();
    },
    mounted() {

    },
    methods: {
        checkIfTestAudioExists() {
            axios.get(route('checkIfTestAudioExists'))
                .then(response => {
                    this.disableTestAudioButton = !response.data.isInStorage;
                    if (!this.disableTestAudioButton) {
                        this.testAudio = new Audio(this.audioSource());
                    }
                })
                .catch(error => {
                    this.$root.errorDisplay(error);
                });
        },
        audioSource() {
            return '/storage/sounds/audio/testAudio-' + this.userId + '.MP3?id=' + Math.random();
        },
        disableTestAudio() {
            this.disableTestAudioButton = true;
        },
        playTestAudio() {
            this.testAudio.play();
        },
        getVoiceList() {
            axios.get(route('getVoiceList'))
                .then(response => {
                    this.voices = response.data;
                })
                .catch(error => {
                    this.$root.errorDisplay(error);
                });
        },
        getUsersVoiceSettings() {
            axios.get(route('getUsersVoiceSettings'))
                .then(response => {
                    this.selectedVoice = response.data.selectedVoice;
                    this.pitchValue = response.data.pitchValue;
                })
                .catch(error => {
                    this.$root.errorDisplay(error);
                });
        },
        changeUsersVoiceSettings() {
            axios.post(route('changeUsersVoiceSettings', {name: this.selectedVoice.name, pitch: this.pitchValue}))
                .then(response => {
                    this.disableTestAudioButton = false;
                    this.checkIfTestAudioExists();
                    this.$toast.add({
                        severity: 'seccess',
                        summary: 'Success',
                        detail: response.data.message,
                        life: 6000
                    });
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

