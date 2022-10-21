<template>
    <div class="row row-eq-height">

        <div class="col-md-12 my-3">
            <Card class="p-3 h-100">
                <template #title>
                    <i class="fa-solid fa-magnifying-glass"></i> Find items
                </template>
                <template #content>

                    <div class="row">
                        <div class="col-md-6">
                            <Card class="p-3">
                                <template #content>
                                    <div class="col-md-12">
                                        <AutoComplete :forceSelection="true" v-model="selectedPalletId"
                                                      placeholder="Pallet ID/Name"
                                                      :suggestions="filteredPalletIds"
                                                      @complete="searchForPalletIds($event)" field="label"
                                                      class="col-md-12 p-0"/>
                                    </div>
                                    <div class="col-md-12">
                                        <Button :disabled="!selectedPalletId" @click="boxBuildFindItems()"
                                                class="btn btn-primary my-3 p-3 col-md-12">
                                            <i class="fa-solid fa-magnifying-glass"></i> Search in pallet
                                        </Button>
                                    </div>
                                </template>
                            </Card>
                        </div>
                        <div class="col-md-6">

                            <Card class="p-3">
                                <template #content>
                                    <div class="col-md-12">
                                        <div v-if="needToFindByManufacturerAmounts">
                                            <DataTable :value="Object.values(needToFindByManufacturerAmounts)"
                                                       :expandedRows.sync="expandedRows"
                                                       :removableSort="true" responsiveLayout="scroll"
                                                       style="max-height: 500px; overflow-y: scroll;">
                                                <Column :expander="true" :headerStyle="{'width': '3rem'}"/>
                                                <Column field="name" header="Center" :sortable="true"></Column>
                                                <Column field="total" header="Total" :sortable="true"></Column>

                                                <template #expansion="slotProps">
                                                    <div class="orders-subtable">
                                                        <DataTable :value="slotProps.data.manufacturers">
                                                            <Column field="manufacturer" header="Manufacturer"
                                                                    sortable></Column>
                                                            <Column field="need" header="Need" sortable></Column>
                                                        </DataTable>
                                                    </div>
                                                </template>

                                            </DataTable>
                                        </div>

                                    </div>
                                </template>
                            </Card>

                        </div>
                    </div>

                </template>
            </Card>
        </div>

        <div class="col-md-12 my-3">
            <Card class="p-3 h-100">
                <template #title>
                    <div class="p-2">
                        Boxes: {{ boxes.length }}
                        <span class="p-input-icon-left float-right">
                        <i class="pi pi-search"></i>
                        <InputText v-model="boxSearchString" type="text" @change="getBoxBuildViewerBoxes()"
                                   class="p-inputtext-sm"></InputText>
                    </span>
                    </div>
                </template>
                <template #content>

                    <div class="row box-field-height py-5">
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

                </template>
            </Card>
        </div>

        <div class="col-md-12">
            <Card class="p-3 h-100">
                <template #content>
                    <ProgressSpinner style="display: flex;" v-if="!filtersReady"/>
                    <DataTable v-if="filtersReady" ref="dt" :value="Object.values(requiredItems)" dataKey="id"
                               :paginator="true" :rows="10" :filters="filters"
                               paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                               :rowsPerPageOptions="[5,10,25]"
                               currentPageReportTemplate="Showing {first} to {last} of {totalRecords} products"
                               sortMode="multiple" :removableSort="true"
                               :filters.sync="filters" filterDisplay="row"
                               :scrollable="true" scrollDirection="both">

                        <Column field="product_condition" header="Product" :sortable="true"
                                :styles="{'flex-grow':'1', 'flex-basis':'250px'}" :frozen="true">
                            <template #filter="{filterModel,filterCallback}">
                                <InputText type="text" v-model="filterModel.value" @input="filterCallback()"
                                           class="p-column-filter"
                                           placeholder="Search by Product"/>
                            </template>
                        </Column>
                        <Column field="product_name" header="Product Name" :sortable="true"
                                :styles="{'flex-grow':'1', 'flex-basis':'320px'}" :frozen="true">
                            <template #filter="{filterModel,filterCallback}">
                                <InputText type="text" v-model="filterModel.value" @input="filterCallback()"
                                           class="p-column-filter"
                                           placeholder="Search by Product Name"/>
                            </template>
                        </Column>
                        <Column v-for="(center, index) in fulfilmentCenters" dataType="numeric"
                                :key="center.name+'_'+index"
                                :filterField="center.name" :field="center.name" :header="center.name"
                                :sortable="true"
                                :styles="{'flex-grow':'1', 'flex-basis':'200px'}">
                            <template #filter="{filterModel,filterCallback}">
                                <InputText type="number" v-model="filterModel.value" @input="filterCallback()"
                                           class="p-column-filter"
                                           placeholder="Search by amount"/>
                            </template>
                        </Column>
                        <Column field="vid" header="VID" :sortable="true"
                                :styles="{'flex-grow':'1', 'flex-basis':'250px'}">
                            <template #filter="{filterModel,filterCallback}">
                                <InputText type="text" v-model="filterModel.value" @input="filterCallback()"
                                           class="p-column-filter"
                                           placeholder="Search by VID"/>
                            </template>
                        </Column>
                        <Column field="manufacturer" header="Manufacturer" :sortable="true"
                                :styles="{'flex-grow':'1', 'flex-basis':'250px'}">
                            <template #filter="{filterModel,filterCallback}">
                                <InputText type="text" v-model="filterModel.value" @input="filterCallback()"
                                           class="p-column-filter"
                                           placeholder="Search by Manufacturer"/>
                            </template>
                        </Column>

                        <Column :exportable="false" :styles="{'min-width':'8rem'}">
                            <template #body="slotProps">
                                <Button icon="pi pi-pencil" class="p-button-rounded p-button-success mr-2"
                                        @click="editItem(slotProps.data)"/>
                                <Button icon="pi pi-trash" class="p-button-rounded p-button-danger"
                                        @click="deleteItemConfirmation(slotProps.data)"/>
                            </template>
                        </Column>
                    </DataTable>
                </template>
            </Card>
        </div>

        <Dialog :visible.sync="editDialog" :style="{width: '650px'}" header="Item Details"
                :modal="true" class="p-fluid">
            <div class="row">
                <div class="col-12">
                    <h4>{{ itemToEdit.product_condition }}</h4>
                </div>
                <div class="col-12">
                    <p>{{ itemToEdit.name }}</p>
                </div>
            </div>

            <div class="formgrid grid">
                <div class="field col-12">
                    <div class="row">
                        <div v-for="center in fulfilmentCenters" :key="center.name" class="col-12">
                            <div class="row">
                                <div class="col-6" style="margin: auto;"><span
                                    style="font-size: 1.2rem; font-weight: bold;">{{ center.name }}</span></div>
                                <div class="col-6">
                                    <InputNumber v-model="itemToEdit[center.name]" :min="0" :format="false"
                                                 mode="decimal" :showButtons="true"
                                                 buttonLayout="horizontal"
                                                 decrementButtonClass="p-button-secondary"
                                                 incrementButtonClass="p-button-secondary"
                                                 incrementButtonIcon="pi pi-plus" decrementButtonIcon="pi pi-minus"
                                                 class="my-1"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <template #footer>
                <Button label="Cancel" icon="pi pi-times" class="p-button-text" @click="hideEdit()"/>
                <Button label="Save" icon="pi pi-check" class="p-button-text" @click="saveEdit()"/>
            </template>
        </Dialog>


        <Dialog :visible.sync="itemsFoundDialog" :style="{width: '70%'}" header="Search results"
                :modal="true" class="p-fluid">
            <div class="container">
                <ProgressSpinner style="display: flex;" v-if="!itemsFound"/>
                <div v-if="itemsFound">
                    <TabView>
                        <TabPanel v-for="(center, centerName) in itemsFound" :key="centerName" :header="centerName">

                            <DataTable :value="Object.values(center)" :expandedRows.sync="expandedRows"
                                       :removableSort="true" responsiveLayout="scroll"
                                       style="max-height: 500px; overflow-y: scroll;">
                                <Column :expander="true" :headerStyle="{'width': '3rem'}"/>
                                <Column field="boxName" header="Box" :sortable="true"></Column>
                                <Column field="total" header="Can collect" :sortable="true"></Column>

                                <template #expansion="slotProps">
                                    <div class="orders-subtable">
                                        <DataTable :value="slotProps.data.items">
                                            <Column field="product" header="Product" sortable></Column>
                                            <Column field="name" header="Name" sortable></Column>
                                            <Column field="need" header="Need" sortable></Column>
                                            <Column field="found" header="Found" sortable></Column>
                                        </DataTable>
                                    </div>
                                </template>

                            </DataTable>

                        </TabPanel>
                    </TabView>
                </div>
            </div>
        </Dialog>

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
import AutoComplete from "primevue/autocomplete";
import Button from "primevue/button";
import Dialog from "primevue/dialog";
import ConfirmDialog from "primevue/confirmdialog";
import Paginator from "primevue/paginator";
import DataTable from "primevue/datatable";
import Column from "primevue/column";
import {FilterMatchMode} from "primevue/api";
import InputNumber from "primevue/inputnumber";
import InputText from "primevue/inputtext";
import TabView from 'primevue/tabview';
import TabPanel from 'primevue/tabpanel';
import ProgressSpinner from 'primevue/progressspinner';


export default {
    components: {
        'Card': Card,
        'AutoComplete': AutoComplete,
        'Button': Button,
        'Dialog': Dialog,
        'ConfirmDialog': ConfirmDialog,
        'Paginator': Paginator,
        'DataTable': DataTable,
        'Column': Column,
        'InputNumber': InputNumber,
        'InputText': InputText,
        'TabView': TabView,
        'TabPanel': TabPanel,
        'ProgressSpinner': ProgressSpinner,
    },
    name: "BoxBuildViewer",
    props: {
        boxBuildId: String,
    },
    data() {
        return {
            boxSearchString: null,
            expandedRows: [],
            needToFindByManufacturerAmounts: null,
            itemsFound: null,
            itemsFoundDialog: false,
            selectedPalletId: null,
            filteredPalletIds: [],
            boxes: {},
            filters: {},
            filtersReady: false,
            filtersForCenters: {},
            fulfilmentCenters: [],
            requiredItems: {},
            editDialog: false,
            itemToEdit: {'centers': {}},
            itemDialog: false,
            itemsInBox: null,
            viewBoxId: null,
        }
    },
    created() {
        Echo.channel('BoxBuildItemChangeEventChannel').listen('BoxBuildItemChangeEvent', (event) => {
            this.updateRequiredItems();
            this.getNeedToFindAmountsByManufacturer();
            this.getBoxBuildViewerBoxes()
        });

        this.getNeedToFindAmountsByManufacturer();
        this.getBoxBuildViewerBoxes();
        this.getBoxBuildRequiredItems();
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
                    this.getNeedToFindAmountsByManufacturer();
                    this.getBoxBuildViewerBoxes();
                    this.getBoxBuildRequiredItems();
                    this.getBoxItems();
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
        searchForPalletIds() {
            axios.post(route('searchForPalletIds'), {string: this.selectedPalletId})
                .then(response => {
                    this.filteredPalletIds = response.data;
                })
                .catch(error => {
                    this.$root.errorDisplay(error);
                });
        },
        boxBuildFindItems() {
            this.itemsFoundDialog = true;
            axios.post(route('boxBuildFindItems'), {palletId: this.selectedPalletId.id, boxBuildId: this.boxBuildId})
                .then(response => {
                    this.itemsFound = response.data;
                })
                .catch(error => {
                    this.$root.errorDisplay(error);
                });
        },
        getNeedToFindAmountsByManufacturer() {
            axios.post(route('getNeedToFindAmountsByManufacturer'), {boxBuildId: this.boxBuildId})
                .then(response => {
                    this.needToFindByManufacturerAmounts = response.data;
                })
                .catch(error => {
                    this.$root.errorDisplay(error);
                });
        },
        printLabel(id) {
            window.open(route('boxBuildLabel', id));
        },
        getBoxBuildViewerBoxes() {
            axios.post(route('getBoxBuildViewerBoxes'), {boxBuildId: this.boxBuildId, string: this.boxSearchString})
                .then(response => {
                    this.boxes = response.data;
                })
                .catch(error => {
                    this.$root.errorDisplay(error);
                });
        },
        deleteItemConfirmation(data) {
            this.$confirm.require({
                message: 'This will delete the item from box-build list. Do you want to proceed?',
                header: 'Confirmation',
                icon: 'pi pi-exclamation-triangle',
                accept: () => {
                    this.deleteItem(data);
                },
                reject: () => {
                    //
                }
            });
        },
        deleteItem(data) {
            axios.post(route('deleteItemFromBoxBuildList'), {boxBuildId: this.boxBuildId, item: data.product_condition})
                .then(response => {
                    this.getBoxBuildRequiredItems()
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
        editItem(item) {
            this.itemToEdit = item;
            this.itemToEdit.boxBuildId = this.boxBuildId;
            this.editDialog = true;
        },
        hideEdit() {
            this.editDialog = false;
            this.itemToEdit = {'centers': {}};
        },
        saveEdit() {
            axios.post(route('editBoxBuildItem'), this.itemToEdit)
                .then(response => {
                    this.editDialog = false;
                    this.itemToEdit = {'centers': {}};
                    this.getBoxBuildRequiredItems()
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
        getBoxBuildRequiredItems() {
            axios.get(route('getBoxBuildRequiredItems', this.boxBuildId))
                .then(response => {
                    this.requiredItems = response.data.list;
                    this.fulfilmentCenters = response.data.fulfilmentCenters;

                    this.fulfilmentCenters.forEach((value) => {
                        this.filtersForCenters[value.name] = {value: null, matchMode: FilterMatchMode.EQUALS};
                    });
                    this.initFilters();
                })
                .catch(error => {
                    this.$root.errorDisplay(error);
                });
        },
        updateRequiredItems() {
            axios.get(route('getBoxBuildRequiredItems', this.boxBuildId))
                .then(response => {
                    this.requiredItems = response.data.list;
                    this.fulfilmentCenters = response.data.fulfilmentCenters;
                })
                .catch(error => {
                    this.$root.errorDisplay(error);
                });
        },
        initFilters() {
            this.filters = Object.assign({}, {
                'product_condition': {value: null, matchMode: FilterMatchMode.CONTAINS},
                'product_name': {value: null, matchMode: FilterMatchMode.CONTAINS},
                'manufacturer': {value: null, matchMode: FilterMatchMode.CONTAINS},
                'vid': {value: null, matchMode: FilterMatchMode.CONTAINS},
            }, this.filtersForCenters);
            this.filtersReady = true;
        }
    }
}

</script>

<style scoped>
::v-deep .p-datatable-scrollable .p-frozen-column {
    font-weight: bold;
}

.box-field-height {
    max-height: 300px;
    overflow-y: scroll;
}

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
</style>
