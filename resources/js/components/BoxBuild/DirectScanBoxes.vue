<template>
    <div>
        <Card class="p-3 h-100">
            <template #title>
                <div class="container">
                    Direct Scan Boxes
                </div>
            </template>
            <template #header></template>
            <template #content>
                <div class="container">
                    <div class="row">

                        <div class="col-md-6 my-1">
                            <Calendar v-model="date" :showIcon="true" selectionMode="range" :manualInput="false"
                                      dateFormat="yy-mm-dd" class="col-md-12 p-0"
                                      placeholder="From - To"
                                      @date-select="getAllBoxes()"/>
                        </div>

                        <span class="p-input-icon-left col-md-6 my-1">
                        <i class="pi pi-search pl-4"></i>
                        <InputText v-model="boxSearchString" type="text" @change="getAllBoxes()"
                                   class=""></InputText>
            </span>

                    </div>

                    <div class="row box-field-height py-3">
                        <div v-for="box in boxes" class="col-md-4 mb-3">
                            <div class="card">
                                <div class="card-header">
                                    {{ box.name }}
                                </div>
                                <div class="card-body">
                                    <p class="card-text"><span style="font-weight: bold;">Items in the box:</span>
                                        {{ box.items.length }}</p>
                                    <Button v-if="box.active === 0" @click="printLabel(box.id)" class="btn btn-primary">
                                        Print label
                                    </Button>
                                    <Button v-if="box.active === 0" @click="viewBoxItems(box.id)"
                                            class="btn btn-primary">
                                        View Items
                                    </Button>
                                    <Button v-tooltip="'Delete Box and all items in it.'" v-if="box.active === 0"
                                            @click="deleteClosedBoxConfirmation(box.id)"
                                            class="btn btn-primary">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </Button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <Paginator :first.sync="first" :rows="rows" :totalRecords="totalBoxesCount"
                               @page="boxPageClick($event)"></Paginator>
                </div>
            </template>
        </Card>

        <Dialog :visible.sync="itemDialog" :style="{width: '70%'}" header="Search results"
                :modal="true" class="p-fluid" @hide="itemDialog = false; itemsInBox = null;">
            <div class="container">
                <ProgressSpinner style="display: flex;" v-if="!itemsInBox"/>
                <div v-if="itemsInBox">
                    <ul class="list-group col-md-12" style="max-height: 500px; overflow-y: scroll; padding-right: 0;">
                        <li v-for="item in itemsInBox" style="width: 100%;"
                            class="list-group-item d-flex justify-content-between align-items-center">
                            <div class="row col-md-12">
                                <div class="col-md-4">
                                    {{ item.gf }}
                                </div>
                                <div class="col-md-4">
                                    {{ item.product }}
                                </div>
                                <div class="col-md-2">
                                    {{ item.condition }}
                                </div>
                                <div class="col-md-2">
                                    <Button v-tooltip="'Delete item'" icon="pi pi-trash" style="padding: 0px;"
                                            class="p-button-danger p-button-text p-button-sm mr-2"
                                            @click="deleteBoxBuildBoxItemConfirmation(item.id)"/>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </Dialog>
        <ConfirmDialog></ConfirmDialog>
    </div>
</template>

<script>
import Card from "primevue/card";
import Button from "primevue/button";
import Paginator from "primevue/paginator";
import InputText from "primevue/inputtext";
import Calendar from 'primevue/calendar';
import moment from "moment";
import Dialog from "primevue/dialog";
import ProgressSpinner from 'primevue/progressspinner';
import ConfirmDialog from "primevue/confirmdialog";

export default {
    components: {
        'Card': Card,
        'Button': Button,
        'Paginator': Paginator,
        'InputText': InputText,
        'Calendar': Calendar,
        'Dialog': Dialog,
        'ProgressSpinner': ProgressSpinner,
        'ConfirmDialog': ConfirmDialog,
    },
    name: "DirectScanBoxes",
    data() {
        return {
            boxes: [],
            totalBoxesCount: 0,
            rows: 9,
            first: 0,
            boxSearchString: null,
            date: [],
            datesToSubmit: [],
            itemDialog: false,
            itemsInBox: null,
            viewBoxId: null,
        }
    },
    created() {
        this.getAllBoxes();
    },
    methods: {
        deleteBoxBuildBoxItemConfirmation(id) {
            this.$confirm.require({
                message: 'Are you sure you want to proceed?',
                header: 'Confirmation',
                icon: 'pi pi-exclamation-triangle',
                accept: () => {
                    this.deleteBoxBuildBoxItem(id);
                },
                reject: () => {
                    //
                }
            });
        },
        deleteClosedBoxConfirmation(boxId) {
            this.$confirm.require({
                message: 'This will delete the box, and all items in it. Do you want to proceed?',
                header: 'Confirmation',
                icon: 'pi pi-exclamation-triangle',
                accept: () => {
                    this.deleteClosedBoxBuildBox(boxId);
                },
                reject: () => {
                    //
                }
            });
        },
        deleteClosedBoxBuildBox(boxId) {
            axios.post(route('deleteClosedBoxBuildBox'), {id: boxId})
                .then(response => {
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
        viewBoxItems(id) {
            this.itemDialog = true;
            this.viewBoxId = id;
            this.getBoxItems();
        },
        getBoxItems() {
            axios.post(route('viewBoxItems'), {id: this.viewBoxId})
                .then(response => {
                    this.itemsInBox = response.data;
                })
                .catch(error => {
                    this.$root.errorDisplay(error);
                });
        },
        deleteBoxBuildBoxItem(id) {
            axios.post(route('deleteBoxBuildBoxItem'), {id: id})
                .then(response => {
                    this.getBoxItems();
                    this.getAllBoxes();
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
        printLabel(id) {
            window.open(route('boxBuildLabel', id));
        },
        getAllBoxes() {
            if ((this.date[0] && this.date[1]) || this.date.length === 0) {
                this.formatDates();
                axios.post(route('getAllDirectScanBoxes'), {
                    page: this.page,
                    rows: this.rows,
                    string: this.boxSearchString,
                    date: this.datesToSubmit,
                })
                    .then(response => {
                        this.boxes = response.data.data;
                        this.totalBoxesCount = response.data.total;
                    })
                    .catch(error => {
                        this.$root.errorDisplay(error);
                    });
            }
        },
        boxPageClick(event) {
            this.page = event.page + 1;
            this.rows = event.rows;
            this.getAllBoxes();
        },
        formatDates() {
            this.date.forEach((date, index) => {
                this.datesToSubmit[index] = moment(this.date[index]).format('YYYY-MM-DD HH:mm:ss');
            })
        }
    }
}
</script>

<style scoped>
.btn-primary {
    color: #fff;
    background-color: rgb(219, 113, 98);
    border-color: #cb412f;
    border-radius: 0px;
}

.btn-primary:active {
    color: #fff;
    background-color: rgb(219, 113, 98);
    border-color: #cb412f;
    border-radius: 0px;
}

.btn-primary:focus {
    color: #fff;
    background-color: rgb(219, 113, 98) !important;
    border-color: #cb412f !important;
    border: 1px solid !important;
    box-shadow: none !important;
    border-radius: 0px !important;
}

.btn-primary:hover {
    color: #cb412f;
    background-color: rgb(250, 233, 231);
    border-color: #cb412f;
    border-radius: 0px;
}

.p-input-icon-left > .p-inputtext {
    padding-left: 3rem;
}
</style>
