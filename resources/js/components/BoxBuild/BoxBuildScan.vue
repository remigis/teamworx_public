<template>
    <div>
        <div class="row row-eq-height">
            <div class="col-md-6">

                <Card class="p-3 h-100">
                    <template #content>
                        <div class="mb-3">
                        <span class="p-float-label col-md-12 my-4">
                        <InputText id="GoodsFlow" class="col-12"
                                   type="text" v-model="goodsFlow" @keyup.enter="submitGoodsFlowId()"/>
                            <label for="GoodsFlow" class="ml-4">GoodsFlow ID</label>
                        </span>
                        </div>

                    </template>
                </Card>
            </div>

            <div class="col-md-6">

                <Card class="p-3 h-100">
                    <template #content>
                        <div class="mb-3">
                            <div class="container">
                                <div class="row my-4">
                                    <div class="col-2 my-3">
                                        <InputSwitch @click="toggleDirectScanStatus()" v-model="directScan"/>
                                    </div>
                                    <div class="col-10">
                                        <Dropdown class="col-12 p-2" v-model="directScanFulfilmentCenter"
                                                  :options="fulfilmentCenters" optionLabel="name"
                                                  placeholder="Select Fulfilment center"
                                                  @change="setDirectScanFulfilmentCenter()"/>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </template>
                </Card>
            </div>
        </div>

        <div class="row my-4">
            <div v-for="box in openBoxes" class="col-md-4 my-2">

                <Card class="p-3 h-100">
                    <template #header>
                        <span style="font-weight: bolder;">{{ box.name }}</span>
                    </template>
                    <template #title>
                        <div class="container">
                            <div class="row py-1">
                                <Button v-tooltip="'Delete box and all item in it'" icon="pi pi-trash"
                                        style="padding: 0px;" class="p-button-danger p-button-text p-button-sm mr-2"
                                        @click="deleteBoxBuildBox(box.id)"/>
                            </div>
                            <div class="row py-1">
                                <span style="font-size: 1rem;">Items: {{ box.items.length }}</span>
                            </div>
                        </div>
                    </template>
                    <template #content>
                        <div class="mb-3">
                            <div class="container">
                                <div class="row my-4">
                                    <ul class="list-group col-md-12" style="padding-right: 0;">
                                        <li v-for="item in box.items" style="width: 100%;"
                                            class="list-group-item d-flex justify-content-between align-items-center">
                                            {{ item.gf }}
                                            <Button v-tooltip="'Delete item'" icon="pi pi-trash" style="padding: 0px;"
                                                    class="p-button-danger p-button-text p-button-sm mr-2"
                                                    @click="deleteBoxBuildBoxItem(item.id)"/>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </template>
                    <template #footer>
                        <Button v-tooltip="'Close box and transfer items'" icon="fa-solid fa-arrow-down-up-lock"
                                style="padding: 0px;"
                                class="p-button-danger p-button-sm mr-2"
                                @click="closeBoxBuildBox(box.id)"/>
                    </template>
                </Card>
            </div>
        </div>

        <ConfirmDialog></ConfirmDialog>


    </div>

</template>

<script>
import Card from "primevue/card";
import InputText from "primevue/inputtext";
import Button from "primevue/button";
import Dialog from "primevue/dialog";
import ConfirmDialog from "primevue/confirmdialog";
import Paginator from "primevue/paginator";
import Dropdown from 'primevue/dropdown';
import InputSwitch from 'primevue/inputswitch';


export default {
    components: {
        'Card': Card,
        'InputText': InputText,
        'Button': Button,
        'Dialog': Dialog,
        'ConfirmDialog': ConfirmDialog,
        'Paginator': Paginator,
        'Dropdown': Dropdown,
        'InputSwitch': InputSwitch,
    },
    name: "BoxBuildScan",
    props: {
        userId: String,
    },
    data() {
        return {
            boxBuildCenterAudios: [],
            goodsFlow: null,
            openBoxes: [],
            directScan: false,
            directScanFulfilmentCenter: null,
            fulfilmentCenters: [],
            scanErrorAudio: new Audio('/storage/sounds/siren.MP3'),
        }
    },
    created() {
        Echo.channel('BoxBuildDirectScanCenterChangeEventChannel').listen('BoxBuildDirectScanCenterChangeEvent', (event) => {
            this.getDirectScanFulfilmentCenter();
            this.prepareBoxBuildAudioFiles();
        });
        Echo.channel('BoxBuildDirectScanStatusChangeEventChannel').listen('BoxBuildDirectScanStatusChangeEvent', (event) => {
            this.getDirectScanStatus();
            this.prepareBoxBuildAudioFiles();
        });
        Echo.channel('BoxBuildItemChangeEventChannel').listen('BoxBuildItemChangeEvent', (event) => {
            this.getOpenBoxBuildBoxes();
        });

        this.getActiveFulfilmentCenters();
        this.getDirectScanFulfilmentCenter();
        this.getDirectScanStatus();
        this.prepareBoxBuildAudioFiles();
        this.getOpenBoxBuildBoxes();
    },
    methods: {
        closeBoxBuildBox(id) {
            axios.post(route('closeBoxBuildBox'), {id: id})
                .then(response => {
                    this.getOpenBoxBuildBoxes();
                    this.$toast.add({
                        severity: 'success',
                        summary: 'Success',
                        detail: response.data.message,
                        life: 6000
                    });
                    this.printLabel(id);
                })
                .catch(error => {
                    this.$root.errorDisplay(error);
                });
            console.log(id);
        },
        printLabel(id) {
            window.open(route('boxBuildLabel', id));
        },
        deleteBoxBuildBoxItem(id) {
            axios.post(route('deleteBoxBuildBoxItem'), {id: id})
                .then(response => {
                    this.getOpenBoxBuildBoxes();
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
        deleteBoxBuildBox(id) {
            axios.post(route('deleteBoxBuildBox'), {id: id})
                .then(response => {
                    this.getOpenBoxBuildBoxes();
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
        getOpenBoxBuildBoxes() {
            axios.get(route('getOpenBoxBuildBoxes'))
                .then(response => {
                    this.openBoxes = response.data;
                })
                .catch(error => {
                    this.$root.errorDisplay(error);
                });
        },
        prepareBoxBuildAudioFiles() {
            axios.get(route('prepareBoxBuildAudioFiles'))
                .then(response => {
                    response.data.centers.forEach((centerId) => {
                        this.boxBuildCenterAudios[centerId] = new Audio('/storage/sounds/audio/boxBuild' + centerId + '-' + this.userId + '.MP3');
                    });
                    this.$toast.add({
                        severity: 'info',
                        summary: 'Info',
                        detail: response.data.message,
                        life: 6000
                    });
                })
                .catch(error => {
                    this.$root.errorDisplay(error);
                });
        },
        toggleDirectScanStatus() {
            axios.get(route('toggleDirectScanStatus'))
                .then(response => {
                    this.prepareBoxBuildAudioFiles();
                    this.getDirectScanStatus();
                    this.$toast.add({
                        severity: 'success',
                        summary: 'Success',
                        detail: response.data.message,
                        life: 6000
                    });
                })
                .catch(error => {
                    this.getDirectScanStatus();
                    this.$root.errorDisplay(error);
                });
        },
        getDirectScanStatus() {
            axios.get(route('getDirectScanStatus'))
                .then(response => {
                    this.directScan = response.data;
                })
                .catch(error => {
                    this.$root.errorDisplay(error);
                });
        },
        getActiveFulfilmentCenters() {
            axios.get(route('getFulfilmentCenters'))
                .then(response => {
                    response.data.forEach((item) => {
                        this.fulfilmentCenters[item.id] = item;
                    })
                    this.fulfilmentCenters = this.fulfilmentCenters.filter(function (el) {
                        return el != null;
                    });
                })
                .catch(error => {
                    this.$root.errorDisplay(error);
                });
        },
        submitGoodsFlowId() {
            axios.post(route('boxBuildSubmitGoodsFlowId'), {goodsFlow: this.goodsFlow})
                .then(response => {
                    if (response.data.to !== null) {
                        this.boxBuildCenterAudios[response.data.to].play();

                        this.$toast.add({
                            severity: 'success',
                            summary: 'Success',
                            detail: "Item added to: " + this.fulfilmentCenters[response.data.to]['name'],
                            life: 6000
                        });
                    } else {
                        this.$toast.add({
                            severity: 'info',
                            summary: 'Info',
                            detail: "Don't need",
                            life: 6000
                        });
                    }
                    this.getOpenBoxBuildBoxes();
                })
                .catch(error => {
                    this.$root.errorDisplay(error);
                    this.scanErrorAudio.play();
                });
            this.goodsFlow = null;
        },
        setDirectScanFulfilmentCenter() {
            axios.post(route('setDirectScanFulfilmentCenter'), {fulfilmentCenterId: this.directScanFulfilmentCenter.id})
                .then(response => {
                    this.getDirectScanFulfilmentCenter()
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
        getDirectScanFulfilmentCenter() {
            axios.get(route('getDirectScanFulfilmentCenter'))
                .then(response => {
                    this.directScanFulfilmentCenter = response.data;
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
