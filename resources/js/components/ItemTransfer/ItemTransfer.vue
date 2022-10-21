<template>
    <div>
        <p>GoodsFlows</p>
        <Textarea v-model="textAreaValue" :autoResize="false" rows="5" cols="6" style="max-height: 150px;"
                  class="col-md-12"/>
        <p class="mt-3">Transfer To:</p>
        <AutoComplete :forceSelection="true" v-model="selectedBox" :suggestions="filteredBoxes"
                      @complete="itemTransferSearchForBox($event)" field="label" class="col-md-12 p-0"/>
        <Button label="Transfer" icon="fa-solid fa-right-left" iconPos="right" @click="transferItems()" class="my-3"/>
    </div>
</template>

<script>
import Textarea from 'primevue/textarea';
import AutoComplete from "primevue/autocomplete";
import Button from 'primevue/button';

export default {
    components: {
        'Textarea': Textarea,
        'AutoComplete': AutoComplete,
        'Button': Button,
    },
    name: "ItemTransfer",
    props: {
        text: String,
    },
    data() {
        return {
            textAreaValue: null,
            selectedBox: null,
            filteredBoxes: [],
        }
    },
    methods: {
        itemTransferSearchForBox() {
            axios.post(route('itemTransferSearchForBox'), {string: this.selectedBox})
                .then(response => {
                    this.filteredBoxes = response.data;
                })
                .catch(error => {
                    this.$root.errorDisplay(error);
                });
        },
        transferItems() {
            axios.post(route('transferItems'), {items: this.textAreaValue, box: this.selectedBox.id})
                .then(response => {
                    this.textAreaValue = null;
                    this.selectedBox = null;
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
        }
    },
}
</script>

<style scoped>

</style>
