<template>
    <div class="container">
        <div class="container container bg- hide-on-print py-3">
            <div class="row">
                <SelectButton class="mt-4" v-model="selectedLabelType" dataKey="name" :options="LabelTypes"
                              optionLabel="name">
                    <template #option="slotProps">
                        <div class="car-option">
                            <i :class="slotProps.option.icon"></i> {{ slotProps.option.name }}
                        </div>
                    </template>
                </SelectButton>
                <div class="row col-md-12 mt-4">
                    <div class="col-md-3">
                        <Dropdown v-if="selectedLabelType.name === 'Plain'" v-model="selectedFontSize"
                                  :options="fontSizes"
                                  optionLabel="value" placeholder="Font size"/>
                    </div>
                </div>

                <InputText v-if="selectedLabelType.name === 'Plain'" type="text" v-model="plainEditorValue"
                           placeholder="Text" class="my-4"/>

                <InputText v-if="selectedLabelType.name === 'Warehouse'" type="text" v-model="editorValue"
                           placeholder="Text" class="my-4"/>
                <print-button v-if="selectedLabelType.name === 'Plain'"></print-button>
                <Button :disabled="!editorValue" v-if="selectedLabelType.name === 'Warehouse'"
                        class="btn btn-primary hide-on-print btn-sm" @click="createWarehouseKeyAndDoPrint()"><i
                    class="fa-solid fa-print"></i>
                    Create warehouse key and Print label
                </Button>
            </div>
        </div>
        <div v-if="selectedLabelType.name === 'Warehouse'" class="container bg-dark py-3">

            <div class="label text-center p-3 my-2 bg-white new-page">
                <div class="justify-content-center bg-white">
                    <div class="col-xs-12">
                        <H1 class="h1 font-weight-bolder box-name"> {{ editorValue }} </H1>
                    </div>
                    <div class="col-xs-12">
                        <div class="row">
                            <div class="col-6 count">

                            </div>
                            <div class="col-6 width50 padtop10">
                                <Skeleton v-if="!warehouseKey" width="8rem" height="8rem"
                                          style="margin: 1cm auto auto;"/>
                                <qr-code v-if="warehouseKey" :box-id="warehouseKey"></qr-code>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div v-if="selectedLabelType.name === 'Plain'" class="container bg-dark py-3">

            <div class="label p-3 my-2 bg-white">
                <div class="bg-white">
                    <div class="col-xs-12 text-center font-weight-bolder"
                         :style="{fontSize: selectedFontSize.value}">{{ plainEditorValue }}
                    </div>
                </div>
            </div>

        </div>
    </div>
</template>

<script>
import InputText from 'primevue/inputtext';
import PrintButton from "../PrintButton";
import Editor from 'primevue/editor';
import Skeleton from 'primevue/skeleton';
import SelectButton from 'primevue/selectbutton';
import Dropdown from 'primevue/dropdown';

export default {
    components: {

        'InputText': InputText,
        'PrintButton': PrintButton,
        'Editor': Editor,
        'Skeleton': Skeleton,
        'SelectButton': SelectButton,
        'Dropdown': Dropdown,
    },
    name: "Printer",
    data() {
        return {
            editorValue: null,
            plainEditorValue: null,
            warehouseKey: null,
            selectedLabelType: {name: 'Warehouse', icon: 'fa-solid fa-warehouse'},
            LabelTypes: [
                {name: 'Warehouse', icon: 'fa-solid fa-warehouse'},
                {name: 'Plain', icon: 'fa-solid fa-scroll'}
            ],
            fontSizes: [{value: '10px'},
                {value: '60px'},
                {value: '80px'},
                {value: '100px'},
                {value: '120px'},
                {value: '140px'},
                {value: '160px'},
                {value: '180px'},
                {value: '200px'},
                {value: '220px'},
                {value: '240px'},
                {value: '260px'},
            ],
            selectedFontSize: {value: '220px'},


        }
    },
    methods: {
        createWarehouseKeyAndDoPrint() {
            axios.post(route('createWarehouseKeyAndDoPrint'), {text: this.editorValue})
                .then(response => {
                    this.warehouseKey = response.data;
                    setTimeout(function () {
                        window.print();
                    }, 1000)
                })
                .catch(error => {
                    this.$root.errorDisplay(error);
                });
        },
    }
}
</script>

<style scoped>


</style>
