<template>
    <div class="col-12 .xlsx">
        <div class="row row-eq-height">
            <div class="col-md-6">

                <Card class="p-3 h-100">
                    <template #title>
                        Scans
                    </template>
                    <template #content>

                        <TabView>
                            <TabPanel header="New">
                                <div class="row h-100">
                                    <Skeleton class="m-1" v-if="loadingListNew" v-for="n in 10" v-bind:key="n"
                                              width="100%" height="3rem"/>
                                    <div v-for="(item, index) in newData" class="col-12">
                                        <Button @click="goToScan(item.id)"
                                                class="col-12 my-1 p-button-raised p-button-secondary p-button-text">
                                            {{ item.name }}
                                        </Button>
                                    </div>
                                    <div v-if="newData.length === 0 && !loadingListNew">
                                        <img src="storage/images/unDraw/undraw_empty_re_opql.svg" alt="Empty"
                                             class="max-width100">
                                    </div>
                                </div>
                                <Paginator :rows.sync="newRows" :first="newFirst" :rows="10"
                                           @page="pageClickNew($event)" :totalRecords="newTotalItemsCount"></Paginator>
                            </TabPanel>
                            <TabPanel header="Done">
                                <div class="row h-100">
                                    <Skeleton class="m-1" v-if="loadingListDone" v-for="n in 10" v-bind:key="n"
                                              width="100%" height="3rem"/>
                                    <div v-for="(item, index) in doneData" class="col-12">
                                        <Button @click="goToScan(item.id)"
                                                class="col-12 my-1 p-button-raised p-button-secondary p-button-text">
                                            {{ item.name }}
                                        </Button>
                                    </div>
                                    <div v-if="doneData.length === 0 && !loadingListDone">
                                        <img src="storage/images/unDraw/undraw_empty_re_opql.svg" alt="Empty"
                                             class="max-width100">
                                    </div>
                                </div>
                                <Paginator :rows.sync="doneRows" :first="doneFirst" :rows="10"
                                           @page="pageClickDone($event)"
                                           :totalRecords="doneTotalItemsCount"></Paginator>
                            </TabPanel>
                            <TabPanel header="Confirmed">
                                <div class="row h-100">
                                    <Skeleton class="m-1" v-if="loadingListConfirmed" v-for="n in 10" v-bind:key="n"
                                              width="100%" height="3rem"/>
                                    <div v-for="(item, index) in confirmedData" class="col-12">
                                        <Button @click="goToScan(item.id)"
                                                class="col-12 my-1 p-button-raised p-button-secondary p-button-text">
                                            {{ item.name }}
                                        </Button>
                                    </div>
                                    <div v-if="confirmedData.length === 0 && !loadingListConfirmed">
                                        <img src="/storage/images/unDraw/undraw_empty_re_opql.svg" alt="Empty"
                                             class="max-width100">
                                    </div>
                                </div>
                                <Paginator :rows.sync="confirmedRows" :first="confirmedFirst" :rows="10"
                                           @page="pageClickConfirmed($event)"
                                           :totalRecords="confirmedTotalItemsCount"></Paginator>
                            </TabPanel>
                        </TabView>

                    </template>
                </Card>

            </div>
            <div class="col-md-6">
                <Card class="p-3 h-100">
                    <template #title>
                        Create Scan
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
                        <InputText class="col-12 my-3" placeholder="RMA" v-model="scanRMA"></InputText>
                        <InputText class="col-12 my-3" placeholder="Sender" v-model="scanSender"></InputText>

                        <span class="p-buttonset">
                            <div class="row col-12">
                        <Button label="Create scan" class="col-8 my-3" icon="pi pi-plus" iconPos="left"
                                :disabled="((fileRecordsForUpload.length===0) || (scanSender === null || scanSender === '') || (scanRMA === null || scanRMA === ''))"
                                @click="uploadFiles()">
                        </Button>
                            <Button label="Empty" class="col-4 my-3" icon="pi pi-plus" iconPos="left"
                                    :disabled="((fileRecordsForUpload.length!==0) || (scanSender === null || scanSender === '') || (scanRMA === null || scanRMA === ''))"
                                    @click="createNewEmptyItemScan()">
                        </Button>
                            </div>
                        </span>

                        <Card class="p-2">
                            <template #content>
                                <h4 class="mb-3">Progress</h4>
                                <Timeline :value="events" align="left" class="customized-timeline">
                                    <template #marker="slotProps">
		<span class="custom-marker shadow-2" :style="{backgroundColor: slotProps.item.color}">
			<i :class="slotProps.item.icon"></i>
		</span>
                                    </template>
                                    <template #content="slotProps">
                                        <Card class="p-2">
                                            <template #subtitle>
                                                <div class="row">
                                                    <div class="col-10">{{ slotProps.item.text }}</div>
                                                    <div class="col-2" v-if="slotProps.item.chunks !== null">
                                                        {{ slotProps.item.chunks }}
                                                    </div>
                                                </div>
                                            </template>
                                        </Card>
                                    </template>
                                </Timeline>
                            </template>
                        </Card>

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
import Paginator from "primevue/paginator";
import Skeleton from "primevue/skeleton";


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
        'Paginator': Paginator,
        'Skeleton': Skeleton,
    },
    name: "NewItemScan",
    data() {
        return {
            loadingListConfirmed: true,
            loadingListNew: true,
            loadingListDone: true,
            confirmedRows: 10,
            confirmedPageNumber: 1,
            confirmedData: [],
            confirmedTotalItemsCount: null,
            confirmedFirst: null,
            doneRows: 10,
            donePageNumber: 1,
            doneData: [],
            doneTotalItemsCount: null,
            doneFirst: null,
            newRows: 10,
            newPageNumber: 1,
            newData: [],
            newTotalItemsCount: null,
            newFirst: null,
            spinnerIcon: 'fas fa-spinner fa-pulse',
            errorIcon: 'fas fa-exclamation',
            goodIcon: 'pi pi-check',
            goodColor: '#2fa415',
            badColor: '#607D8B',
            errorColor: '#e13838',
            keys: ['fileSelected', 'fileUploaded', 'fileRed', 'fileMakingChunks', 'fileUploadingChunks', 'scanCreated'],
            events: {
                fileSelected: {icon: 'pi pi-check', color: '#607D8B', text: 'Select xlsx file'},
                fileUploaded: {icon: 'pi pi-check', color: '#607D8B', text: 'Upload file to server'},
                fileRed: {icon: 'pi pi-check', color: '#607D8B', text: 'Read file'},
                fileMakingChunks: {icon: 'pi pi-check', color: '#607D8B', text: 'Split file to chunks', chunks: 0},
                fileUploadingChunks: {
                    icon: 'pi pi-check',
                    color: '#607D8B',
                    text: 'Upload chunks to database',
                    chunks: 0
                },
                scanCreated: {icon: 'pi pi-check', color: '#607D8B', text: 'Done'},
            },
            fileRecords: [],
            uploadUrl: route('createNewItemScan'),
            uploadHeaders: {'X-Test-Header': 'vue-file-agent', 'X-CSRF-TOKEN': this.$root.csrf_token},
            fileRecordsForUpload: [],
            scanRMA: null,
            scanSender: null,
        };
    }, created() {
        this.getNewScans();
        this.getDoneScans();
        this.getConfirmedScans();

        Echo.channel('NewScanFileUploadChannel' + this.authUserId).listen('NewScanFileUpload', (event) => {
            Object.keys(event.states).forEach(key => {
                if (key === 'chunksCreated') {
                    this.setChunks('fileMakingChunks', event.states[key]);
                } else if (key === 'chunksUploaded') {
                    this.setChunks('fileUploadingChunks', event.states[key]);
                } else {
                    if (event.states[key]) {
                        this.setGoodColor(key);
                    } else {
                        this.setBadColor(key);
                    }
                }
            });
        });
        Echo.channel('NewItemScanListChangeChanel').listen('NewItemScanListChange', (event) => {
            this.getNewScans();
            this.getDoneScans();
            this.getConfirmedScans();
        });
    },
    props: {
        authUserId: null,
    },
    methods: {
        createNewEmptyItemScan() {
            axios.post(route('createNewEmptyItemScan', {sender: this.scanSender, rma: this.scanRMA}))
                .then(response => {
                    this.getNewScans();
                    this.getDoneScans();
                    this.getConfirmedScans();
                    this.fileRecordsForUpload = [];
                    this.fileRecords = [];
                    this.scanRMA = null;
                    this.scanSender = null;
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
        goToScan(id) {
            window.open(route('GoToScan', {'id': id}), '_self');
        },
        pageClickConfirmed(event) {
            this.confirmedPageNumber = event.page + 1;
            this.getConfirmedScans();
        },
        getConfirmedScans() {
            axios.get(route('getConfirmedScans', {perPage: this.confirmedRows, pageNumber: this.confirmedPageNumber}))
                .then(response => {
                    this.confirmedTotalItemsCount = response.data.total;
                    this.confirmedData = response.data.data;
                    this.loadingListConfirmed = false;
                })
                .catch(error => {
                    this.$root.errorDisplay(error);
                });
        },

        pageClickDone(event) {
            this.donePageNumber = event.page + 1;
            this.getDoneScans();
        },
        getDoneScans() {
            axios.get(route('getDoneScans', {perPage: this.doneRows, pageNumber: this.donePageNumber}))
                .then(response => {
                    this.doneTotalItemsCount = response.data.total;
                    this.doneData = response.data.data;
                    this.loadingListDone = false;
                })
                .catch(error => {
                    this.$root.errorDisplay(error);
                });
        },


        pageClickNew(event) {
            this.newPageNumber = event.page + 1;
            this.getNewScans();
        },
        getNewScans() {
            axios.get(route('getNewScans', {perPage: this.newRows, pageNumber: this.newPageNumber}))
                .then(response => {
                    this.newTotalItemsCount = response.data.total;
                    this.newData = response.data.data;
                    this.loadingListNew = false;
                })
                .catch(error => {
                    this.$root.errorDisplay(error);
                });
        },
        uploadFiles: function () {
            this.$refs.vueFileAgent.upload(this.uploadUrl, this.uploadHeaders, this.fileRecordsForUpload, (fileData) => {
                let formData = new FormData();
                formData.append('rma', this.scanRMA);
                formData.append('sender', this.scanSender);
                formData.append('file', fileData.file);
                return formData;
            }).catch((error) => {
                this.showErrorIcon();
                this.$root.errorDisplay(error[0]);
            });
            this.fileRecordsForUpload = [];
            this.fileRecords = [];
            this.scanRMA = null;
            this.scanSender = null;
        },
        deleteUploadedFile: function (fileRecord) {
            this.$refs.vueFileAgent.deleteUpload(this.uploadUrl, this.uploadHeaders, fileRecord);
        },
        filesSelected: function (fileRecordsNewlySelected) {
            var validFileRecords = fileRecordsNewlySelected.filter((fileRecord) => !fileRecord.error);
            this.fileRecordsForUpload = this.fileRecordsForUpload.concat(validFileRecords);
            this.resetEvents();
            this.timelineFileSelected('fileSelected');
        },
        onBeforeDelete: function (fileRecord) {
            var i = this.fileRecordsForUpload.indexOf(fileRecord);
            if (i !== -1) {
                this.fileRecordsForUpload.splice(i, 1);
                var k = this.fileRecords.indexOf(fileRecord);
                if (k !== -1) this.fileRecords.splice(k, 1);
                this.resetEvents();
            } else {
                if (confirm('Are you sure you want to delete?')) {
                    this.$refs.vueFileAgent.deleteFileRecord(fileRecord); // will trigger 'delete' event
                    this.resetEvents();
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
            this.resetEvents();
            this.scanRMA = null;
            this.scanSender = null;
        },
        removeExtension(filename) {
            return filename.substring(0, filename.lastIndexOf('.')) || filename;
        },
        timelineFileSelected(key) {
            this.events[key].color = this.goodColor;
            this.loadingNext(key);
        },
        setGoodColor(key) {
            this.events[key].color = this.goodColor;
            this.events[key].icon = this.goodIcon;
            this.loadingNext(key);
        },
        setBadColor(key) {
            this.events[key].color = this.badColor;
        },
        setChunks(key, number) {
            this.events[key].chunks = number;
        },
        loadingNext(key) {
            if (this.getNextKey(key)) {
                this.events[this.getNextKey(key)].icon = this.spinnerIcon;
            }
        },
        getNextKey(k) {
            let keyString = null;
            let next = null;
            this.keys.forEach((value, key) => {
                if (k === value) {
                    next = key + 1;
                }
                if (next !== null && key === next) {
                    keyString = value;
                }
            })
            return keyString;
        },
        showErrorIcon() {
            Object.keys(this.events).every(key => {
                if (this.events[key].color === this.badColor) {
                    this.events[key].color = this.errorColor;
                    this.events[key].icon = this.errorIcon;
                    return false;
                } else {
                    return true;
                }
            });

        },
        resetEvents() {
            this.events = {
                fileSelected: {icon: 'pi pi-check', color: '#607D8B', text: 'Select xlsx file'},
                fileUploaded: {icon: 'pi pi-check', color: '#607D8B', text: 'Upload file to server'},
                fileRed: {icon: 'pi pi-check', color: '#607D8B', text: 'Read file'},
                fileMakingChunks: {icon: 'pi pi-check', color: '#607D8B', text: 'Split file to chunks', chunks: 0},
                fileUploadingChunks: {
                    icon: 'pi pi-check',
                    color: '#607D8B',
                    text: 'Upload chunks to database',
                    chunks: 0
                },
                scanCreated: {icon: 'pi pi-check', color: '#607D8B', text: 'Done'},
            };
        }

    }
}
</script>

<style scoped>
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

.p-tabview .p-tabview-panels {
    background: #ffffff;
    padding: 1rem;
    border: 0 none;
    color: #323130;
    border-bottom-right-radius: 2px;
    border-bottom-left-radius: 2px;
    overflow-y: scroll;
    height: 100%;
    margin-top: 5px !important;
}

.p-card .p-card-body {
    padding: 0;
    height: 100%;
}

.max-width100 {
    max-width: 100%;
}

</style>
