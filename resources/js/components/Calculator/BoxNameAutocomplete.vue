<template>
    <div class="p-fluid">
        <AutoComplete v-model="selectedBox" :suggestions="filteredBoxes" @item-select="onSelect($event)"
                      @complete="searchBox($event)" :delay="500">
            <template #item="slotProps">
                <div><b>ID:</b> {{ slotProps.item.id }} <b>BOX:</b> {{ slotProps.item.karton_name }} <b>GFID:</b>
                    {{ slotProps.item.gUID }}
                </div>
            </template>
        </AutoComplete>
    </div>
</template>

<script>

import AutoComplete from 'primevue/autocomplete';

export default {
    name: "BoxNameAutocomplete",
    components: {
        'AutoComplete': AutoComplete,
    },
    data() {
        return {
            selectedBox: null,
            filteredBoxes: [],
        }
    },
    methods: {
        searchBox() {
            axios.post(route('boxSearch', {searchString: this.selectedBox}))
                .then(response => {
                    this.filteredBoxes = response.data;
                })
                .catch(function (error) {
                    this.$root.errorDisplay(error);
                })
        },
        onSelect(event) {
            window.location.href = '/box/' + event.value.id;
            this.selectedBox = null;
        }
    },
}
</script>
