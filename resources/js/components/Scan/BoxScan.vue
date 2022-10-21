<template>
    <div class="row">
        <Toast position="top-right"/>
        <div class="col-md-4">
            <Panel>
                <template #header>
                    <InputText :disabled="disableInput" type="text" v-model="inputValue" @keyup.enter="send"
                               class="col-12 p-inputtext-sm"/>
                </template>
                <div class="row p-3">
                    <Button label="Create new Scan" icon="fa-solid fa-plus" @click="createNewScan" iconPos="left"
                            class="col-12 my-1"></Button>
                    <Button :disabled="disableInput" label="Close Scan" icon="fa-solid fa-lock"
                            @click="closeScanConfirm"
                            iconPos="left"
                            class="col-12 p-button-danger my-1"></Button>
                </div>
                <div class="row p-3">
                    <div v-for="scan in closedScans" @click="openOldScan(scan.id)"
                         class="btn col-12 p-2 border my-1 hover">
                        {{ scan.name }}
                    </div>
                </div>
            </Panel>
        </div>
        <div class="col-md-8">
            <Panel>
                <template #header>
                    Header Content
                </template>
                <TabView :activeIndex="activeIndex">
                    <TabPanel>
                        <template #header>
                            <i class="fa-solid fa-table-columns mx-2"></i>
                            <span>All GoodsFlows</span>
                        </template>
                        <div class="container">
                            <div class="row">
                                <div v-for="gf in needToScanGoodsFlows" class="col-lg-3 col-md-4 col-xs-12 p-1">
                                    <Chip :class="duplicates(gf.goodsflow)"
                                          style="font-family: Consolas,monaco,monospace;" class="" v-bind:key="gf.id">
                                        {{ gf.goodsflow }}
                                        <Button icon="fa-solid fa-circle-info"
                                                class="p-button-rounded p-button-text p-button-plain"/>
                                        <Dialog header="Header">
                                            Content
                                        </Dialog>
                                    </Chip>
                                </div>
                            </div>
                        </div>
                    </TabPanel>
                    <TabPanel>
                        <template #header>
                            <i class="fa-solid fa-list mx-2"></i>
                            <span>Scanned GoodsFlows</span>
                        </template>
                        <div v-for="gf in scannedGoodsFlows" class="col-lg-12 col-md-12 col-xs-12 p-1">
                            <Chip :class="duplicates(gf.goods_flow_id)"
                                  style="font-family: Consolas,monaco,monospace;" class="" v-bind:key="gf.id">
                                {{ gf.user.name }} {{ gf.goods_flow_id }}
                                <Button icon="fa-solid fa-circle-info"
                                        class="p-button-rounded p-button-text p-button-plain"/>
                                <Dialog header="Header">
                                    Content
                                </Dialog>
                            </Chip>
                        </div>

                    </TabPanel>
                </TabView>

            </Panel>
        </div>
        <Dialog header="Header" :visible.sync="displayOldScan" :hide="closeOldScan" :containerStyle="{width: '50vw'}"
                :modal="true" :closable="false" :closeOnEscape="false">

            <TabView :activeIndex="activeIndex">
                <TabPanel>
                    <template #header>
                        <i class="fa-solid fa-table-columns mx-2"></i>
                        <span>All GoodsFlows</span>
                    </template>


                    <div class="container align-content-center align-items-center text-center">
                        <ProgressSpinner v-if="loadingOld" style="width:50px;height:50px" class="" strokeWidth="6"
                                         animationDuration=".5s"/>
                        <div class="row">
                            <div v-for="gf in needToScanGoodsFlowsOld" class="col-lg-3 col-md-4 sol-sm-6 col-xs-12 p-1">
                                <Chip :class="duplicatesOld(gf.goodsflow)"
                                      style="font-family: Consolas,monaco,monospace;" v-bind:key="gf.id">
                                    {{ gf.goodsflow }}
                                </Chip>
                            </div>
                        </div>
                    </div>
                </TabPanel>
                <TabPanel>
                    <template #header>
                        <i class="fa-solid fa-list mx-2"></i>
                        <span>Scanned GoodsFlows</span>
                    </template>
                    <div class="container">
                        <div class="row">
                            <div v-for="gf in scannedGoodsFlowsOld" class="col-lg-12 col-md-12 col-xs-12 p-1">
                                <Chip :class="duplicatesOld(gf.goods_flow_id)"
                                      style="font-family: Consolas,monaco,monospace;" class="col-12"
                                      v-bind:key="gf.id">
                                    {{ gf.user.name }} {{ gf.goods_flow_id }}
                                </Chip>
                            </div>
                        </div>
                    </div>
                </TabPanel>
            </TabView>
            <template #footer>
                <Button label="Close" icon="fa-solid fa-xmark" class="my-3 mr-3" @click="closeOldScan" autofocus/>
            </template>
        </Dialog>
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
import Toast from 'primevue/toast';
import ProgressSpinner from 'primevue/progressspinner';
import TabView from 'primevue/tabview';
import TabPanel from 'primevue/tabpanel';
import ConfirmDialog from 'primevue/confirmdialog';
import Listbox from 'primevue/listbox';


export default {
    components: {
        'InputText': InputText,
        'SplitButton': SplitButton,
        'Button': Button,
        'Dialog': Dialog,
        'InputNumber': InputNumber,
        'Panel': Panel,
        'Chip': Chip,
        'Toast': Toast,
        'ProgressSpinner': ProgressSpinner,
        'TabView': TabView,
        'TabPanel': TabPanel,
        'ConfirmDialog': ConfirmDialog,
        'Listbox': Listbox,
    },
    name: "BoxScan",
    props: {
        boxId: String,
    },
    data() {
        return {
            activeIndex: 0,
            loading: false,
            loadingOld: false,
            scannedGoodsFlows: [],
            scannedGoodsFlowsGFOnly: [],
            needToScanGoodsFlowsGFOnly: [],
            needToScanGoodsFlows: [],
            scannedGoodsFlowsOld: [],
            scannedGoodsFlowsGFOnlyOld: [],
            needToScanGoodsFlowsGFOnlyOld: [],
            needToScanGoodsFlowsOld: [],
            inputValue: null,
            scanId: null,
            disableInput: true,
            closedScans: [],
            selectedScan: null,
            displayOldScan: false,
        }
    },
    created() {
        let audio = new Audio('/storage/sounds/siren.MP3');

        Echo.channel('CreateScanChannel' + this.boxId).listen('CreateScan', (event) => {
            this.scanId = event.scan.id;
            this.getNeedToScanGFIds();
            this.getScannedGfs();
            this.disableInput = false;
        });

        Echo.channel('CloseScanChannel').listen('CloseScan', (event) => {
            if (event.scanId == this.scanId) {
                this.scanId = null;
                this.disableInput = true;
                this.scannedGoodsFlows = [];
                this.scannedGoodsFlowsGFOnly = [];
                this.needToScanGoodsFlowsGFOnly = [];
                this.needToScanGoodsFlows = [];
                this.closedScans.unshift(event.scan);
            }
        });

        Echo.channel('ItemScanChannel' + this.boxId).listen('ItemScan', (event) => {
            if (this.scannedGoodsFlowsGFOnly.includes(event.goodsFlowId.goods_flow_id)) {
                audio.play();
            }
            if (this.needToScanGoodsFlowsGFOnly.includes(event.goodsFlowId.goods_flow_id) === false) {
                audio.play();
            }
            this.scannedGoodsFlows.unshift(event.goodsFlowId);
            this.scannedGoodsFlowsGFOnly.unshift(event.goodsFlowId.goods_flow_id);

        });
    },
    mounted() {
        this.getOpenScanIdForBox();
        this.getClosedScansForBox();

    },
    methods: {
        send() {
            axios.interceptors.request.use(req => {
                this.inputValue = null;
                return req;
            });
            axios.post(route('submitGf', {
                'gf': this.inputValue,
                'boxId': this.boxId,
                'scanId': this.scanId
            })).catch((error) => {
                this.$root.errorDisplay(error);
            });

        },
        getScannedGfs() {
            axios.post(route('getScannedGfIds', {'boxId': this.boxId, 'scanId': this.scanId}))
                .then(response => {
                    this.scannedGoodsFlows = response.data;
                    response.data.forEach((value, index) => {
                        this.scannedGoodsFlowsGFOnly.push(value.goods_flow_id);
                    })

                })
                .catch(error => {
                    this.$root.errorDisplay(error);
                })
        },
        getScannedGfsOld(bId, sId) {
            axios.post(route('getScannedGfIds', {'boxId': bId, 'scanId': sId}))
                .then(response => {
                    this.scannedGoodsFlowsOld = response.data;
                    response.data.forEach((value, index) => {
                        this.scannedGoodsFlowsGFOnlyOld.push(value.goods_flow_id);
                    })
                    this.loadingOld = false;

                })
                .catch(error => {
                    this.$root.errorDisplay(error);
                })
        },
        createNewScan() {
            axios.post(route('createBoxScan', {'boxId': this.boxId}))
                .then(response => {
                    this.loading = false;
                })
                .catch(error => {
                    this.$root.errorDisplay(error);
                });
        },
        closeScanConfirm() {
            this.$confirm.require({
                message: 'Are you sure you want to close this scan?',
                header: 'Confirmation',
                icon: 'pi pi-exclamation-triangle',
                accept: () => {
                    this.closeScan();
                },
                reject: () => {
                    //callback to execute when user rejects the action
                }
            });
        },
        closeScan() {
            axios.post(route('closeScan', {'scanId': this.scanId}))
                .then(response => {
                    //
                })
                .catch(error => {
                    this.$root.errorDisplay(error);
                });
        },
        getOpenScanIdForBox() {
            axios.post(route('getOpenScanIdForBox', {'boxId': this.boxId}))
                .then(response => {
                    this.scanId = response.data.id;
                    if (this.scanId != null) {
                        this.getNeedToScanGFIds();
                        this.getScannedGfs();
                        this.disableInput = false;
                    } else {
                        this.disableInput = true;
                    }
                })
                .catch(error => {
                    this.$root.errorDisplay(error);
                });
        },
        getNeedToScanGFIds() {
            axios.post(route('getNeedToScanGFIds', {'boxId': this.boxId, 'scanId': this.scanId}))
                .then(response => {
                    this.needToScanGoodsFlows = response.data;
                    response.data.forEach((value, index) => {
                        this.needToScanGoodsFlowsGFOnly.push(value.goodsflow);
                    })
                })
                .catch(error => {
                    this.$root.errorDisplay(error);
                });
        },
        getNeedToScanGFIdsOld(bId, sId) {
            axios.post(route('getNeedToScanGFIds', {'boxId': bId, 'scanId': sId}))
                .then(response => {
                    this.needToScanGoodsFlowsOld = response.data;
                    response.data.forEach((value, index) => {
                        this.needToScanGoodsFlowsGFOnlyOld.push(value.goodsflow);
                    })
                })
                .catch(error => {
                    this.$root.errorDisplay(error);
                });
        },
        getClosedScansForBox() {
            axios.post(route('getClosedScansForBox', {'boxId': this.boxId}))
                .then(response => {
                    this.closedScans = response.data;
                })
                .catch(error => {
                    this.$root.errorDisplay(error);
                });
        },
        openOldScan(id) {
            this.loadingOld = true;
            this.getNeedToScanGFIdsOld(this.boxId, id);
            this.getScannedGfsOld(this.boxId, id);
            this.displayOldScan = true;
        },
        closeOldScan() {
            this.displayOldScan = false;
            this.scannedGoodsFlowsOld = [];
            this.scannedGoodsFlowsGFOnlyOld = [];
            this.needToScanGoodsFlowsGFOnlyOld = [];
            this.needToScanGoodsFlowsOld = [];
        },
        duplicates(gf) {
            let c = 0;
            this.scannedGoodsFlowsGFOnly.forEach((value, index) => {
                if (value == gf) {
                    c++;
                }
            })
            if ((c > 1) && (this.needToScanGoodsFlowsGFOnly.includes(gf))) {
                return "danger";
            } else if ((c == 1) && (this.needToScanGoodsFlowsGFOnly.includes(gf))) {
                return "success";
            } else {
                return "";
            }

        },
        duplicatesOld(gf) {
            let c = 0;
            this.scannedGoodsFlowsGFOnlyOld.forEach((value, index) => {
                if (value == gf) {
                    c++;
                }
            })
            if ((c > 1) && (this.needToScanGoodsFlowsGFOnlyOld.includes(gf))) {
                return "danger";
            } else if ((c == 1) && (this.needToScanGoodsFlowsGFOnlyOld.includes(gf))) {
                return "success";
            } else {
                return "";
            }

        }
    }
}
</script>

<style scoped>
.p-chip.success {
    background: var(--success);
    color: var(--primary-color-text);
}

.p-chip.danger {
    background: var(--danger) !important;
    color: var(--primary-color-text);
}

.border {
    border: 1px solid lightslategrey;
    border-radius: 3px;
}

.hover:hover {
    background-color: #c2c2c2;
}

.p-tabview .p-tabview-panel {
    background: #ffffff;
    padding: 1rem;
    border: 0 none;
    color: #323130;
    border-bottom-right-radius: 2px;
    border-bottom-left-radius: 2px;
    overflow-y: scroll;
    height: 400px;
}

</style>
