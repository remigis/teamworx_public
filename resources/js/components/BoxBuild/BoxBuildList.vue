<template>
    <Card class="p-3 h-100">
        <template #title>
            Box-Builds
        </template>
        <template #content>


            <div class="list-group">`
                <div v-for="item in allBoxBuilds" class="list-group-item">
                    <Button @click="goToBoxBuild(item.id)" class="p-button-link p-button-sm py-0">{{
                            item.name
                        }}
                    </Button>
                    <Button v-tooltip="'Deactivate'" @click="deActivateBoxBuild(item.id)" v-if="item.active"
                            class="p-button-text p-button-sm py-1 float-right text-danger"><i
                        class="fa-solid fa-xmark"></i></Button>
                    <Button v-tooltip="'Activate'" @click="activateBoxBuild(item.id)" v-if="!item.active"
                            class="p-button-text p-button-sm py-1 float-right text-success"><i
                        class="fa-solid fa-play"></i></Button>
                </div>
            </div>

            <Paginator :first.sync="first" :rows="rows" :totalRecords="totalBoxBuildsCount"
                       @page="boxBuildPageClick($event)"></Paginator>
        </template>
    </Card>
</template>

<script>
import Card from "primevue/card";
import Button from "primevue/button";
import Paginator from "primevue/paginator";

export default {
    components: {
        'Card': Card,
        'Button': Button,
        'Paginator': Paginator,
    },
    name: "BoxBuildList",
    data() {
        return {
            allBoxBuilds: [],
            totalBoxBuildsCount: 0,
            rows: 5,
            first: 0,
        }
    },
    created() {
        this.getAllBoxBuilds();
    },
    methods: {
        goToBoxBuild(id) {
            window.location.href = route('boxBuildViewer', id);
        },
        getAllBoxBuilds() {
            axios.post(route('getAllBoxBuilds'), {
                page: this.page,
                rows: this.rows
            })
                .then(response => {
                    this.allBoxBuilds = response.data.data;
                    this.totalBoxBuildsCount = response.data.total;
                })
                .catch(error => {
                    this.$root.errorDisplay(error);
                });
        },
        activateBoxBuild(id) {
            axios.post(route('activateBoxBuild'), {
                id: id,
            })
                .then(response => {
                    this.getAllBoxBuilds()
                })
                .catch(error => {
                    this.$root.errorDisplay(error);
                });
        },
        deActivateBoxBuild(id) {
            axios.post(route('deActivateBoxBuild'), {
                id: id,
            })
                .then(response => {
                    this.getAllBoxBuilds()
                })
                .catch(error => {
                    this.$root.errorDisplay(error);
                });
        },
        boxBuildPageClick(event) {
            this.page = event.page + 1;
            this.rows = event.rows;
            this.getAllBoxBuilds();
        },
    }
}
</script>

<style scoped>

</style>
