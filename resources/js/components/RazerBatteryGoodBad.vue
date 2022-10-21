<template>
    <div class="col-12 .xlsx">
        <ConfirmDialog></ConfirmDialog>
        <div class="row row-eq-height">
            <div class="col-md-6 mb-4">

                <Card class="p-3 h-100">
                    <template #title>
                        Upload list
                    </template>
                    <template #content>
                        <VueFileAgent
                            ref="vueFileAgent"
                            :theme="'list'"
                            :multiple="false"
                            :resumable="false"
                            :deletable="true"
                            :meta="true"
                            :accept="'.xlsx'"
                            :maxSize="'100MB'"
                            :maxFiles="1"
                            :helpText="'Choose xlsx file'"
                            :errorText="{
      type: 'Invalid file type. Only .xlsx Allowed',
      size: 'Files should not exceed 100MB in size',
    }"
                            @select="filesSelected($event)"
                            @beforedelete="onBeforeDelete($event)"
                            @delete="fileDeleted($event)"
                            v-model="fileRecords"
                            class="my-3"
                        ></VueFileAgent>
                        <Button label="Upload list" class="col-12 my-3" icon="pi pi-plus" iconPos="left"
                                :disabled="!fileRecordsForUpload.length" @click="uploadFiles()">
                        </Button>

                    </template>
                </Card>

            </div>
            <div class="col-md-6 mb-4">
                <Card class="p-3 h-100">
                    <template #title>
                        Download list
                    </template>
                    <template #content>
                        <Button label="Download list" class="col-12 my-3" icon="fa-solid fa-download" iconPos="left"
                                @click="downloadFile()">
                        </Button>
                    </template>
                </Card>

            </div>
            <div class="col-md-6 mb-4">
                <Card class="p-3 h-100">
                    <template #title>
                        Add
                    </template>
                    <template #content>
                        <div class="row">
                            <div class="col-12 mb-3">
                                <span class="col-12 p-input-icon-right p-0">
                                    <i :class="iconToDisplay" style="z-index:2;"></i>
                                <InputText class="col-12" id="rz" placeholder="Product ID (RZ02-01070300-R3M2)"
                                           type="text" @keyup="searchTimeOut" v-model="addRZ"/>
                                </span>
                            </div>
                            <div class="col-12 mb-3">
                                <span class="col-12 p-input-icon-right p-0">
                                    <i class="fa-solid fa-barcode" style="z-index:2;"></i>
                                <InputText class="col-12" id="rz" placeholder="EAN"
                                           type="text" v-model="addEan"/>
                                </span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="col-md-12 text-center"><i class="fa-solid fa-car-battery"></i> Battery</div>
                                <div class="col-md-12">
                                    <SelectButton class="text-center" v-model="addBattery"
                                                  :options="batteryScrapOptions"/>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="col-md-12 text-center"><i class="fa-solid fa-dumpster"></i> Direct scrap
                                </div>
                                <div class="col-md-12">
                                    <SelectButton class="text-center" v-model="addScrap"
                                                  :options="batteryScrapOptions"/>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <Button label="Add" class="col-12 my-3" icon="pi pi-plus" iconPos="left"
                                        :disabled="!addScrap || !addBattery || !addRZ || !addConfirmed" @click="add()">
                                </Button>
                            </div>
                        </div>
                    </template>
                </Card>

            </div>
            <div class="col-md-6 mb-4">
                <Card class="p-3 h-100">
                    <template #title>
                        Edit
                    </template>
                    <template #content>
                        <div class="row">
                            <AutoComplete class="col-12 mb-3" placeholder="Product ID (RZ02-01070300-R3M2)"
                                          v-model="editSelectedRZ" :suggestions="editFilteredRZs"
                                          @complete="editSearchRZ($event)" field="label" @item-select="editRZSelected()"
                                          @clear="editInputClear"/>
                            <div v-if="!dataToEdit" class="col-md-12 text-center">

                                <i class="fa-solid fa-pen-to-square fa-8x"></i>

                            </div>

                            <div class="col-12">
                                <InputText v-if="dataToEdit" class="col-12 mb-3" placeholder="EAN"
                                           type="text" v-model="dataToEdit.ean"/>
                            </div>

                            <div v-if="dataToEdit" class="col-md-6 mb-3">
                                <div class="col-md-12 text-center"><i class="fa-solid fa-car-battery"></i> Battery
                                </div>
                                <div class="col-md-12">
                                    <SelectButton class="text-center" v-model="dataToEdit.battery"
                                                  :options="batteryScrapOptions"/>
                                </div>
                            </div>
                            <div v-if="dataToEdit" class="col-md-6 mb-3">
                                <div class="col-md-12 text-center"><i class="fa-solid fa-dumpster"></i> Direct scrap
                                </div>
                                <div class="col-md-12">
                                    <SelectButton class="text-center" v-model="dataToEdit.scrap"
                                                  :options="batteryScrapOptions"/>
                                </div>
                            </div>
                            <div v-if="dataToEdit" class="col-md-12">
                                <div class="container">
                                    <div class="row mt-0">
                                        <Button label="Delete" class="btn-danger col-6 my-3" icon="pi pi-trash"
                                                iconPos="left"
                                                :disabled="!dataToEdit.rz || !dataToEdit.battery || !dataToEdit.scrap"
                                                @click="deleteRZ()">
                                        </Button>
                                        <Button label="Update" class="col-6 my-3" icon="fa-solid fa-pen-to-square"
                                                iconPos="left"
                                                :disabled="!dataToEdit.rz || !dataToEdit.battery || !dataToEdit.scrap"
                                                @click="editSubmitEditedRZ()">
                                        </Button>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </template>
                </Card>

            </div>
        </div>
    </div>
</template>


<script>
import Card from 'primevue/card';
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
import Timeline from "primevue/timeline";
import SelectButton from "primevue/selectbutton";
import AutoComplete from 'primevue/autocomplete';

export default {
    components: {
        'Card': Card,
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
        'Timeline': Timeline,
        'SelectButton': SelectButton,
        'AutoComplete': AutoComplete,
    },
    name: "RazerBatteryGoodBad",
    data() {
        return {
            editSelectedRZ: null,
            editFilteredRZs: [],
            dataToEdit: null,
            badIcon: 'fa-solid fa-xmark text-danger',
            goodIcon: 'fa-solid fa-check text-success',
            spinIcon: 'fa-solid fa-circle-notch fa-spin',
            neutralIcon: 'fa-solid fa-pen-clip',
            iconToDisplay: 'fa-solid fa-pen-clip',
            addConfirmed: false,
            addRZ: null,
            addEan: null,
            addScrap: 'No',
            addBattery: 'No',
            fileRecords: [],
            uploadUrl: route('RAZERBatteryGoodBadUpload'),
            uploadHeaders: {'X-Test-Header': 'vue-file-agent', 'X-CSRF-TOKEN': this.$root.csrf_token},
            fileRecordsForUpload: [],
            scanName: null,
            batteryScrapOptions: ['Yes', 'No'],
        };
    }, created() {

    },
    props: {
        authUserId: null,
    },
    methods: {
        deleteRZ() {
            this.$confirm.require({
                message: 'Are you sure you want to delete?',
                header: 'Confirmation',
                icon: 'pi pi-exclamation-triangle',
                accept: () => {
                    this.editSubmitRZForDelete()
                },
                reject: () => {
                    //
                }
            });
        },
        editSubmitRZForDelete() {
            axios.post(route('RAZERBatteryGoodBadSubmitRZForDelete', {'rz': this.dataToEdit.rz}))
                .then(response => {
                    this.dataToEdit = null;
                    this.editSelectedRZ = null;
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
        editSubmitEditedRZ() {
            axios.post(route('RAZERBatteryGoodBadSubmitEditedRZ', this.dataToEdit))
                .then(response => {
                    this.dataToEdit = null;
                    this.editSelectedRZ = null;
                    this.$toast.add({
                        severity: 'success',
                        summary: 'Success',
                        detail: response.data.message,
                        life: 6000
                    });
                })
                .catch(error => {
                    this.$toast.add({
                        severity: 'error',
                        summary: 'Error',
                        detail: error.response.data.message,
                        life: 6000
                    });
                });
        },
        editRZSelected() {
            axios.post(route('RAZERBatteryGoodBadEditRZSelected', {
                'rz': this.editSelectedRZ.rz
            }))
                .then(response => {
                    this.dataToEdit = response.data;
                })
                .catch(error => {
                    this.$root.errorDisplay(error);
                });
        },
        editInputClear() {
            this.dataToEdit = null;
        },
        editSearchRZ() {
            axios.post(route('RAZERBatteryGoodBadEditSearch', {
                'rz': this.editSelectedRZ
            }))
                .then(response => {
                    this.editFilteredRZs = response.data;
                })
                .catch(error => {
                    this.$root.errorDisplay(error);
                });
        },
        add() {
            axios.post(route('RAZERBatteryGoodBadAdd', {
                'rz': this.addRZ,
                'ean': this.addEan,
                'battery': this.addBattery,
                'scrap': this.addScrap
            }))
                .then(response => {
                    this.iconToDisplay = this.neutralIcon;
                    this.addConfirmed = false;
                    this.addScrap = 'No';
                    this.addBattery = 'No';
                    this.addRZ = null;
                    this.addEan = null;
                    this.$toast.add({
                        severity: 'success',
                        summary: 'Success',
                        detail: response.data.message,
                        life: 6000
                    });
                })
                .catch(error => {
                    this.confirmAdd();
                    this.$root.errorDisplay(error);
                });
        },
        downloadFile() {
            window.open(route('RAZERBatteryGoodBadDownload'));
        },
        searchTimeOut() {
            this.addConfirmed = false;
            if (this.timer) {
                clearTimeout(this.timer);
                this.timer = null;
            }
            this.timer = setTimeout(() => {
                this.confirmAdd();
            }, 800);
        },
        confirmAdd() {
            this.addConfirmed = false;
            this.iconToDisplay = this.spinIcon;
            if (this.addRZ != '') {
                axios.post(route('RAZERBatteryGoodBadAddConfirmation', {'rz': this.addRZ}))
                    .then(response => {
                        this.iconToDisplay = this.goodIcon;
                        this.addConfirmed = true;
                    })
                    .catch(error => {
                        this.addConfirmed = false;
                        this.iconToDisplay = this.badIcon;
                        this.$root.errorDisplay(error);
                    });
            } else {
                this.iconToDisplay = this.neutralIcon;
            }

        },
        uploadFiles: function () {
            this.$refs.vueFileAgent.upload(this.uploadUrl, this.uploadHeaders, this.fileRecordsForUpload, (fileData) => {
                let formData = new FormData();
                formData.append('file', fileData.file);
                return formData;
            }).then((response) => {
                if (response[0].data.message) {
                    this.$toast.add({
                        severity: 'success',
                        summary: 'Success',
                        detail: response[0].data.message,
                        life: 3000
                    });
                }
            }).catch((error) => {
                this.$root.errorDisplay(error);
            });
            this.fileRecordsForUpload = [];
            this.fileRecords = [];
        },
        deleteUploadedFile: function (fileRecord) {
            this.$refs.vueFileAgent.deleteUpload(this.uploadUrl, this.uploadHeaders, fileRecord);
        },
        filesSelected: function (fileRecordsNewlySelected) {
            var validFileRecords = fileRecordsNewlySelected.filter((fileRecord) => !fileRecord.error);
            this.fileRecordsForUpload = this.fileRecordsForUpload.concat(validFileRecords);
        },
        onBeforeDelete: function (fileRecord) {
            var i = this.fileRecordsForUpload.indexOf(fileRecord);
            if (i !== -1) {
                this.fileRecordsForUpload.splice(i, 1);
                var k = this.fileRecords.indexOf(fileRecord);
                if (k !== -1) this.fileRecords.splice(k, 1);
            } else {
                if (confirm('Are you sure you want to delete?')) {
                    this.$refs.vueFileAgent.deleteFileRecord(fileRecord);
                }
            }
        },
        fileDeleted: function (fileRecord) {
            var i = this.fileRecordsForUpload.indexOf(fileRecord);
            if (i !== -1) {
                this.fileRecordsForUpload.splice(i, 1);
            } else {
                this.deleteUploadedFile(fileRecord);
            }
        },

    }
}
</script>

<style>
.custom-marker {
    display: flex;
    width: 2rem;
    height: 2rem;
    align-items: center;
    justify-content: center;
    color: #ffffff;
    border-radius: 50%;
    z-index: 1;
}

.p-timeline-event-opposite {
    display: none;
}

.p-selectbutton .p-button.p-highlight {
    background: #6b9f69;
    border-color: #459f41;
    color: #ffffff;
}

.p-selectbutton .p-button.p-highlight:hover {
    background: #9eb99d;
    border-color: #459f41;
    color: #020202;
}

.p-selectbutton.p-button:hover {
    background: #9eb99d;
    border-color: #459f41;
    color: #020202;
}

.p-inputtext {
    width: 100%;
    padding: 15px;
}

.p-autocomplete-loader {
    margin-right: 20px;
}

.btn-danger:hover {
    color: #fff !important;
    background-color: #d0211c !important;
    border-color: #c51f1a !important;
}
</style>
