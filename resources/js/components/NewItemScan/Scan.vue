<template>
    <div class="row">
        <div class="col-md-6 my-2">
            <Panel>
                <template #header>
                    <div class="p-inputgroup">
                        <InputText ref="snInput" :autofocus="true" :disabled="disableInput || !audioFilesAreReady"
                                   type="text"
                                   v-model="scanedSN" @keyup.enter="send()"
                                   class="col-12 p-inputtext-sm"></InputText>
                        <Button v-tooltip="'EAN scan'" icon="" @click="toggleEanScan()"
                                :class="{'p-button-success': eanScan, 'p-button-plain': !eanScan}">
                            <i class="fa-solid fa-barcode"></i>
                        </Button>
                        <Button v-tooltip="'Submit No S/N'" icon="" @click="showMultipleNoSnsModal = true"
                                class="p-button-plain">
                             <span class="fa-layers fa-fw">
                                 <i class="fas fa-ban fa-2x" data-fa-transform="left-3"
                                    style="color: #ec7e7e; z-index: 2;"></i>
                                         <span class="fa-layers-text fa-inverse" data-fa-transform="shrink-3"
                                               style="font-weight:900;">SN</span>
                             </span>
                        </Button>
                    </div>
                </template>
                <div v-if="scan" class="row p-3">
                    <div class="container">
                        <div class="row">
                            <ToggleButton v-if="scan.status === 'new'" id="directScrapCheck" class="col-12 my-1"
                                          v-model="putToDirectScrap"
                                          onLabel="Direct Scrap [ON]" offLabel="Direct Scrap [OFF]" onIcon="pi pi-trash"
                                          offIcon="pi pi-trash"></ToggleButton>

                            <Button v-if="scan.status === 'new'" label="Close pallet" icon="fa-solid fa-circle-xmark"
                                    @click="closePallet()" iconPos="left"
                                    class="col-12 my-1"></Button>
                            <Button v-if="scan.status === 'new'" :disabled="disableInput" label="Close Scan"
                                    icon="fas fa-angle-left fa-rotate-180"
                                    @click="closeScanConfirm()"
                                    iconPos="left"
                                    class="col-12 p-button-danger p-button-outlined my-1"></Button>
                            <div v-if="scan.status === 'done'" class="col-sm-12 p-0">
                                <Button class="p-button-outlined col-sm-12 my-1" label="Back to Scan"
                                        @click="makeScanNew()"
                                        icon="fas fa-angle-left"/>
                                <Button class="p-button-outlined col-sm-12 my-1" label="Confirm"
                                        @click="makeScanConfirmed()"
                                        icon="fas fa-angle-left fa-rotate-180"/>
                            </div>
                            <div v-if="scan.status === 'confirmed'" class="col-sm-12 p-0">
                                <Button class="p-button-outlined col-sm-12 my-1" label="Back to Scan"
                                        @click="makeScanNew()"
                                        icon="fa-solid fa-angles-left"/>
                                <Button class="p-button-outlined col-sm-12 my-1" label="Back to Done"
                                        @click="makeScanDone()"
                                        icon="fas fa-angle-left"/>
                            </div>
                        </div>
                    </div>
                </div>
            </Panel>
        </div>
        <div class="col-md-6 my-2">

            <Panel>
                <template #header>
                    <Button :disabled="fileExists" label="Generate xlsx. file" icon="fa-solid fa-table-cells"
                            @click="generateXlsx()"
                            class="p-button-sm">
                    </Button>
                </template>
                <div class="container">
                    <div class="row p-3">
                        <Button :disabled="!fileExists" label="Download file" icon="fa-solid fa-download"
                                @click="downloadXlsxFile()" class="col-12 my-1">
                        </Button>

                        <Button :disabled="!fileExists" label="Delete file" icon="fa-solid fa-x"
                                @click="deleteXlsxFile()"
                                class="col-12 my-1 p-button-danger">
                        </Button>
                    </div>
                </div>

            </Panel>

        </div>
        <div class="col-md-12 my-2">
            <Panel>
                <template #icons>
                    <Button class="btn-sm" label="" icon="fa-solid fa-gear" @click="displaySettings"/>
                </template>
                <template #header>
                    Process: {{ scannedSNsCount }} / {{ needToScanSNsCount }}
                </template>
                <TabView :activeIndex="activeIndex" style="min-height: 500px">
                    <TabPanel>
                        <template #header>
                            <i class="fa-solid fa-table-columns mx-2"></i>
                            <span>All S/N</span>
                        </template>
                        <div class="container">
                            <DataTable :value="needToScanSNs" :paginator="true" :rows="10" responsiveLayout="scroll"
                                       dataKey="id" :filters.sync="allSnFilters" filterDisplay="row"
                                       @filter="onNeedToScanSnsFilter($event)"
                                       paginatorTemplate="CurrentPageReport FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown"
                                       :rowsPerPageOptions="[10,20,50]">
                                <template #header>
                                    Filtered Items: {{ filteredNeedToScanSns }}
                                </template>
                                <Column field="serial_number" header="S/N">
                                    <template #filter="{filterModel,filterCallback}">
                                        <InputText type="text" v-model="filterModel.value" @input="filterCallback()"
                                                   class="p-column-filter"
                                                   placeholder="Search by S/N"/>
                                    </template>
                                </Column>
                                <Column field="product_checked" header="Product">
                                    <template #filter="{filterModel,filterCallback}">
                                        <InputText type="text" v-model="filterModel.value" @input="filterCallback()"
                                                   class="p-column-filter"
                                                   placeholder="Search by Product"/>
                                    </template>
                                </Column>
                                <Column field="qty" header="Quantity" dataType="numeric">
                                    <template #filter="{filterModel,filterCallback}">
                                        <InputText type="number" v-model="filterModel.value" @input="filterCallback()"
                                                   class="p-column-filter"
                                                   placeholder="Search by Quantity"/>
                                    </template>
                                </Column>
                                <Column field="status" header="Status">
                                    <template #filter="{filterModel,filterCallback}">
                                        <InputText type="text" v-model="filterModel.value" @input="filterCallback()"
                                                   class="p-column-filter"
                                                   placeholder="Search by Status"/>
                                    </template>
                                </Column>
                            </DataTable>
                        </div>
                    </TabPanel>
                    <TabPanel>
                        <template #header>
                            <i class="fa-solid fa-list mx-2"></i>
                            <span>Scanned S/N</span>
                        </template>


                        <div class="container">

                            <Toolbar class="mb-4">
                                <template #start>
                                    <Button label="Delete" icon="pi pi-trash" class="p-button-danger mr-2"
                                            @click="confirmDeleteSelectedItems()"
                                            :disabled="!selectedItems || !selectedItems.length"/>
                                </template>

                            </Toolbar>

                            <DataTable :value="scannedSNs" :paginator="true" :rows="10" responsiveLayout="scroll"
                                       :selection.sync="selectedItems"
                                       dataKey="id" :filters.sync="scannedSnFilters" filterDisplay="row"
                                       @filter="onScannedSnsFilter($event)" :totalRecords="filteredScannedSns"
                                       paginatorTemplate="CurrentPageReport FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown"
                                       :rowsPerPageOptions="[10,20,50]">
                                <template #header>
                                    Filtered Items: {{ filteredScannedSns }}
                                </template>
                                <Column selectionMode="multiple" :styless="{width: '3rem'}"
                                        :exportable="false"></Column>
                                <Column field="user.name" header="User">
                                    <template #filter="{filterModel,filterCallback}">
                                        <InputText type="text" v-model="filterModel.value" @input="filterCallback()"
                                                   class="p-column-filter"
                                                   placeholder="Search by S/N"/>
                                    </template>
                                </Column>
                                <Column field="sn" header="S/N">
                                    <template #filter="{filterModel,filterCallback}">
                                        <InputText type="text" v-model="filterModel.value" @input="filterCallback()"
                                                   class="p-column-filter"
                                                   placeholder="Search by S/N"/>
                                    </template>
                                </Column>
                                <Column field="rz" header="Product">
                                    <template #filter="{filterModel,filterCallback}">
                                        <InputText type="text" v-model="filterModel.value" @input="filterCallback()"
                                                   class="p-column-filter"
                                                   placeholder="Search by Product"/>
                                    </template>
                                </Column>

                                <Column field="result" header="Result" :showClearButton="false" :showFilterMenu="false"
                                        :showFilterMatchModes="false" :filterMenuStyle="{'width':'14rem'}"
                                        :styles="{'min-width':'12rem'}">
                                    <template #body="{data}">
                                        <span :class="'customer-badge'">{{ data.result }}</span>
                                    </template>
                                    <template #filter="{filterModel,filterCallback}">
                                        <Dropdown style="z-index: 100;" v-model="filterModel.value"
                                                  @input="filterCallback()" :options="results" optionLabel="result"
                                                  placeholder="Any" class="p-column-filter" :showClear="true">
                                            <template #value="slotProps">
                                                <span :class="'customer-badge'"
                                                      v-if="slotProps.value">{{ slotProps.value }}</span>
                                                <span v-else>{{ slotProps.placeholder }}</span>
                                            </template>
                                            <template #option="slotProps">
                                                <span :class="'customer-badge'">{{ slotProps.option }}</span>
                                            </template>
                                        </Dropdown>
                                    </template>
                                </Column>

                                <Column :styles="{'min-width':'8rem'}">
                                    <template #body="slotProps">
                                        <Button icon="pi pi-pencil" class="p-button-rounded p-button-success mr-2"
                                                @click="editScannedSnDialog(slotProps.data)"/>
                                        <Button icon="pi pi-trash" class="p-button-rounded p-button-danger"
                                                @click="confirmDeleteScannedProduct(slotProps.data)"/>
                                    </template>
                                </Column>
                            </DataTable>
                        </div>


                    </TabPanel>
                    <TabPanel>
                        <template #header>
                            <i class="fa-solid fa-truck-ramp-box mx-2"></i>
                            <span>Pallets</span>
                        </template>

                        <div class="container">

                            <Button v-if="regularPallets.length > 0"
                                    v-tooltip="'This will rewrite Pallet numbers. Most likely You would want to use this option if you delete one or more pallets and pallet numbers do not match pallet count any more. Example You have: 2022-017_1, 2022-017_2, 2022-017_3 and you delete 2022-017_2. After pressing this button pallet 2022-017_3 will be renamed to 2022-017_2'"
                                    label="Rewrite pallet numbers" icon="fa-solid fa-arrow-down-9-1"
                                    @click="rewritePalletNumbers()" class="p-button-plain mb-3"/>
                            <div v-if="regularPallets.length > 0" class="list-group">
                                <div v-for="pallet in regularPallets" :key="pallet.id"
                                     class="list-group-item list-group-item-action">
                                    <div class="row">
                                        <div class="col-md-2 col-sm-2 col-lg-2">
                                            <i v-if="pallet.closed" class="fa-box fa-solid"></i>
                                            <i v-if="!pallet.closed" class="fa-box-open fa-solid"
                                               style="color:#17cc67;"></i>
                                        </div>
                                        <div class="col-md-8 col-sm-8 col-lg-3 text-center">
                                            <h5 style="font-weight: 900; line-height: 8rem;">{{
                                                    pallet.text
                                                }}_{{ pallet.pallet_number }}</h5>
                                        </div>
                                        <div class="col-md-12 col-sm-12 col-lg-7">
                                            <Button v-if="pallet.closed" label="Open Pallet"
                                                    icon="fa-solid fa-lock-open" @click="openPallet(pallet.id)"
                                                    class="col-sm-12 p-button-plain p-button-sm mb-2"/>
                                            <Button v-if="!pallet.closed" label="Close Pallet" icon="fa-solid fa-lock"
                                                    @click="closePallet()"
                                                    class="col-sm-12 p-button-danger p-button-sm mb-2"/>
                                            <Button label="Delete Pallet" icon="fa-solid fa-trash"
                                                    @click="confirmDeletePallet(pallet.id)"
                                                    class="col-sm-12 p-button-danger p-button-sm mb-2"/>
                                            <Button label="Print Label" icon="fa-solid fa-print"
                                                    @click="printLabel(pallet.text, pallet.pallet_number)"
                                                    class="col-sm-12 p-button-plain p-button-sm mb-2"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </TabPanel>

                    <TabPanel>
                        <template #header>
                            <i class="fa-solid fa-list-ol mx-2"></i>
                            <span>Difference</span>
                        </template>


                        <DataTable :value="Object.values(differences)" :paginator="true" :rows="10"
                                   responsiveLayout="scroll"
                                   :removableSort="true"
                                   dataKey="id" :filters.sync="differencesFilters" filterDisplay="row"
                                   paginatorTemplate="CurrentPageReport FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown"
                                   :rowsPerPageOptions="[10,20,50]">
                            <Column field="name" header="Name">
                                <template #filter="{filterModel,filterCallback}">
                                    <InputText type="text" v-model="filterModel.value" @input="filterCallback()"
                                               class="p-column-filter"
                                               placeholder="Search by Product"/>
                                </template>
                            </Column>
                            <Column field="rz" header="Product">
                                <template #filter="{filterModel,filterCallback}">
                                    <InputText type="text" v-model="filterModel.value" @input="filterCallback()"
                                               class="p-column-filter"
                                               placeholder="Search by Product"/>
                                </template>
                            </Column>
                            <Column field="required" header="Required" dataType="numeric" :sortable="true">
                                <template #filter="{filterModel,filterCallback}">
                                    <InputText type="number" v-model="filterModel.value" @input="filterCallback()"
                                               class="p-column-filter"
                                               placeholder="Search by Quantity"/>
                                </template>
                            </Column>
                            <Column field="scanned" header="Scanned" dataType="numeric" :sortable="true">
                                <template #filter="{filterModel,filterCallback}">
                                    <InputText type="number" v-model="filterModel.value" @input="filterCallback()"
                                               class="p-column-filter"
                                               placeholder="Search by Status"/>
                                </template>
                            </Column>
                            <Column field="difference" header="Difference" dataType="numeric" :sortable="true">
                                <template #filter="{filterModel,filterCallback}">
                                    <InputText type="number" v-model="filterModel.value" @input="filterCallback()"
                                               class="p-column-filter"
                                               placeholder="Search by Status"/>
                                </template>
                                <template #body="slotProps">
                                    <span v-if="slotProps.data.difference < 0"
                                          style="color: red; font-weight: 900;">{{ slotProps.data.difference }}</span>
                                    <span v-if="slotProps.data.difference === 0"
                                          style="color: green; font-weight: 900;">{{ slotProps.data.difference }}</span>
                                    <span v-if="slotProps.data.difference > 0"
                                          style="color: blue; font-weight: 900;">{{ slotProps.data.difference }}</span>

                                </template>
                            </Column>
                        </DataTable>


                    </TabPanel>
                </TabView>

            </Panel>
        </div>

        <Dialog :modal="true" header="Select product number" :visible.sync="askForRz" @hide="closeAskoForRz"
                :containerStyle="{width: '50vw'}">
            <Card class="p-5 my-2">
                <template #title>
                    Serial number: {{ scanedSN }}
                </template>
                <template #content>
                    <div class="row">
                        <div class="col-md-12">Product ID:</div>
                        <AutoComplete class="col-md-12" v-model="selectedProductId" :suggestions="filteredProductIds"
                                      @complete="searchProductId($event)" @item-select="setRzIfAsked()"
                                      @keyup.enter="send()" field="label"/>
                    </div>

                </template>
            </Card>
            <template #footer>
                <Button label="Close" icon="pi pi-times" @click="closeAskoForRz()" class="p-button-text"/>
                <Button label="Submit" icon="pi pi-plus" @click="send()" class="p-button-plain"/>
            </template>
        </Dialog>


        <Dialog :modal="true" header="Edit Serial Number" @hide="canselSnEdit()" :visible.sync="showScannedSnEditDialog"
                :containerStyle="{width: '50vw'}">
            <Card class="p-5 my-2">
                <template #content>
                    <div class="row">
                        <InputText v-if="scannedSnToEdit" v-model="scannedSnToEdit.sn"></InputText>
                    </div>

                </template>
            </Card>
            <template #footer>
                <Button label="Cansel" icon="pi pi-times" @click="canselSnEdit()" class="p-button-text"/>
                <Button label="Edit" icon="pi pi-plus" @click="scannedSnEdit()" class="p-button-plain"/>
            </template>
        </Dialog>


        <Dialog :modal="true" header="Select product number" :visible.sync="showMultipleNoSnsModal"
                @hide="closeMultipleNoSnsModal()"
                :containerStyle="{width: '50vw'}">
            <Card class="p-5 my-2">
                <template #title>
                    Serial number: {{ noSnSn }}
                </template>
                <template #content>
                    <div class="row">
                        <div class="col-md-12">Product ID:</div>
                        <AutoComplete class="col-md-12 mb-4" v-model="multipleNoSnsProduct"
                                      :suggestions="filteredNoSnProducts"
                                      @complete="searchProductIdForMultipleNoSns($event)"
                                      @item-select="setMultipleNoSnsProductId()"
                                      field="label"/>

                        <div class="col-md-12">Count:</div>
                        <InputNumber class="col-md-12" :useGrouping="false" :min="1" v-model="multipleNoSnsCount"
                                     mode="decimal" showButtons
                                     buttonLayout="horizontal"
                                     decrementButtonClass="p-button-secondary"
                                     incrementButtonClass="p-button-secondary"
                                     incrementButtonIcon="pi pi-plus" decrementButtonIcon="pi pi-minus"/>
                    </div>

                </template>
            </Card>
            <template #footer>
                <Button label="Close" icon="pi pi-times" @click="closeMultipleNoSnsModal()" class="p-button-text"/>
                <Button label="Submit" icon="pi pi-plus" @click="sendMultipleNoSns()" class="p-button-plain"/>
            </template>
        </Dialog>

        <Dialog :modal="true" header="Response" :visible.sync="showMultipleNoSnsResponseModal"
                @hide="closeMultipleNoSnsResponseModal()"
                :containerStyle="{width: '50vw'}">
            <Card class="p-5 my-2">
                <template #content>
                    <div class="list-group">
                        <div v-if="multipleNoSnsResponse.directScrap" class="list-group-item list-group-item-action">
                            <div class="row">
                                <div class="col-md-5">To scrap:</div>
                                <div class="col-md-5">{{ multipleNoSnsResponse.count }}</div>
                                <div class="col-md-2"><i v-if="multipleNoSnsResponse.battery"
                                                         class="fas fa-car-battery text-danger"></i></div>
                            </div>
                        </div>

                        <div v-if="multipleNoSnsResponse.toGood" class="list-group-item list-group-item-action">
                            <div class="row">
                                <div class="col-md-6">To regular pallet:</div>
                                <div class="col-md-6">{{ multipleNoSnsResponse.toGoodCount }}</div>
                            </div>
                        </div>

                        <div v-for="item in multipleNoSnsResponse.toRequiredList"
                             class="list-group-item list-group-item-action">
                            <div class="row">
                                <div class="col-md-6">To {{ item.name }} :</div>
                                <div class="col-md-6">{{ item.count }}</div>
                            </div>
                        </div>
                    </div>
                </template>
            </Card>
            <template #footer>
                <Button label="Close" icon="pi pi-times" @click="closeMultipleNoSnsResponseModal()"
                        class="p-button-text"/>
            </template>
        </Dialog>

        <Dialog header="EAN Scan" :visible.sync="eanScan" :containerStyle="{width: '20vw'}" :closable="false"
                :position="'topleft'">

            <div class="list-group">
                <div class="list-group-item list-group-item">
                    S/N: {{ eanScanSn }}
                    <Button v-if="eanScanSn" v-tooltip="'Delete SN'" label="" icon="pi pi-trash"
                            class="p-button-sm p-button-link text-danger ml-2 p-0"
                            @click="eanScanSn=null; eanScanEan=null; scanedSN=null; $refs.snInput.$el.focus()"></Button>
                </div>
                <div class="list-group-item list-group-item">
                    EAN: {{ eanScanEan }}
                    <Button v-if="eanScanEan" v-tooltip="'Delete EAN'" label="" icon="pi pi-trash"
                            class="p-button-sm p-button-link text-danger ml-2 p-0"
                            @click="eanScanEan=null; scanedSN=null; $refs.snInput.$el.focus();"></Button>
                </div>
            </div>

            <template #footer>
                <Button label="Stop EAN Scan" icon="pi pi-times" @click="toggleEanScan()"
                        class="p-button-text"/>
            </template>
        </Dialog>
        <Dialog :modal="true" header="Scan Settings" :visible.sync="showSettings"
                :containerStyle="{width: '50vw'}">
            <Card class="p-5 my-2">
                <template #title>
                    Use RAZER API
                </template>
                <template #content>
                    With this option enabled all scanned serial numbers will be sent to RAZER API to get
                    product number
                </template>
                <template #footer>
                    <ToggleButton class="col-md-6 my-1 settingsButtons" v-model="useRazerAPI"
                                  onLabel="RAZER API is [ON]" offLabel="RAZER API is [OFF]"
                                  onIcon="fa fa-satellite-dish" offIcon="fa fa-satellite-dish"
                                  @click="toggleRazerAPIStatus"/>
                </template>
            </Card>

            <Card class="p-5 my-2">
                <template #title>
                    Check required list
                </template>
                <template #content>
                    Items that match required list will be placed in the corresponding box.
                </template>
                <template #footer>
                    <ToggleButton class="col-md-6 my-1 settingsButtons" v-model="checkRequired"
                                  onLabel="Checking is [ON]" offLabel="Checking is [OFF]"
                                  onIcon="fa fa-stream" offIcon="fa fa-stream"
                                  @click="toggleRequiredListUsageStatus"/>
                </template>
            </Card>

            <template #footer>
                <Button label="Close" icon="pi pi-times" @click="closeSettings" class="p-button-text"/>
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
import {FilterMatchMode} from 'primevue/api';
import Dropdown from 'primevue/dropdown';
import Toolbar from 'primevue/toolbar';


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
        'Dropdown': Dropdown,
        'Toolbar': Toolbar,
    },
    name: "Scan",
    props: {
        userId: String,
        scanId: String,
    },
    data() {
        return {
            fileExists: false,
            scan: null,
            differences: [],
            selectedItems: null,
            regularPallets: [],
            results: [],
            showScannedSnEditDialog: false,
            scannedSnToEdit: null,
            eanScanSn: null,
            eanScanEan: null,
            eanScan: false,
            showMultipleNoSnsResponseModal: false,
            multipleNoSnsResponse: null,
            noSnSn: 'No S/N',
            multipleNoSnsProduct: null,
            showMultipleNoSnsModal: false,
            filteredNoSnProducts: [],
            filteredScannedSns: null,
            filteredNeedToScanSns: null,
            multipleNoSnsCount: 1,
            multipleNoSnsProductId: null,
            allSnFilters: {},
            differencesFilters: {},
            scannedSnFilters: {},
            audioFilesAreReady: false,
            selectedProductId: null,
            filteredProductIds: [],
            rzIfAsked: null,
            showSettings: false,
            askForRz: false,
            scanedSN: null,
            putToDirectScrap: false,
            checkRequired: null,
            useRazerAPI: null,
            inputValue: null,
            disableInput: false,
            activeIndex: 0,
            needToScanSNs: [],
            needToScanSNsCount: 0,
            scannedSNs: [],
            scannedSNsCount: 0,
            errorAudio: new Audio('/storage/sounds/siren.MP3'),
            scrapBatteryAudio: new Audio('/storage/sounds/audio/scanBatteryScrapText-' + this.userId + '.MP3'),
            scrapAudio: new Audio('/storage/sounds/audio/scanScrapText-' + this.userId + '.MP3'),
            scanErrorAudio: new Audio('/storage/sounds/audio/audioError-' + this.userId + '.MP3'),
            requiredListAudios: [],
            filesThatNeedToBeOverwritten: [],
        }
    },
    created() {
        this.getScanData();
        this.initDifferenceFilters();
        this.initAllSnFilters();
        this.initScannedSnFilters();

        this.getScannedSNs();
        this.getNeedToScanSNs();
        this.getRegularPallets();
        this.getResults();
        this.getDifferences();
        this.checkIfFileExists();

        this.getRazerAPIStatus();
        this.getRequiredListUsageStatus();

        Echo.channel('NewRequiredListCreatedEventChannel').listen('NewRequiredListCreatedEvent', (event) => {
            this.audioFilesAreReady = false;
            this.filesThatNeedToBeOverwritten = event.needToOverwrite;
            this.$toast.add({
                severity: 'info',
                summary: 'Info',
                detail: "Some data changed in required lists",
                life: 6000
            });
            this.getRequiredListUsageStatus();
        });
        Echo.channel('RazerAPIStatusChangeChannel').listen('RazerAPIStatusChange', (event) => {
            this.getRazerAPIStatus();
            this.$toast.add({
                severity: 'info',
                summary: 'Info',
                detail: "RAZER API status changed",
                life: 6000
            });
        });
        Echo.channel('CheckRequiredListStatusChangeChannel').listen('CheckRequiredListStatusChange', (event) => {
            this.audioFilesAreReady = false;
            this.getRequiredListUsageStatus();
            this.$toast.add({
                severity: 'info',
                summary: 'Info',
                detail: "Required list status changed",
                life: 6000
            });
        });
    },
    mounted() {
    },
    methods: {
        printLabel(text, number) {
            axios.post(route('createWarehouseLabelForPalletInScan', {text: text, number: number}))
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
        downloadXlsxFile() {
            window.open(route('downloadXlsxFile', this.scanId));
        },
        deleteXlsxFile() {
            axios.post(route('deleteXlsxFile', {'scanId': this.scanId}))
                .then(response => {
                    this.checkIfFileExists();
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
        checkIfFileExists() {
            axios.post(route('checkIfFileExists', {'scanId': this.scanId}))
                .then(response => {
                    this.fileExists = response.data;
                })
                .catch(error => {
                    this.$root.errorDisplay(error);
                });
        },
        generateXlsx() {
            axios.post(route('generateXlsx', {'scanId': this.scanId}))
                .then(response => {
                    this.checkIfFileExists();
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
        getScanData() {
            axios.get(route('getScanData', {'scanId': this.scanId}))
                .then(response => {
                    this.scan = response.data;
                })
                .catch(error => {
                    this.$root.errorDisplay(error);
                });
        },
        getDifferences() {
            axios.get(route('getDifferences', {'id': this.scanId}))
                .then(response => {
                    this.differences = response.data;
                })
                .catch(error => {
                    this.$root.errorDisplay(error);
                });
        },
        getRegularPallets() {
            axios.get(route('getRegularPallets', {'id': this.scanId}))
                .then(response => {
                    this.regularPallets = response.data;
                })
                .catch(error => {
                    this.$root.errorDisplay(error);
                });
        },
        scannedSnEdit() {
            this.showScannedSnEditDialog = false;
            axios.post(route('scannedSnEdit'), {
                sn: this.scannedSnToEdit.sn,
                id: this.scannedSnToEdit.id,
                scanId: this.scanId,
            }).then(response => {
                this.scannedSnToEdit = null;
                this.getScannedSNs();
                this.$toast.add({
                    severity: 'success',
                    summary: 'Success',
                    detail: response.data.message,
                    life: 6000
                });
            })
                .catch(error => {
                    this.scannedSnToEdit = null;
                    this.getScannedSNs();
                    this.$root.errorDisplay(error);
                });
        },
        confirmDeleteScannedProduct(data) {
            this.$confirm.require({
                message: 'Are you sure you want to delete this item? It will be deleted from ' + data.result + ' too',
                header: 'Confirmation',
                icon: 'pi pi-exclamation-triangle',
                accept: () => {
                    this.deleteScannedProduct(data);
                },
                reject: () => {
                    //callback to execute when user rejects the action
                }
            });
        },
        confirmDeleteSelectedItems() {
            this.$confirm.require({
                message: 'Are you sure you want to delete all selected items?',
                header: 'Confirmation',
                icon: 'pi pi-exclamation-triangle',
                accept: () => {
                    this.deleteScannedItems();
                },
                reject: () => {
                    //callback to execute when user rejects the action
                }
            });
        },
        deleteScannedItems() {
            axios.post(route('deleteScannedItems'), {
                items: this.selectedItems,
            }).then(response => {
                this.selectedItems = null;
                this.getScannedSNs();
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
        deleteScannedProduct(data) {
            axios.post(route('deleteScannedProduct'), {
                id: data.id,
                rz: data.rz,
            }).then(response => {
                this.getScannedSNs();
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
        canselSnEdit() {
            this.showScannedSnEditDialog = false;
            this.scannedSnToEdit = null;
        },
        editScannedSnDialog(data) {
            this.showScannedSnEditDialog = true;
            this.scannedSnToEdit = data;
        },
        toggleEanScan() {
            this.eanScan = !this.eanScan;
            this.$refs.snInput.$el.focus();
            if (!this.eanScan) {
                this.eanScanSn = null;
                this.eanScanEan = null;
                this.$refs.snInput.$el.blur();
            }
        },
        closeMultipleNoSnsResponseModal() {
            this.showMultipleNoSnsResponseModal = false;
            this.multipleNoSnsResponse = null;
        },
        onScannedSnsFilter(event) {
            this.filteredScannedSns = event.filteredValue.length;
        },
        onNeedToScanSnsFilter(event) {
            this.filteredNeedToScanSns = event.filteredValue.length;
        },
        setMultipleNoSnsProductId() {
            this.multipleNoSnsProductId = this.multipleNoSnsProduct.rz;
        },
        closeMultipleNoSnsModal() {
            this.showMultipleNoSnsModal = false;
            this.multipleNoSnsProductId = null;
            this.multipleNoSnsProduct = null;
            this.multipleNoSnsCount = 1;
        },
        setRzIfAsked() {
            this.rzIfAsked = this.selectedProductId.rz;
        },
        initAllSnFilters() {
            this.allSnFilters = {
                'serial_number': {value: null, matchMode: FilterMatchMode.CONTAINS},
                'product_checked': {value: null, matchMode: FilterMatchMode.CONTAINS},
                'qty': {value: null, matchMode: FilterMatchMode.EQUALS},
                'status': {value: null, matchMode: FilterMatchMode.CONTAINS},
            }
        },
        initDifferenceFilters() {
            this.differencesFilters = {
                'name': {value: null, matchMode: FilterMatchMode.CONTAINS},
                'rz': {value: null, matchMode: FilterMatchMode.CONTAINS},
                'required': {value: null, matchMode: FilterMatchMode.EQUALS},
                'scanned': {value: null, matchMode: FilterMatchMode.EQUALS},
                'difference': {value: null, matchMode: FilterMatchMode.EQUALS},
            }
        },
        initScannedSnFilters() {
            this.scannedSnFilters = {
                'sn': {value: null, matchMode: FilterMatchMode.CONTAINS},
                'rz': {value: null, matchMode: FilterMatchMode.CONTAINS},
                'user.name': {value: null, matchMode: FilterMatchMode.CONTAINS},
                'result': {value: null, matchMode: FilterMatchMode.EQUALS},
            }
        },
        getRequiredListAudioFilePaths() {
            axios.get(route('getActiveRequiredListIds'))
                .then(response => {
                    response.data.forEach((value, key) => {
                        this.requiredListAudios[value.id] = new Audio('/storage/sounds/audio/requiredList' + value.id + '-' + this.userId + '.MP3');
                    });
                })
                .catch(error => {
                    this.$root.errorDisplay(error);
                });
        },
        createRequiredListAudioFiles() {
            axios.post(route('createRequiredListAudioFiles'), {needToOverwrite: this.filesThatNeedToBeOverwritten})
                .then(response => {
                    this.audioFilesAreReady = true;
                    this.getRequiredListAudioFilePaths();
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
        send() {
            if (this.eanScan) {
                if (!this.eanScanSn) {
                    this.eanScanSn = this.scanedSN;
                    this.scanedSN = null;
                } else {
                    this.eanScanEan = this.scanedSN;
                    this.sendEanScan();
                }
            } else {
                this.askForRz = false;
                axios.post(route('SNSubmit', {
                    'sn': this.scanedSN,
                    'putToDirectScrap': this.putToDirectScrap,
                    'scanId': this.scanId,
                    'rz': this.rzIfAsked,
                }))
                    .then(response => {
                        this.rzIfAsked = null;
                        this.getScannedSNs();
                        this.getNeedToScanSNs();
                        this.getRegularPallets();
                        this.getResults();
                        this.getDifferences();
                        this.scanedSN = null;
                        this.selectedProductId = null;
                        if (response.data.askingForRz) {
                            this.askForRz = true;
                            this.scanedSN = response.data.allInputs.sn;
                            this.errorAudio.play();
                        }
                        if (response.data.directScrap) {
                            if (response.data.battery) {
                                this.scrapBatteryAudio.play();
                            } else {
                                this.scrapAudio.play();
                            }
                        }
                        if (response.data.need) {
                            this.requiredListAudios[response.data.idOfList].play();
                        }
                        if (response.data.message === 'Regular') {
                            this.$toast.add({
                                severity: 'info',
                                summary: 'Info',
                                detail: response.data.message,
                                life: 6000
                            });
                        }

                    })
                    .catch(error => {
                        this.$root.errorDisplay(error);
                        this.scanErrorAudio.play();
                    });
            }
        },
        sendEanScan() {
            axios.post(route('SNEanSubmit', {
                'sn': this.eanScanSn,
                'ean': this.eanScanEan,
                'putToDirectScrap': this.putToDirectScrap,
                'scanId': this.scanId,
            }))
                .then(response => {
                    this.eanScanSn = null;
                    this.eanScanEan = null;
                    this.getScannedSNs();
                    this.getNeedToScanSNs();
                    this.getRegularPallets();
                    this.getResults();
                    this.getDifferences()
                    this.scanedSN = null;
                    this.selectedProductId = null;

                    if (response.data.directScrap) {
                        if (response.data.battery) {
                            this.scrapBatteryAudio.play();
                        } else {
                            this.scrapAudio.play();
                        }
                    }
                    if (response.data.need) {
                        this.requiredListAudios[response.data.idOfList].play();
                    }

                })
                .catch(error => {
                    this.$root.errorDisplay(error);
                    this.scanErrorAudio.play();
                });
        },
        sendMultipleNoSns() {
            this.showMultipleNoSnsModal = false;
            axios.post(route('multipleNoSnSubmit', {
                'putToDirectScrap': this.putToDirectScrap,
                'sn': this.noSnSn,
                'snCount': this.multipleNoSnsCount,
                'scanId': this.scanId,
                'rz': this.multipleNoSnsProductId,
            }))
                .then(response => {
                    this.rzIfAsked = null;
                    this.getScannedSNs();
                    this.getNeedToScanSNs();
                    this.getRegularPallets();
                    this.getResults();
                    this.getDifferences()
                    this.scanedSN = null;
                    this.multipleNoSnsProduct = null;
                    this.multipleNoSnsProductId = null;
                    this.multipleNoSnsCount = 1;

                    this.multipleNoSnsResponse = response.data;

                    this.showMultipleNoSnsResponseModal = true;

                    this.errorAudio.play();

                })
                .catch(error => {
                    this.$root.errorDisplay(error);
                    this.scanErrorAudio.play();
                });

        },
        closePallet() {
            axios.get(route('closePallet', {'id': this.scanId}))
                .then(response => {
                    this.getRegularPallets();
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
        rewritePalletNumbers() {
            axios.get(route('rewritePalletNumbers', {'id': this.scanId}))
                .then(response => {
                    this.getRegularPallets();
                    this.getScannedSNs();
                    this.getResults();
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
        openPallet(palletId) {
            axios.get(route('openPallet', {palletId: palletId}))
                .then(response => {
                    this.getRegularPallets();
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
        deletePallet(palletId) {
            axios.get(route('deletePallet', {palletId: palletId}))
                .then(response => {
                    this.getRegularPallets();
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
        confirmDeletePallet(palletId) {
            this.$confirm.require({
                message: 'Are you sure you want to delete this pallet?',
                header: 'Confirmation',
                icon: 'pi pi-exclamation-triangle',
                accept: () => {
                    this.deletePallet(palletId);
                },
                reject: () => {
                    //callback to execute when user rejects the action
                }
            });
        },
        getNeedToScanSNs() {
            axios.get(route('getNeedToScanSNs', {'id': this.scanId}))
                .then(response => {
                    this.needToScanSNsCount = response.data.length;
                    this.needToScanSNs = response.data;
                })
                .catch(error => {
                    this.$root.errorDisplay(error);
                });
        },
        getScannedSNs() {
            axios.get(route('getScannedSNs', {'id': this.scanId}))
                .then(response => {
                    this.scannedSNsCount = response.data.length;
                    this.scannedSNs = response.data;
                })
                .catch(error => {
                    this.$root.errorDisplay(error);
                });
        },
        getResults() {
            axios.get(route('getResults', {'id': this.scanId}))
                .then(response => {
                    this.results = [];
                    response.data.forEach((value) => {
                        this.results.push(value.result);
                    })

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
                    this.makeScanDone();
                },
                reject: () => {
                    //callback to execute when user rejects the action
                }
            });
        },
        makeScanDone() {
            axios.post(route('makeScanDone', {'scanId': this.scanId}))
                .then(response => {
                    this.getScanData();
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
        makeScanNew() {
            axios.post(route('makeScanNew', {'scanId': this.scanId}))
                .then(response => {
                    this.getScanData();
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
        makeScanConfirmed() {
            axios.post(route('makeScanConfirmed', {'scanId': this.scanId}))
                .then(response => {
                    this.getScanData();
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
        getRazerAPIStatus() {
            axios.get(route('getRazerAPIStatus'))
                .then(response => {
                    this.useRazerAPI = this.convertToTrueFalse(response.data.value);
                })
                .catch(error => {
                    this.$root.errorDisplay(error);
                });
        },
        getRequiredListUsageStatus() {
            axios.get(route('getRequiredListUsageStatus'))
                .then(response => {
                    this.checkRequired = this.convertToTrueFalse(response.data.value);
                    if (this.checkRequired) {
                        this.createRequiredListAudioFiles();
                    } else {
                        this.audioFilesAreReady = true;
                    }
                })
                .catch(error => {
                    this.$root.errorDisplay(error);
                });
        },
        toggleRazerAPIStatus() {
            axios.get(route('toggleRazerAPIStatus'))
                .then(response => {
                    //
                })
                .catch(error => {
                    this.$root.errorDisplay(error);
                });
        },
        toggleRequiredListUsageStatus() {
            axios.get(route('toggleRequiredListUsageStatus'))
                .then(response => {
                    //
                })
                .catch(error => {
                    this.$root.errorDisplay(error);
                });
        },
        displaySettings() {
            this.showSettings = true;
        },
        closeSettings() {
            this.showSettings = false;
        },
        convertToTrueFalse(value) {
            return (value === '1');
        },
        closeAskoForRz() {
            this.rzIfAsked = null;
            this.selectedProductId = null
            this.scanedSN = null;
            this.askForRz = false;
        },
        searchProductId(event) {
            axios.post(route('searchProductIdForScan', {
                'rz': this.selectedProductId
            }))
                .then(response => {
                    this.filteredProductIds = response.data;
                })
                .catch(error => {
                    this.$root.errorDisplay(error);
                });
        },
        searchProductIdForMultipleNoSns(event) {
            axios.post(route('searchProductIdForScan', {
                'rz': this.multipleNoSnsProduct,
            }))
                .then(response => {
                    this.filteredNoSnProducts = response.data;
                })
                .catch(error => {
                    this.$root.errorDisplay(error);
                });
        }
    }
    ,

}
</script>

<style scoped>
.settingsButtons.p-togglebutton.p-button.p-highlight {
    color: #fff;
    background-color: #38c172;
    border-color: #38c172;
}

.settingsButtons.p-togglebutton.p-button.p-highlight:hover {
    background-color: #209d51;
}

#directScrapCheck.p-togglebutton.p-button.p-highlight {
    color: #ffffff;
    background: #d13438;
    border: 1px solid #d13438;
}

#directScrapCheck.p-togglebutton.p-button.p-highlight:hover {
    background: #af1f22;
}
</style>
