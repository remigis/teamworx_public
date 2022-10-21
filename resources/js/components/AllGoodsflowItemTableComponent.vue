<template>
    <div>
        <DataTable v-model:value="gfItems" :filters.sync="filters" :page.sync="page" :first.sync="first"
                   :paginator="true"
                   stateStorage="session" stateKey="allGoodsflowItemsDataTable"
                   :totalRecords="totalRecords" paginatorPosition="top" removableSort sortMode="multiple" :lazy="true"
                   :loading="loading" ref="gfitemDataTable" :rows.sync="rows"
                   paginatorTemplate="CurrentPageReport FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown"
                   :rowsPerPageOptions="[10,20,50,totalRecords]" @filter="onFilter($event)" @page="onPage($event)"
                   @sort="onSort($event)"
                   currentPageReportTemplate="Showing {first} to {last} of {totalRecords}" responsiveLayout="scroll"
                   filterDisplay="menu"
                   :showGridlines="true"
                   :expandedRows.sync="expandedRows"
                   :stripedRows="true">

            <template #header>
                <div style="text-align: left">
                    <SplitButton label="Copy" icon="pi pi-plus" :model="copyItems"></SplitButton>
                </div>
            </template>
            <Column :expander="true" :headerStyle="{'width': '3rem'}"/>
            <Column field="artikel_name" filterField="artikel_name" header="Artikel Name"
                    :sortable="true" :showFilterMatchModes="true" :maxConstraints="10" showClearButton>
                <template #filter="{filterModel,filterCallback}">
                    <InputText type="text" v-model="filterModel.value" @submit="filterCallback()" :filter="true"
                               class="p-column-filter" placeholder="Search by Artikel Name"/>
                </template>
            </Column>
            <Column field="karton_id" filterField="karton_id" header="Karton ID"
                    :sortable="true" :maxConstraints="10" showClearButton>
                <template #filter="{filterModel,filterCallback}">
                    <InputText type="text" v-model="filterModel.value" @submit="filterCallback()" :filter="true"
                               class="p-column-filter" placeholder="Search by Karton ID"/>
                </template>
            </Column>
            <Column field="karton_name" filterField="karton_name" header="Karton Name"
                    :sortable="true" :maxConstraints="10" showClearButton>
                <template #filter="{filterModel,filterCallback}">
                    <InputText type="text" v-model="filterModel.value" @submit="filterCallback()" :filter="true"
                               class="p-column-filter" placeholder="Search by Karton Name"/>
                </template>
            </Column>
            <Column field="artikelnummer" filterField="artikelnummer" header="Artikel Nummer"
                    :sortable="true" :maxConstraints="10" showClearButton>
                <template #filter="{filterModel,filterCallback}">
                    <InputText type="text" v-model="filterModel.value" @submit="filterCallback()" :filter="true"
                               class="p-column-filter" placeholder="Search by Artikel Nummer"/>
                </template>
            </Column>
            <Column field="seriennummer" filterField="seriennummer" header="Serial Number" :sortable="true"
                    :maxConstraints="10" showClearButton>
                <template #filter="{filterModel,filterCallback}">
                    <InputText type="text" v-model="filterModel.value" @submit="filterCallback()" :filter="true"
                               class="p-column-filter" placeholder="Search by Serial Number"/>
                </template>
            </Column>
            <Column field="zustand" filterField="zustand" header="Condition" :sortable="true" :maxConstraints="10"
                    showClearButton>
                <template #filter="{filterModel,filterCallback}">
                    <InputText type="text" v-model="filterModel.value" @submit="filterCallback()" :filter="true"
                               class="p-column-filter" placeholder="Search by Condition"/>
                </template>
            </Column>
            <Column field="gUID" filterField="gUID" header="Goodsflow" :sortable="true" :maxConstraints="10"
                    showClearButton>
                <template #filter="{filterModel,filterCallback}">
                    <InputText type="text" v-model="filterModel.value" @submit="filterCallback()" :filter="true"
                               class="p-column-filter" placeholder="Search by Goodsflow"/>
                </template>
            </Column>
            <Column field="flow_user_name" filterField="flow_user_name" header="Name" :sortable="true"
                    :maxConstraints="10"
                    showClearButton>
                <template #filter="{filterModel,filterCallback}">
                    <InputText type="text" v-model="filterModel.value" @submit="filterCallback()" :filter="true"
                               class="p-column-filter" placeholder="Search by Name"/>
                </template>
            </Column>


            <template #expansion="slotProps">
                <div class="row">
                    <div class="col-md-4">
                        <div class="orders-subtable">
                            <h5>History</h5>
                            <DataTable :value="slotProps.data.statistiks">
                                <Column field="user_id" header="User Id"></Column>
                                <Column field="user.name" header="Name"></Column>
                                <Column field="zustand" header="Condition"></Column>
                                <Column field="timestamp" header="Time"></Column>
                            </DataTable>

                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="orders-subtable">
                            <h5>Info</h5>
                            <ul>
                                <li>Item sphere: <b>{{ slotProps.data.sphere.name }}</b></li>
                                <li>Commentar: <b>{{ slotProps.data.kommentar }}</b></li>
                            </ul>

                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="orders-subtable">

                        </div>
                    </div>
                </div>
            </template>
            <ScrollTop target="window"/>
        </DataTable>
    </div>
</template>

<script>
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import {FilterMatchMode, FilterOperator} from "primevue/api";
import SplitButton from 'primevue/splitbutton';
import ScrollTop from 'primevue/scrolltop';

export default {

    name: 'AllGoodsflowItems',
    components: {
        'DataTable': DataTable,
        'Column': Column,
        'Button': Button,
        'InputText': InputText,
        'SplitButton': SplitButton,
        'ScrollTop': ScrollTop,
    },
    data() {
        return {
            expandedRows: [],
            copyItems: [
                {
                    label: 'Copy visible table',
                    icon: 'pi pi-copy',
                    command: () => {
                        this.copyVisibleTable();
                    }
                },
                {
                    label: "Copy visible GF IDs",
                    icon: 'pi pi-copy',
                    command: () => {
                        this.copyVisibleGoodsflows();
                    }
                }
            ],
            filters: [],
            multiSortMeta: {},
            loading: false,
            gfItems: [],
            lazyParams: {},
            totalRecords: 0,
            rows: 10,
            page: 1,
            first: 1,
            copyStrings: [],
        }
    },
    created() {
        this.initFilters();
    },
    mounted() {
        this.lazyParams = {
            first: this.first,
            multiSortMeta: this.multiSortMeta,
            rows: this.rows,
            filters: this.filters,
        }
        this.loadKartonArtikels();
    },

    methods: {
        copyVisibleGoodsflows(event) {
            if (navigator.clipboard && window.isSecureContext) {
                return navigator.clipboard.writeText(this.copyStrings['visibleGoodsflows']);
            } else {
                let textArea = document.createElement("textarea");
                textArea.value = this.copyStrings['visibleGoodsflows'];
                textArea.style.position = "fixed";
                textArea.style.left = "-999999px";
                textArea.style.top = "-999999px";
                document.body.appendChild(textArea);
                textArea.focus();
                textArea.select();
                return new Promise((res, rej) => {
                    document.execCommand('copy') ? res() : rej();
                    textArea.remove();
                });
            }
        },
        copyVisibleTable(event) {
            if (navigator.clipboard && window.isSecureContext) {
                return navigator.clipboard.writeText(this.copyStrings['visibleTable']);
            } else {
                let textArea = document.createElement("textarea");
                textArea.value = this.copyStrings['visibleTable'];
                textArea.style.position = "fixed";
                textArea.style.left = "-999999px";
                textArea.style.top = "-999999px";
                document.body.appendChild(textArea);
                textArea.focus();
                textArea.select();
                return new Promise((res, rej) => {
                    document.execCommand('copy') ? res() : rej();
                    textArea.remove();
                });
            }
        },
        loadKartonArtikels() {
            this.loading = true;
            axios.get(route('getFilteredGoodsflowItems', this.lazyParams))
                .then(response => {
                    this.copyStrings = response.data.copyStrings;
                    this.gfItems = response.data.data.data;
                    this.totalRecords = response.data.data.total;
                    this.loading = false;
                })
                .catch(function (error) {
                    this.$root.errorDisplay(error);
                })
        },
        submit() {
            this.loadKartonArtikels();
        },
        onFilter(event) {
            this.filters = event.filters;
            this.lazyParams.filters = event.filters;
            this.$refs.gfitemDataTable.d_filters = event.filters;
            this.loadKartonArtikels();
        },
        onSort(event) {
            this.lazyParams.multiSortMeta = event.multiSortMeta
            this.loadKartonArtikels();
        },
        onPage(event) {
            this.lazyParams.rows = event.rows;
            this.lazyParams.page = event.page + 1;
            this.loadKartonArtikels();
        },
        initFilters() {
            this.filters = {
                'artikel_name': {
                    operator: FilterOperator.AND, constraints: [
                        {value: null, matchMode: FilterMatchMode.CONTAINS}
                    ]
                },
                'karton_id': {
                    operator: FilterOperator.AND, constraints: [
                        {value: null, matchMode: FilterMatchMode.CONTAINS}
                    ]
                },
                'karton_name': {
                    operator: FilterOperator.AND, constraints: [
                        {value: null, matchMode: FilterMatchMode.CONTAINS}
                    ]
                },
                'artikelnummer': {
                    operator: FilterOperator.AND, constraints: [
                        {value: null, matchMode: FilterMatchMode.CONTAINS}
                    ]
                },
                'seriennummer': {
                    operator: FilterOperator.AND, constraints: [
                        {value: null, matchMode: FilterMatchMode.CONTAINS}
                    ]
                },
                'zustand': {
                    operator: FilterOperator.AND, constraints: [
                        {value: null, matchMode: FilterMatchMode.CONTAINS}
                    ]
                },
                'gUID': {
                    operator: FilterOperator.AND, constraints: [
                        {value: null, matchMode: FilterMatchMode.CONTAINS}
                    ]
                },
                'flow_user_name': {
                    operator: FilterOperator.AND, constraints: [
                        {value: null, matchMode: FilterMatchMode.CONTAINS}
                    ]
                },
            }
        }
    }
};
</script>


