<template>

    <div class="row row-eq-height">
        <div class="col-md-6">

            <Card class="p-3 h-100">
                <template #title>
                    Create Box-Build
                </template>
                <template #content>
                    <div class="px-3">
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
                    </div>
                    <div class="mb-3">
                        <span class="p-float-label col-md-12 my-4">
                        <InputText id="sheetName" class="col-12"
                                   type="text" v-model="sheetName"/>
                            <label for="sheetName" class="ml-4">Sheet name</label>
                        </span>
                    </div>

                    <hr/>

                    <div class="mb-3">
                        <span class="p-float-label col-md-12 my-4">
                        <InputText class="col-12 text-uppercase" id="vidColumn"
                                   type="text" v-model="vidColumn"/>
                        <label for="vidColumn" class="ml-4">VID column</label>
                        </span>
                    </div>
                    <div class="mb-3">
                        <span class="p-float-label col-md-12 my-4">
                        <InputText class="col-12 text-uppercase" id="productConditionColumn"
                                   type="text" v-model="productConditionColumn"/>
                        <label for="productConditionColumn" class="ml-4">Product_Condition column</label>
                        </span>
                    </div>
                    <div class="mb-3">
                        <span class="p-float-label col-md-12 my-4">
                        <InputText class="col-12 text-uppercase" id="manufacturerColumn"
                                   type="text" v-model="manufacturerColumn"/>
                        <label for="manufacturerColumn" class="ml-4">Manufacturer column</label>
                        </span>
                    </div>

                    <hr/>

                    <Button label="Add fulfilment center" class="col-12 my-3 p-button-outlined" icon="pi pi-plus"
                            iconPos="left"
                            @click="addFulfilmentCenterDialog = true">
                    </Button>


                    <div v-for="item in fulfilmentCenterColumns" class="col-md-12">
                        <div class="p-inputgroup">
                            <span class="p-inputgroup-addon col-md-7">{{ item.name }}</span>
                            <InputText v-model="item.column" class="col-md-3 text-uppercase" placeholder="column"/>
                            <Button v-tooltip="'Remove'" class="col-2 my-3 py-1 p-button-text p-button-danger"
                                    icon="fa-solid fa-xmark"
                                    @click="removeFulfilmentCenterColumn(item)">
                            </Button>
                        </div>
                    </div>


                    <hr/>
                    <Button label="Upload list" class="col-12 my-3" icon="pi pi-plus" iconPos="left"
                            :disabled="!fileRecordsForUpload.length || !productConditionColumn || !vidColumn"
                            @click="uploadFiles()">
                    </Button>

                </template>
            </Card>
        </div>
        <div class="col-md-6">

            <Card class="p-3 h-100">
                <template #title>
                    Fulfilment centers
                </template>
                <template #content>
                    <div class="my-5">
                        <span class="p-float-label col-md-12 my-4">
                        <InputText class="col-12" id="newCenterName"
                                   type="text" v-model="newCenterName"></InputText>
                        <label for="newCenterName" class="ml-4">Name</label>
                        </span>
                    </div>
                    <div class="my-5">
                        <span class="p-float-label col-md-12 my-4 ">
                        <InputText class="col-12 text-uppercase" id="newCenterPrefix"
                                   type="text" v-model="newCenterPrefix"></InputText>
                        <label for="newCenterPrefix" class="ml-4">Box name prefix</label>
                        </span>
                    </div>
                    <div class="my-5">
                        <span class="p-float-label col-md-12 my-4">
                        <InputText class="col-12" id="newCenterPrefix"
                                   type="text" v-model="newCenterPalletId"></InputText>
                        <label for="newCenterPrefix" class="ml-4">Pallet ID</label>
                        </span>
                    </div>
                    <div class="mb-2">
                        <span class="p-float-label col-md-12 my-4">
                        <InputText class="col-12" id="newCenterAudioText"
                                   type="text" v-model="newCenterAudioText"></InputText>
                        <label for="newCenterAudioText" class="ml-4">Audio Text</label>
                        </span>
                    </div>
                    <Button label="Add new" class="col-12 my-1" icon="pi pi-plus" iconPos="left"
                            :disabled="!newCenterName || !newCenterAudioText"
                            @click="addNewWarehouseCenter()">
                    </Button>

                    <hr class="my-2"/>

                    <div class="list-group set-height-300">
                        <div v-for="item in allWarehouseCenters" class="list-group-item p-2">
                            <span>{{ item.name }}</span>
                            <span class="float-right">
                                <Button v-tooltip="'Edit'" class="p-button-text p-button-sm p-0" icon="fas fa-marker"
                                        @click="openWarehouseEdit(item.id)"></Button>
                            </span>
                        </div>
                    </div>


                </template>
            </Card>
        </div>

        <div class="col-md-12 my-4">
            <box-build-list ref="list"></box-build-list>
        </div>

        <Dialog header="Edit warehouse center" :visible.sync="editWarehouse" @hide="hideEdit()">
            <Card class="p-3 h-100">
                <template #content>
                    <div v-if="selectedWarehouse">
                        <div class="mt-2">
                             <span class="p-float-label col-md-12 my-4">
                            <InputText class="col-12" id="selectedWarehouseName"
                                       type="text" v-model="selectedWarehouse.name"/>
                            <label for="selectedWarehouseName" class="ml-4">Name</label>
                            </span>
                        </div>
                        <div class="mb-2">
                             <span class="p-float-label col-md-12 my-4">
                            <InputText class="col-12" id="selectedWarehousePrefix"
                                       type="text" v-model="selectedWarehouse.box_prefix"/>
                            <label for="selectedWarehousePrefix" class="ml-4">Box prefix</label>
                            </span>
                        </div>
                        <div class="mb-2">
                             <span class="p-float-label col-md-12 my-4">
                            <InputText class="col-12" id="selectedWarehousePrefix"
                                       type="text" v-model="selectedWarehouse.pallet_id"/>
                            <label for="selectedWarehousePrefix" class="ml-4">Pallet ID</label>
                            </span>
                        </div>
                        <div class="mb-2">
                             <span class="p-float-label col-md-12 my-4">
                            <InputText class="col-12" id="selectedWarehouseAudioText"
                                       type="text" v-model="selectedWarehouse.audio_text"/>
                            <label for="selectedWarehouseAudioText" class="ml-4">Audio Text</label>
                            </span>
                        </div>
                        <Button label="Edit" class="col-12 mt-3" icon="fas fa-marker" iconPos="left"
                                :disabled="!selectedWarehouse.name || !selectedWarehouse.audio_text"
                                @click="editWarehouseCenter()">
                        </Button>
                        <Button label="Delete" class="col-12 mt-3 p-button-danger" icon="fas fa-trash" iconPos="left"
                                @click="deleteWarehouseCenterConfirm()">
                        </Button>
                    </div>
                </template>
            </Card>
        </Dialog>


        <Dialog header="Add column" :visible.sync="addFulfilmentCenterDialog">
            <Card class="p-3 h-100">
                <template #content>

                    <div class="list-group set-height-300">
                        <div v-for="item in allWarehouseCenters" @click="addFulfilmentCenterColumn(item)"
                             class="list-group-item p-2">
                            <span>{{ item.name }}</span>
                        </div>
                    </div>

                </template>
            </Card>
        </Dialog>

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
import BoxBuildList from "./BoxBuildList"


export default {
    components: {
        'Card': Card,
        'InputText': InputText,
        'Button': Button,
        'Dialog': Dialog,
        'ConfirmDialog': ConfirmDialog,
        'Paginator': Paginator,
        'BoxBuildList': BoxBuildList,
    },
    name: "BoxBuildCreator",
    data() {
        return {
            fileRecords: [],
            fileRecordsForUpload: [],
            addFulfilmentCenterDialog: false,


            sheetName: null,
            vidColumn: null,
            manufacturerColumn: null,
            productConditionColumn: null,
            fulfilmentCenterColumns: [],

            uploadUrl: route('createBoxBuild'),
            uploadHeaders: {'X-Test-Header': 'vue-file-agent', 'X-CSRF-TOKEN': this.$root.csrf_token},
            newCenterName: null,
            newCenterAudioText: null,
            newCenterPrefix: null,
            newCenterPalletId: null,
            allWarehouseCenters: [],
            selectedWarehouse: null,
            editWarehouse: false,
        }
    },
    created() {
        this.getAllWarehouseCenters();
    },
    methods: {
        removeFulfilmentCenterColumn(ffCenter) {
            this.fulfilmentCenterColumns.splice(this.fulfilmentCenterColumns.indexOf(ffCenter), 1);
        },
        addFulfilmentCenterColumn(ffCenter) {
            this.fulfilmentCenterColumns.push(ffCenter);
        },
        deleteWarehouseCenterConfirm() {
            this.$confirm.require({
                message: 'Are you sure you want to delete this warehouse?',
                header: 'Confirmation',
                icon: 'pi pi-exclamation-triangle',
                accept: () => {
                    this.deleteWarehouseCenter();
                },
                reject: () => {
                }
            });
        },
        deleteWarehouseCenter() {
            axios.post(route('deleteWarehouseCenter'), {id: this.selectedWarehouse.id})
                .then(response => {
                    this.getAllWarehouseCenters();
                    this.hideEdit();
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
        hideEdit() {
            this.editWarehouse = false;
            this.selectedWarehouse = null;
        },
        editWarehouseCenter() {
            axios.post(route('editWarehouseCenter'), {
                id: this.selectedWarehouse.id,
                name: this.selectedWarehouse.name,
                box_prefix: this.selectedWarehouse.box_prefix,
                pallet_id: this.selectedWarehouse.pallet_id,
                audio_text: this.selectedWarehouse.audio_text
            })
                .then(response => {
                    this.getAllWarehouseCenters();
                    this.hideEdit();
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
        openWarehouseEdit(id) {
            this.editWarehouse = true;
            this.getSelectedWarehouseData(id);
        },
        getSelectedWarehouseData(id) {
            axios.post(route('getSelectedWarehouseData'), {id: id})
                .then(response => {
                    this.selectedWarehouse = response.data;
                })
                .catch(error => {
                    this.$root.errorDisplay(error);
                });
        },
        addNewWarehouseCenter() {
            axios.post(route('addNewWarehouseCenter', {
                name: this.newCenterName,
                audio_text: this.newCenterAudioText,
                box_prefix: this.newCenterPrefix,
                pallet_id: this.newCenterPalletId,
            }))
                .then(response => {
                    this.getAllWarehouseCenters();
                    this.newCenterPrefix = null;
                    this.newCenterName = null;
                    this.newCenterAudioText = null;
                    this.newCenterPalletId = null;
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
        getAllWarehouseCenters() {
            axios.get(route('getAllWarehouseCenters'))
                .then(response => {
                    this.allWarehouseCenters = response.data;
                })
                .catch(error => {
                    this.$root.errorDisplay(error);
                });
        },
        uploadFiles: function () {
            this.$refs.vueFileAgent.upload(this.uploadUrl, this.uploadHeaders, this.fileRecordsForUpload, (fileData) => {
                let formData = new FormData();
                formData.append('file', fileData.file);
                formData.append('manufacturer', this.manufacturerColumn);
                formData.append('vid_column', this.vidColumn);
                formData.append('product_condition_column', this.productConditionColumn);
                formData.append('sheet_name', this.sheetName);
                this.fulfilmentCenterColumns.forEach((item) => {
                    formData.append('fulfilment_centers[]', JSON.stringify(item));
                });
                return formData;
            }).then((response) => {
                this.sheetName = null;
                this.vidColumn = null;
                this.manufacturerColumn = null;
                this.productConditionColumn = null;
                this.fulfilmentCenterColumns = [];
                this.$refs.list.getAllBoxBuilds();
                if (response[0].data.message) {
                    this.$toast.add({
                        severity: 'success',
                        summary: 'Success',
                        detail: response[0].data.message,
                        life: 3000
                    });
                }
            }).catch((error) => {
                this.$root.errorDisplay(error[0]);
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

<style scoped>

</style>
