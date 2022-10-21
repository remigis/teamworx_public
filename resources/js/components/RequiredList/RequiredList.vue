<template>
    <div class="col-12 .xlsx">
        <ConfirmDialog></ConfirmDialog>
        <div class="row row-eq-height">


            <div class="col-md-6 p-3">
                <Button label="Create empty Required List" icon="pi pi-plus" class="col-md-12 p-button-plain"
                        @click="showCreateNewEmptyList = true"/>
            </div>

            <div class="col-md-6 p-3">
                <Button v-if="newItemScanLockStatus" label="Unock new item scan" icon="fa-solid fa-unlock"
                        class="col-md-12 p-button-danger" @click="unlockNewItemScan()"></Button>
                <Button v-if="!newItemScanLockStatus" label="Lock new item scan" icon="fa-solid fa-lock"
                        class="col-md-12 p-button-danger p-button-outlined" @click="lockNewItemScan()"></Button>
            </div>


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
                        <div class="mb-3">
                            <InputText class="col-12" id="rz" placeholder="Name"
                                       type="text" v-model="requiredListName" @keyup="requiredListNameChange()"/>
                        </div>
                        <div class="mb-3">
                            <InputText class="col-12" id="rz" placeholder="Audio text"
                                       type="text" v-model="requiredListAudioText"
                                       @keyup="requiredListAudioTextChange()"/>
                        </div>
                        <Button label="Upload list" class="col-12 my-3" icon="pi pi-plus" iconPos="left"
                                :disabled="!fileRecordsForUpload.length || !requiredListAudioTextIsSet || !requiredListNameIsSet"
                                @click="uploadFiles()">
                        </Button>

                    </template>
                </Card>

            </div>
            <div class="col-md-6 mb-4">
                <Card class="p-3 h-100">
                    <template #title>
                        <div class="row">
                            <div class="col-md-10" v-if="showActiveLists">
                                Active required lists
                            </div>
                            <div class="col-md-10" v-if="!showActiveLists">
                                Passive required lists
                            </div>

                            <Button v-tooltip="'Show activated lists'" v-if="!showActiveLists" label=""
                                    icon="fa-solid fa-person-running" class="col-md-2 p-button-link"
                                    @click="showActivatedLists()"/>
                            <Button v-tooltip="'Show deactivated lists (15)'" v-if="showActiveLists" label=""
                                    icon="fa-solid fa-bed" class="col-md-2 p-button-link"
                                    @click="showDeactivatedLists()"/>
                        </div>
                    </template>

                    <template #content>
                        <div>
                            <BlockUI :blocked="blockContent">
                                <div v-if="!showActiveLists" class="col-12 overflow-y-scroll" style="height: 236px;">

                                    <ProgressSpinner style="display: flex;" v-if="!disabledRequiredLists"/>

                                    <div v-if="disabledRequiredLists">
                                        <vuedraggable v-if="disabledRequiredLists.length > 0" class="py-2"
                                                      type="transition" v-model="disabledRequiredLists">
                                            <transition-group class="list-group">
                                                <div v-for="list in disabledRequiredLists"
                                                     :key="list.id" @click="selectRequiredListToDisplay(list.id)"
                                                     class="list-group-item list-group-item-action"
                                                     :class="{active: isListActive(list.id)}">
                                                    {{ list.name }}
                                                </div>
                                            </transition-group>
                                        </vuedraggable>

                                        <div class="text-center" v-if="disabledRequiredLists.length < 1">
                                            <p>Empty</p>
                                            <img src="storage/images/unDraw/undraw_empty_re_opql.svg" alt="Empty"
                                                 class="img-max-100">
                                        </div>
                                    </div>
                                </div>

                                <div v-if="showActiveLists" class="col-12 overflow-y-scroll" style="height: 236px;">

                                    <ProgressSpinner style="display: flex;" v-if="!requiredLists"/>

                                    <div v-if="requiredLists">
                                        <vuedraggable v-if="requiredLists.length > 0" class="py-2" type="transition"
                                                      v-model="requiredLists">
                                            <transition-group class="list-group">
                                                <div v-for="list in requiredLists"
                                                     :key="list.id" @click="selectRequiredListToDisplay(list.id)"
                                                     class="list-group-item list-group-item-action"
                                                     :class="{active: isListActive(list.id)}">
                                                    {{ list.name }}
                                                </div>
                                            </transition-group>
                                        </vuedraggable>

                                        <div class="text-center" v-if="requiredLists.length < 1">
                                            <p>Empty</p>
                                            <img src="storage/images/unDraw/undraw_empty_re_opql.svg" alt="Empty"
                                                 class="img-max-100">
                                        </div>
                                    </div>


                                </div>
                                <Button v-if="showActiveLists" label="Save priorities" class="col-12 my-3"
                                        icon="pi pi-check"
                                        iconPos="left"
                                        @click="savePriorities()">
                                </Button>
                            </BlockUI>
                        </div>

                    </template>
                </Card>

            </div>
            <div class="col-md-12 mb-4">
                <Card class="p-3 h-100">
                    <template #title>
                        List data
                    </template>
                    <template #content>
                        <div class="text-center" v-if="!selectedList">
                            <p>Click on list to show it</p>
                            <img src="storage/images/unDraw/undraw_selection_re_ycpo.svg" alt="Empty"
                                 class="img-max-100">
                        </div>


                        <div v-if="selectedList" class="">
                            <BlockUI :blocked="blockContent">
                                <Toolbar class="mb-4">
                                    <template #start>
                                        <Button label="New" icon="pi pi-plus" class="p-button-plain mr-2"
                                                @click="openNew"/>
                                        <Button label="Delete" icon="pi pi-trash" class="p-button-danger mr-2"
                                                @click="confirmDeleteSelected()"
                                                :disabled="!selectedProducts || !selectedProducts.length"/>
                                    </template>

                                    <template #end>
                                        <Button v-tooltip="'Deactivate list'" v-if="selectedList.active" label=""
                                                icon="fa-solid fa-toggle-on" class="p-button-link mr-2"
                                                @click="deactivateSelectedList()"/>
                                        <Button v-tooltip="'Activate list'" v-if="!selectedList.active" label=""
                                                icon="fa-solid fa-toggle-off" class="p-button-link mr-2"
                                                @click="activateSelectedList()"/>

                                        <Button v-tooltip="'Settings'" label="" icon="pi pi-cog"
                                                class="p-button-link mr-2"
                                                @click="openSelectedListEdit()"/>
                                        <Button v-tooltip="'Delete list'" label="" icon="pi pi-trash"
                                                class="p-button-link text-danger mr-2"
                                                @click="deleteListConfirmation()"/>
                                    </template>
                                </Toolbar>
                            </BlockUI>

                            <BlockUI :blocked="blockContent">
                                <DataTable ref="dt" :value="selectedList.required_list_items"
                                           :selection.sync="selectedProducts" dataKey="id"
                                           :paginator="true" :rows="10" :filters="filters"
                                           paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                                           :rowsPerPageOptions="[5,10,25]"
                                           currentPageReportTemplate="Showing {first} to {last} of {totalRecords} products"
                                           responsiveLayout="scroll">
                                    <template #header>
                                        <div class="table-header flex">
                                            <h2 class="mb-2 ">{{ selectedList.name }}</h2>
                                            <span class="p-input-icon-left float-right">
                    <i class="pi pi-search"/>
                    <InputText v-model="filters['global'].value" placeholder="Search..." style="padding-left: 2rem;"/>
                </span>
                                        </div>
                                    </template>

                                    <Column selectionMode="multiple" :styless="{width: '3rem'}"
                                            :exportable="false"></Column>
                                    <Column field="name" header="Name" :sortable="true"
                                            :styles="{'min-width':'12rem'}"></Column>
                                    <Column field="rz" header="Product" :sortable="true"
                                            :styles="{'min-width':'12rem'}"></Column>
                                    <Column field="count" header="Amount" :sortable="true"
                                            :styles="{'min-width':'6rem'}"></Column>

                                    <Column :exportable="false" :styles="{'min-width':'8rem'}">
                                        <template #body="slotProps">
                                            <Button icon="pi pi-pencil" class="p-button-rounded p-button-success mr-2"
                                                    @click="editProduct(slotProps.data)"/>
                                            <Button icon="pi pi-trash" class="p-button-rounded p-button-danger"
                                                    @click="confirmDeleteProduct(slotProps.data)"/>
                                        </template>
                                    </Column>
                                </DataTable>
                            </BlockUI>
                        </div>
                    </template>
                </Card>
            </div>

            <div class="col-md-12 mb-4">
                <Card class="p-3 h-100">
                    <template #title>
                        Pallets
                    </template>
                    <template #content>
                        <div class="container">
                            <div v-if="listPallets" class="list-group">
                                <div v-for="pallet in listPallets" :key="pallet.id"
                                     class="list-group-item list-group-item-action">
                                    <div class="row">
                                        <div class="col-md-2 col-sm-2 col-lg-2">
                                            <i v-if="pallet.closed" class="fa-box fa-solid"></i>
                                            <i v-if="!pallet.closed" class="fa-box-open fa-solid"
                                               style="color:#17cc67;"></i>
                                        </div>
                                        <div class="col-md-8 col-sm-8 col-lg-3 text-center" style="padding: 5rem 0;">
                                            <h5 style="font-weight: 900;">{{ pallet.text }}</h5>
                                        </div>
                                        <div class="col-md-12 col-sm-12 col-lg-7">
                                            <Button v-if="pallet.closed" label="Open Pallet"
                                                    icon="fa-solid fa-lock-open" @click="openRequiredPallet(pallet.id)"
                                                    class="col-sm-12 p-button-plain p-button-sm mb-2"/>
                                            <Button v-if="!pallet.closed" label="Close Pallet" icon="fa-solid fa-lock"
                                                    @click="closeRequiredPallet(pallet.id)"
                                                    class="col-sm-12 p-button-danger p-button-sm mb-2"/>
                                            <Button label="Delete Pallet" icon="fa-solid fa-trash"
                                                    @click="confirmDeleteRequiredPallet(pallet.id)"
                                                    class="col-sm-12 p-button-danger p-button-sm mb-2"/>
                                            <Button label="Print Label" icon="fa-solid fa-print"
                                                    @click="printLabel(pallet.text)"
                                                    class="col-sm-12 p-button-plain p-button-sm mb-2"/>
                                            <Button :disabled="!pallet.closed" label="Download Xlsx"
                                                    icon="fa-solid fa-download"
                                                    @click="downloadRequiredPalletXlsx(pallet.id)"
                                                    class="col-sm-12 p-button-plain p-button-sm mb-2"/>
                                        </div>
                                    </div>
                                </div>


                                <Paginator :first.sync="first" :rows="rows" :totalRecords="totalItemsCount"
                                           :rowsPerPageOptions="[5,10,20]" @page="palletPageClick($event)"></Paginator>


                            </div>
                        </div>
                    </template>
                </Card>
            </div>

        </div>

        <Dialog :visible.sync="productDialog" :style="{width: '450px'}" header="Product Details"
                :modal="true" class="p-fluid">
            <div class="row">
                <div class="col-12">
                    <h4>{{ product.rz }}</h4>
                </div>
                <div class="col-12">
                    <p>{{ product.name }}</p>
                </div>
            </div>

            <div class="formgrid grid">
                <div class="field col-12">
                    <label for="vertical" style="display: block">Quantity</label>
                    <InputNumber v-model="product.count" mode="decimal" showButtons
                                 buttonLayout="horizontal"
                                 decrementButtonClass="p-button-secondary"
                                 incrementButtonClass="p-button-secondary"
                                 incrementButtonIcon="pi pi-plus" decrementButtonIcon="pi pi-minus"/>
                </div>
            </div>
            <template #footer>
                <Button label="Cancel" icon="pi pi-times" class="p-button-text" @click="hideDialog()"/>
                <Button label="Save" icon="pi pi-check" class="p-button-text" @click="saveProduct()"/>
            </template>
        </Dialog>

        <Dialog :visible.sync="productAddDialog" @hide="selectedRZ = null; filteredRZs = [];"
                :style="{width: '60%'}" header="Product Details"
                :modal="true" class="p-fluid">
            <div class="formgrid grid">
                <div class="field col-12">
                    <AutoComplete class="col-12 mb-3" placeholder="Product ID (RZ02-01070300-R3M2)"
                                  v-model="selectedRZ" :suggestions="filteredRZs"
                                  @complete="searchRZ()" field="label" @item-select="rZSelected()"/>
                    <div class="col-12">
                        <label style="display: block">Quantity</label>
                        <InputNumber :min="0" :max="9999" :useGrouping="false" v-model="product.count"
                                     mode="decimal" showButtons buttonLayout="horizontal"
                                     decrementButtonClass="p-button-secondary"
                                     incrementButtonClass="p-button-secondary"
                                     incrementButtonIcon="pi pi-plus"
                                     decrementButtonIcon="pi pi-minus"/>
                    </div>
                </div>
            </div>
            <template #footer>
                <Button label="Cancel" icon="pi pi-times" class="p-button-text"
                        @click="hideAddDialog()"/>
                <Button label="Add" icon="pi pi-plus" class="p-button-text" @click="addNewProduct()"/>
            </template>
        </Dialog>

        <Dialog :visible.sync="deleteProductDialog" :styles="{width: '450px'}" header="Confirm"
                :modal="true">
            <div class="confirmation-content row">
                <div class="col-12">
                    <i class="pi pi-exclamation-triangle mr-3" style="font-size: 2rem"/>
                    <span>Are you sure you want to delete <b>{{ product.rz }}</b>?</span>
                </div>
                <div class="col-12 pl-5 mt-5">
                    <p>{{ product.name }}</p>
                </div>
            </div>
            <template #footer>
                <Button label="No" icon="pi pi-times" class="p-button-text"
                        @click="deleteProductDialog = false"/>
                <Button label="Yes" icon="pi pi-check" class="p-button-text" @click="deleteProduct()"/>
            </template>
        </Dialog>

        <Dialog :visible.sync="deleteProductsDialog" :styles="{width: '450px'}" header="Confirm"
                :modal="true">
            <div class="confirmation-content">
                <i class="pi pi-exclamation-triangle mr-3" style="font-size: 2rem"/>
                <span v-if="product">Are you sure you want to delete the selected products?</span>
            </div>
            <template #footer>
                <Button label="No" icon="pi pi-times" class="p-button-text"
                        @click="deleteProductsDialog = false"/>
                <Button label="Yes" icon="pi pi-check" class="p-button-text"
                        @click="deleteSelectedProducts()"/>
            </template>
        </Dialog>

        <Dialog v-if="selectedListToEdit" :visible.sync="showEditSelectedList"
                :styles="{width: '450px'}" header="Edit list" :modal="true">
            <div class="confirmation-content">
                <div class="field col-12 row">
                    <label style="display: block">Name</label>
                    <InputText class="col-12 mb-3" v-model="selectedListToEdit.name"></InputText>
                    <label style="display: block">Audio text</label>
                    <InputText class="col-12 mb-3" v-model="selectedListToEdit.audioText"></InputText>
                </div>
            </div>
            <template #footer>
                <Button label="Cansel" icon="pi pi-times" class="p-button-text"
                        @click="showEditSelectedList = false"/>
                <Button label="Edit" icon="fa-solid fa-pen" class="p-button-text"
                        @click="saveEditedListData()"/>
            </template>
        </Dialog>

        <Dialog :visible.sync="showCreateNewEmptyList"
                :styles="{width: '450px'}" header="Create New List" :modal="true">
            <div class="confirmation-content">
                <div class="field col-12 row">
                    <label style="display: block">Name</label>
                    <InputText class="col-12 mb-3" v-model="NewEmptyListName"></InputText>
                    <label style="display: block">Audio text</label>
                    <InputText class="col-12 mb-3" v-model="NewEmptyListAudioText"></InputText>
                </div>
            </div>
            <template #footer>
                <Button label="Cansel" icon="pi pi-times" class="p-button-text"
                        @click="closeCreateNewEmptyListDialog()"/>
                <Button label="Create" icon="fa-solid fa-plus" class="p-button-text"
                        @click="createNewEmptyList()"/>
            </template>
        </Dialog>

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
import vuedraggable from 'vuedraggable';
import {FilterMatchMode} from 'primevue/api';
import DataTable from 'primevue/datatable';
import Toolbar from 'primevue/toolbar';
import Column from 'primevue/column';
import BlockUI from 'primevue/blockui';
import Paginator from 'primevue/paginator';

export default {
    components: {
        'Card': Card,
        'DataTable': DataTable,
        'Toolbar': Toolbar,
        'Column': Column,
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
        'vuedraggable': vuedraggable,
        'BlockUI': BlockUI,
        'Paginator': Paginator,
    },
    name: "RequiredList",
    data() {
        return {
            totalItemsCount: 0,
            rows: 5,
            first: 0,
            listPallets: null,
            page: 0,


            showCreateNewEmptyList: false,
            NewEmptyListName: null,
            NewEmptyListAudioText: null,
            blockContent: false,
            requiredListName: '',
            requiredListAudioText: '',
            fileRecords: [],
            requiredListAudioTextIsSet: false,
            requiredListNameIsSet: false,
            requiredLists: null,
            disabledRequiredLists: null,
            selectedList: null,
            selectedListToEdit: {},
            showEditSelectedList: false,
            selectedRZ: null,
            filteredRZs: [],
            newItemScanLockStatus: null,
            showActiveLists: true,
            productAddDialog: false,
            products: null,
            productDialog: false,
            deleteProductDialog: false,
            deleteProductsDialog: false,
            product: {},
            selectedProducts: null,
            filters: {},
            productService: null,
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
            addScrap: 'No',
            addBattery: 'No',
            uploadUrl: route('requiredListUpload'),
            uploadHeaders: {'X-Test-Header': 'vue-file-agent', 'X-CSRF-TOKEN': this.$root.csrf_token},
            fileRecordsForUpload: [],
            scanName: null,
            batteryScrapOptions: ['Yes', 'No'],
        };
    },
    created() {
        this.getNewItemScanLockStatus();
        this.getActiveRequiredLists();
        this.getDisabledRequiredLists();


        this.initFilters();

    },
    props: {
        authUserId: null,
    },
    methods: {
        printLabel(text) {
            axios.post(route('createWarehouseLabelForRequiredList', {text: text}))
                .then(response => {
                    this.openWarehouseLabel(response.data.warehouse_label_key)
                })
                .catch(error => {
                    this.$root.errorDisplay(error);
                });
        },
        openWarehouseLabel(warehouse_label_key) {
            window.open(route('warehouseLabel', warehouse_label_key));
        },
        downloadRequiredPalletXlsx(id) {
            window.open('/downloadRequiredPalletXlsx/' + id);
        },
        closeRequiredPallet(id) {
            axios.post(route('closeRequiredPallet', {id: id}))
                .then(response => {
                    this.getSelectedListPallets();
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
        openRequiredPallet(id) {
            axios.post(route('openRequiredPallet', {id: id, listId: this.selectedList.id}))
                .then(response => {
                    this.getSelectedListPallets();
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
        deleteRequiredPallet(id) {
            axios.post(route('deleteRequiredPallet', {id: id}))
                .then(response => {
                    this.getSelectedListPallets();
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
        confirmDeleteRequiredPallet(id) {
            this.$confirm.require({
                message: 'Are you sure you want to delete this pallet?',
                header: 'Confirmation',
                icon: 'pi pi-exclamation-triangle',
                accept: () => {
                    this.deleteRequiredPallet(id);
                },
                reject: () => {
                    //callback to execute when user rejects the action
                }
            });
        },
        getSelectedListPallets() {
            axios.post(route('getSelectedListPallets'), {
                listId: this.selectedList.id,
                page: this.page,
                rows: this.rows
            })
                .then(response => {
                    this.listPallets = response.data.data;
                    this.totalItemsCount = response.data.total;
                })
                .catch(error => {
                    this.$root.errorDisplay(error);
                });
        },
        palletPageClick(event) {
            this.page = event.page + 1;
            this.rows = event.rows;
            this.getSelectedListPallets();
        },
        deleteListConfirmation() {
            this.$confirm.require({
                message: 'This will delete the list. Do you want to proceed?',
                header: 'Confirmation',
                icon: 'pi pi-exclamation-triangle',
                accept: () => {
                    this.deleteList();
                },
                reject: () => {
                    //
                }
            });
        },
        deleteList() {
            axios.post(route('deleteRequiredList'), {listId: this.selectedList.id})
                .then(response => {
                    this.selectedList = null;
                    this.getActiveRequiredLists();
                    this.getDisabledRequiredLists();
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
        closeCreateNewEmptyListDialog() {
            this.showCreateNewEmptyList = false;
            this.NewEmptyListName = null;
            this.NewEmptyListAudioText = null;
        },
        createNewEmptyList() {
            axios.post(route('createNewEmptyList'), {
                name: this.NewEmptyListName,
                audioText: this.NewEmptyListAudioText,
            })
                .then(response => {
                    this.showCreateNewEmptyList = false;
                    this.NewEmptyListName = null;
                    this.NewEmptyListAudioText = null;
                    this.getActiveRequiredLists();
                    this.getDisabledRequiredLists();
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
        isListActive(listId) {
            let selectedListId = false;
            if (this.selectedList) {
                selectedListId = this.selectedList.id
            }

            return listId === selectedListId;
        },
        showDeactivatedLists() {
            this.showActiveLists = false
            this.getDisabledRequiredLists()
        },
        showActivatedLists() {
            this.showActiveLists = true
            this.getActiveRequiredLists()
        },
        getNewItemScanLockStatus() {
            axios.get(route('getNewItemScanLockStatus'))
                .then(response => {
                    this.newItemScanLockStatus = response.data.status;
                })
                .catch(error => {
                    this.$root.errorDisplay(error);
                });
        },
        lockNewItemScan() {
            this.blockContent = true;
            axios.get(route('lockNewItemScan'))
                .then(response => {
                    this.getNewItemScanLockStatus();
                    setTimeout(() => {
                        if (this.selectedList) {
                            this.selectRequiredListToDisplay(this.selectedList.id)
                            this.blockContent = false;
                            this.$toast.add({
                                severity: 'success',
                                summary: 'Success',
                                detail: response.data.message,
                                life: 6000
                            });
                        } else {
                            this.blockContent = false;
                            this.$toast.add({
                                severity: 'success',
                                summary: 'Success',
                                detail: response.data.message,
                                life: 6000
                            });
                        }
                    }, 3000);
                })
                .catch(error => {
                    this.blockContent = false;
                    this.$root.errorDisplay(error);
                });
        },
        unlockNewItemScan() {
            axios.get(route('unlockNewItemScan'))
                .then(response => {
                    this.getNewItemScanLockStatus();
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
        requiredListNameChange() {
            this.requiredListNameIsSet = this.requiredListName !== "";
        },
        requiredListAudioTextChange() {
            this.requiredListAudioTextIsSet = this.requiredListAudioText !== "";
        },
        getActiveRequiredLists() {
            axios.get(route('getActiveRequiredLists'))
                .then(response => {
                    this.requiredLists = response.data;
                })
                .catch(error => {
                    this.$root.errorDisplay(error);
                });
        },
        getDisabledRequiredLists() {
            axios.get(route('getDisabledRequiredLists'))
                .then(response => {
                    this.disabledRequiredLists = response.data;
                })
                .catch(error => {
                    this.$root.errorDisplay(error);
                });
        },
        savePriorities() {
            axios.post(route('savePriorities'), {lists: this.requiredLists})
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
        addNewProduct() {
            axios.post(route('RequiredListAddNewProduct'), {
                rz: this.product.rz,
                count: this.product.count,
                listId: this.selectedList.id
            })
                .then(response => {
                    this.productAddDialog = false;
                    this.selectedRZ = null;
                    this.product = {};
                    this.selectRequiredListToDisplay(this.selectedList.id);
                    this.$toast.add({
                        severity: 'success',
                        summary: 'Success',
                        detail: response.data.message,
                        life: 6000
                    });
                })
                .catch(error => {
                    this.productAddDialog = false;
                    this.selectedRZ = null;
                    this.product = {};
                    this.$root.errorDisplay(error);
                });
        },
        selectRequiredListToDisplay(id) {
            axios.post(route('selectRequiredListToDisplay'), {id: id})
                .then(response => {
                    this.selectedList = response.data;
                    this.selectedListToEdit.name = this.selectedList.name;
                    this.selectedListToEdit.audioText = this.selectedList.audioText;
                    this.getSelectedListPallets();
                })
                .catch(error => {
                    this.$root.errorDisplay(error);
                });
        },
        saveEditedListData() {
            axios.post(route('editRequiredList'), {
                name: this.selectedListToEdit.name,
                audioText: this.selectedListToEdit.audioText,
                listId: this.selectedList.id
            })
                .then(response => {
                    this.showEditSelectedList = false;
                    this.selectRequiredListToDisplay(this.selectedList.id);
                    this.getActiveRequiredLists();
                    this.getDisabledRequiredLists();
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
        deactivateSelectedList() {
            axios.post(route('deactivateRequiredList'), {listId: this.selectedList.id})
                .then(response => {
                    this.getActiveRequiredLists();
                    this.getDisabledRequiredLists();
                    this.getRequiredListActivityStatus();
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
        activateSelectedList() {
            axios.post(route('activateRequiredList'), {listId: this.selectedList.id})
                .then(response => {
                    this.getActiveRequiredLists();
                    this.getDisabledRequiredLists();
                    this.getRequiredListActivityStatus();
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
        getRequiredListActivityStatus() {
            axios.post(route('getRequiredListActivityStatus'), {listId: this.selectedList.id})
                .then(response => {
                    this.selectedList.active = response.data.active;
                })
                .catch(error => {
                    this.$root.errorDisplay(error);
                });
        },


        openSelectedListEdit() {
            this.showEditSelectedList = true;
        },
        openNew() {
            this.product = {};
            this.productAddDialog = true;
        },
        hideDialog() {
            this.product = {};
            this.productDialog = false;
        },
        hideAddDialog() {
            this.product = {};
            this.selectedRZ = null;
            this.productAddDialog = false;
        },
        saveProduct() {
            this.productDialog = false;
            axios.post(route('editRequiredListQuantity', {'product': this.product}))
                .then(response => {
                    this.selectRequiredListToDisplay(this.selectedList.id);
                    this.product = {};
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
        editProduct(product) {
            this.product = {...product};
            this.productDialog = true;
        },
        confirmDeleteProduct(product) {
            this.product = product;
            this.deleteProductDialog = true;
        },
        deleteProduct() {
            this.deleteProductDialog = false;
            axios.post(route('deleteRequiredListProduct', {'product': this.product}))
                .then(response => {
                    this.selectRequiredListToDisplay(this.selectedList.id);
                    this.product = {};
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
        findIndexById(id) {
            let index = -1;
            for (let i = 0; i < this.products.length; i++) {
                if (this.products[i].id === id) {
                    index = i;
                    break;
                }
            }

            return index;
        },

        confirmDeleteSelected() {
            this.deleteProductsDialog = true;
        },
        deleteSelectedProducts() {
            this.deleteProductsDialog = false;
            axios.post(route('deleteRequiredListProducts', {'products': this.selectedProducts}))
                .then(response => {
                    this.selectRequiredListToDisplay(this.selectedList.id);
                    this.selectedProducts = null;
                    this.product = {};
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
        initFilters() {
            this.filters = {
                'global': {value: null, matchMode: FilterMatchMode.CONTAINS},
            }
        },
        searchRZ() {
            axios.post(route('RAZERBatteryGoodBadEditSearch', {
                'rz': this.selectedRZ
            }))
                .then(response => {
                    this.filteredRZs = response.data;
                })
                .catch(error => {
                    this.$root.errorDisplay(error);
                });
        },
        rZSelected() {
            this.product.rz = this.selectedRZ.rz;
        },


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
                'battery': this.addBattery,
                'scrap': this.addScrap
            }))
                .then(response => {
                    this.iconToDisplay = this.neutralIcon;
                    this.addConfirmed = false;
                    this.addScrap = 'No';
                    this.addBattery = 'No';
                    this.addRZ = null;
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
            window.open(route('RAZERBatteryGoodBadDownloade'));
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
                formData.append('name', this.requiredListName);
                formData.append('audioText', this.requiredListAudioText);
                return formData;
            }).then((response) => {
                this.getActiveRequiredLists();
                if (response[0].data.message) {
                    this.$toast.add({
                        severity: 'success',
                        summary: 'Success',
                        detail: response[0].data.message,
                        life: 3000
                    });
                }
                this.requiredListName = '';
                this.requiredListAudioText = '';
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
            let validFileRecords = fileRecordsNewlySelected.filter((fileRecord) => !fileRecord.error);
            this.fileRecordsForUpload = this.fileRecordsForUpload.concat(validFileRecords);
        },
        onBeforeDelete: function (fileRecord) {
            let i = this.fileRecordsForUpload.indexOf(fileRecord);
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


.table-header {
    display: flex;
    align-items: center;
    justify-content: space-between;

@media screen and (max-width: 960px) {
    align-items: start

;
}

}

.confirmation-content {
    display: flex;
    align-items: center;
    justify-content: center;
}

@media screen and (max-width: 960px) {
    ::v-deep .p-toolbar {
        flex-wrap: wrap;

    .p-button {
        margin-bottom: 0.25rem;
    }
}

}

.p-button.p-button-success, .p-buttonset.p-button-success > .p-button, .p-splitbutton.p-button-success > .p-button {
    color: #ffffff;
    background: #28a745;
    border: 1px solid #104b21;
}

.p-button.p-button-success:hover, .p-buttonset.p-button-success > .p-button:hover, .p-splitbutton.p-button-success > .p-button:hover {
    color: #ffffff;
    background: #207e34 !important;
    border: 1px solid #104b21;
}
</style>
