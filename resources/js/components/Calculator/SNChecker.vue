<template>
    <div>
        <Button v-tooltip="'SN look over'" @click="checkerDialog = true" class="p-button-link">
            <i class="fa-solid fa-eye"></i>
        </Button>

        <Dialog position="left" :containerStyle="{width: '20vw'}" :visible.sync="checkerDialog">
            <template #header>
                <h3><i class="fa-solid fa-eye"></i> S/N Look Over</h3>
                <div class="p-dialog-header-icons">

                </div>
            </template>
            <div class="row">
                <div class="col-md-12 mb-4">
                    <InputSwitch v-tooltip="'Auto start'" @click="toggleAutoStart()" v-model="autoStart"
                                 class="float-right my-2"/>
                    <Button @click="checkerSettingsDialog = true" class="p-button-link p-button-lg float-right">
                        <i class="fa-solid fa-cog"></i>
                    </Button>
                </div>
                <div class="row-eq-height col-12">
                    <div class="col-md-4">
                        <div class="col-12 text-center p-0" style="font-weight: bold;">Box</div>
                        <div class="col-12">
                            <Button @click="lookOverResultDialog = true" class="p-button-text"
                                    v-html="boxIcon"></Button>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="col-12 bold text-center p-0" style="font-weight: bold;">DB</div>
                        <div class="col-12">
                            <Button @click="lookOverResultDialog = true" class="p-button-text" v-html="dbIcon"></Button>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="col-12 bold text-center p-0" style="font-weight: bold;">Format</div>
                        <div class="col-12">
                            <Button @click="lookOverResultDialog = true" class="p-button-text"
                                    v-html="formatIcon"></Button>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 mt-4">
                    <Button label="Start Looking" icon="fa-solid fa-glasses" @click="startLooking()" class="p-button"/>
                </div>
            </div>
            <template #footer>
                <Button label="Close" icon="pi pi-times" @click="checkerDialog = false" class="p-button-text"/>
            </template>
        </Dialog>


        <Dialog position="center" :containerStyle="{width: '80vw'}" :visible.sync="lookOverResultDialog">
            <template #header>
                <h3><i class="fa-solid fa-list-dots"></i> S/N Look Over Results</h3>
                <div class="p-dialog-header-icons">

                </div>
            </template>

            <div class="row">
                <div class="col-md-4">
                    <div class="col-12 overflow-y-scroll" style="max-height: 60vh">

                        <div class="col-12">Duplicates in box: {{ snDubBox.length }}</div>

                        <Card v-for="sn in snDubBox" :key="sn.sn" class="p-3">
                            <template #title>
                                {{ sn.sn }}
                            </template>
                            <template #content>
                                <div class="list-group my-4">
                                    <div v-for="item in sn.items"
                                         class="col-12 list-group-item list-group-item-action">
                                        <div class="row">
                                            <div class="col-md-6 p-1">{{ item.gUID }}</div>
                                            <div class="col-md-6 p-1"><span class="text-danger">{{
                                                    item.seriennummer
                                                }}</span></div>
                                            <div class="col-md-6 p-1">{{ item.statistiks[0].user.name }}</div>
                                            <div class="col-md-6 p-1">{{ item.artikel.artikelnummer }}</div>
                                            <div class="col-md-12 p-1">{{ item.artikel.name }}</div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </Card>

                    </div>

                </div>
                <div class="col-md-4">
                    <div class="col-12">

                        <div class="col-12">Duplicates in all database: {{ snDubDb.length }}</div>

                        <div class="list-group my-4 overflow-y-scroll" style="max-height: 60vh">
                            <div v-for="item in snDubDb" class="col-12 list-group-item list-group-item-action">
                                <div class="row">
                                    <div class="col-md-6 p-1">{{ item.gUID }}</div>
                                    <div class="col-md-6 p-1"><span class="text-danger">{{ item.seriennummer }}</span>
                                    </div>
                                    <div class="col-md-6 p-1">{{ item.statistiks[0].user.name }}</div>
                                    <div class="col-md-6 p-1">{{ item.artikel.artikelnummer }}</div>
                                    <div class="col-md-12 p-1">{{ item.artikel.name }}</div>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
                <div class="col-md-4">
                    <div class="col-12">Format errors: {{ badSnFormats.length }}</div>

                    <div class="list-group my-4 overflow-y-scroll" style="max-height: 60vh">
                        <div v-for="item in badSnFormats" class="col-12 list-group-item list-group-item-action">
                            <div class="row">
                                <div class="col-md-12 p-1">GoodsFlow: <span style="font-weight: bold;">{{
                                        item.gUID
                                    }}</span></div>
                                <div class="col-md-12 p-1">Serial number: <span class="text-danger"
                                                                                style="font-weight: bold;">{{
                                        item.seriennummer
                                    }}</span></div>
                                <div class="col-md-12 p-1">User: <span v-if="item.statistiks[0]"
                                                                       style="font-weight: bold;">{{
                                        item.statistiks[0].user.name
                                    }}</span></div>
                                <div class="col-md-12 p-1">Product: <span
                                    style="font-weight: bold;">{{ item.artikel.artikelnummer }}</span></div>
                                <div class="col-md-12 p-1">{{ item.artikel.name }}</div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <template #footer>
                <Button label="Close" icon="pi pi-times" @click="lookOverResultDialog = false" class="p-button-text"/>
            </template>
        </Dialog>

        <Dialog position="center" :modal="true" :containerStyle="{width: '50vw'}" :visible.sync="checkerSettingsDialog">
            <template #header>
                <h3><i class="fa-solid fa-cog"></i> S/N Look Over Settings</h3>
            </template>
            <div class="row overflow-y-scroll" style="max-height: 70vh;">
                <div class="col-md-12">
                    <Card class="p-3 m-2">
                        <template #title>
                            Groups
                        </template>
                        <template #content>
                            <div class="row my-3">
                                <div class="col-md-6">
                                    <span class="p-float-label">
	                                    <InputText id="groupName" type="text" class="col-md-12" v-model="newGroupName"/>
	                                <label for="groupName">Group Name</label>
                                    </span>
                                </div>
                                <div class="col-md-6">
                                    <Button label="Create New Group" icon="pi pi-plus" @click="createNewGroup()"
                                            class="p-button-lg col-md-12"/>
                                </div>
                            </div>
                            <div class="list-group set-height-300">
                                <div v-for="group in groups"
                                     class="list-group-item list-group-item-action d-flex justify-content-between align-items-center cursor-pointer"
                                     @click="selectGroup(group)">
                                    {{ group.name }}
                                    <div class="float-right">
                                        <Button v-tooltip="'Disable'" v-if="group.active"
                                                @click="disableGroup(group.id)" class="p-button-text py-2"><span
                                            style="color: #6b9f69"><i class="fa-solid fa-circle mx-2"></i></span>Enabled
                                        </Button>
                                        <Button v-tooltip="'Enable'" v-if="!group.active"
                                                @click="activateGroup(group.id)" class="p-button-text py-2"><span
                                            style="color: #3d4852"><i class="fa-solid fa-circle mx-2"></i></span>Disabled
                                        </Button>
                                        <Button v-tooltip="'Delete the group and all rules inside that group'"
                                                @click="deleteGroupConfirmation(group.id)"
                                                class="p-button-text py-2">
                                            <span style="color: #d9534f">
                                            <i class="fa-solid fa-trash mx-2"></i>
                                            </span>
                                        </Button>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </Card>


                    <Card class="p-3 m-2" v-if="selectedGroup">
                        <template #title>
                            <p>Rules For: <span style="font-weight: bold">{{ selectedGroup.name }}</span></p>
                        </template>
                        <template #content>


                            <div class="row my-3">
                                <div class="col-md-6">
                                    <span class="p-float-label">
	                                    <InputText id="rule" type="text" class="col-md-12 text-uppercase"
                                                   v-model="newRule"/>
	                                <label for="rule">Rule</label>
                                    </span>
                                </div>
                                <div class="col-md-6">
                                    <Button label="Add rule" icon="pi pi-plus" @click="createNewRule()"
                                            class="p-button-lg col-md-12"/>
                                </div>
                            </div>


                            <div v-if="selectedGroup" class="list-group set-height-300">
                                <div v-if="selectedGroupRules" v-for="rule in selectedGroupRules"
                                     class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    {{ rule.rule }}

                                    <Button v-tooltip="'Delete rule'"
                                            @click="deleteRule(rule.id)"
                                            class="p-button-text py-2">
                                            <span style="color: #d9534f">
                                            <i class="fa-solid fa-trash mx-2"></i>
                                            </span>
                                    </Button>

                                </div>
                            </div>
                        </template>
                    </Card>

                </div>
            </div>

            <template #footer>
                <Button label="Close" icon="pi pi-times" @click="checkerSettingsDialog = false" class="p-button-text"/>
            </template>
        </Dialog>

        <ConfirmDialog></ConfirmDialog>
    </div>
</template>

<script>
import Dialog from 'primevue/dialog';
import Button from "primevue/button";
import Card from 'primevue/card';
import InputText from 'primevue/inputtext';
import InputSwitch from 'primevue/inputswitch';
import ConfirmDialog from 'primevue/confirmdialog';

export default {
    components: {
        'Button': Button,
        'Dialog': Dialog,
        'Card': Card,
        'InputText': InputText,
        'InputSwitch': InputSwitch,
        'ConfirmDialog': ConfirmDialog,
    },
    name: "SNChecker",
    props: {
        boxId: String,
    },
    created() {
        this.getGroups();
        this.getAutoStartStatus();
    },
    data() {
        return {
            newGroupName: null,
            checkerDialog: false,
            checkerSettingsDialog: false,
            groups: [],
            selectedGroup: null,
            selectedGroupRules: null,
            newRule: null,
            autoStart: false,

            badSnFormats: [],
            snDubDb: [],
            snDubBox: [],

            lookOverResultDialog: false,
            notStarted: '<i class="fa-solid fa-question"></i>',
            spinner: '<i class="fas fa-spinner fa-pulse"></i>',
            good: '<span class="text-success"><i class="fa-solid fa-check"></i></span>',
            bad: '<span class="text-danger"><i class="fa-solid fa-circle-exclamation"></i></span>',
            warning: '<span class="text-warning"><i class="fa-solid fa-triangle-exclamation"></i></span>',

            boxIcon: '<i class="fa-solid fa-question"></i>',
            dbIcon: '<i class="fa-solid fa-question"></i>',
            formatIcon: '<i class="fa-solid fa-question"></i>',
        }
    },
    methods: {
        startLookingIfAutoLookEnabled() {
            if (this.autoStart) {
                this.checkerDialog = true;
                this.startLooking();
            }
        },
        toggleAutoStart() {
            axios.get(route('toggleAutoStart'))
                .then(response => {
                    this.getAutoStartStatus();
                })
                .catch(error => {
                    this.$root.errorDisplay(error);
                });
        },
        getAutoStartStatus() {
            axios.get(route('getAutoStartStatus'))
                .then(response => {
                    this.autoStart = response.data;
                    this.startLookingIfAutoLookEnabled();
                })
                .catch(error => {
                    this.$root.errorDisplay(error);
                });
        },
        startLooking() {
            this.startLookingSnFormats();
            this.startLookingSnDuplicationsInBox();
            this.startLookingSnDuplicationsInDatabase();
        },
        startLookingSnFormats() {
            this.formatIcon = this.spinner;
            axios.post(route('startLookingSnFormats'), {box_id: this.boxId})
                .then(response => {
                    this.badSnFormats = response.data;
                    if (this.badSnFormats.length === 0) {
                        this.formatIcon = this.good;
                    } else {
                        this.formatIcon = this.bad;
                    }
                })
                .catch(error => {
                    this.$root.errorDisplay(error);
                });
        },
        startLookingSnDuplicationsInBox() {
            this.boxIcon = this.spinner;
            axios.post(route('startLookingSnDuplicationsInBox'), {box_id: this.boxId})
                .then(response => {
                    this.snDubBox = response.data;
                    if (this.snDubBox.length === 0) {
                        this.boxIcon = this.good;
                    } else {
                        this.boxIcon = this.bad;
                    }
                })
                .catch(error => {
                    this.$root.errorDisplay(error);
                });
        },
        startLookingSnDuplicationsInDatabase() {
            this.dbIcon = this.spinner;
            axios.post(route('startLookingSnDuplicationsInDatabase'), {box_id: this.boxId})
                .then(response => {
                    this.snDubDb = response.data;
                    if (this.snDubDb.length === 0) {
                        this.dbIcon = this.good;
                    } else {
                        this.dbIcon = this.bad;
                    }
                })
                .catch(error => {
                    this.$root.errorDisplay(error);
                });
        },
        deleteRule(id) {
            axios.post(route('deleteRule'), {rule_id: id})
                .then(response => {
                    this.getRulesForGroup();
                })
                .catch(error => {
                    this.$root.errorDisplay(error);
                });
        },
        getGroups() {
            axios.get(route('getGroups'))
                .then(response => {
                    this.groups = response.data;
                })
                .catch(error => {
                    this.$root.errorDisplay(error);
                });
        },
        selectGroup(group) {
            this.selectedGroup = group;
            this.getRulesForGroup()
        },
        getRulesForGroup() {
            axios.get(route('getRulesForGroup', this.selectedGroup.id))
                .then(response => {
                    this.selectedGroupRules = response.data;
                })
                .catch(error => {
                    this.$root.errorDisplay(error);
                });
        },
        createNewGroup() {
            axios.post(route('createNewSnCheckGroup'), {name: this.newGroupName})
                .then(response => {
                    this.newGroupName = null;
                    this.getGroups();
                })
                .catch(error => {
                    this.$root.errorDisplay(error);
                });
        },
        createNewRule() {
            axios.post(route('createNewSnCheckRule'), {rule: this.newRule, group_id: this.selectedGroup.id})
                .then(response => {
                    this.newRule = null;
                    this.getRulesForGroup();
                })
                .catch(error => {
                    this.$root.errorDisplay(error);
                });
        },
        deleteGroupConfirmation(id) {
            this.$confirm.require({
                message: 'Are you sure you want to delete group with all rules?',
                header: 'Confirmation',
                icon: 'pi pi-exclamation-triangle',
                accept: () => {
                    this.deleteGroup(id)
                },
                reject: () => {
                    //
                }
            });
        },
        deleteGroup(id) {
            axios.get(route('deleteSnCheckGroup', id))
                .then(response => {
                    if (this.selectedGroup.id === id) {
                        this.selectedGroup = null;
                        this.selectedGroupRules = null;
                    }

                    this.getGroups();
                    this.getRulesForGroup();
                })
                .catch(error => {
                    this.$root.errorDisplay(error);
                });
        },
        activateGroup(id) {
            axios.get(route('activateSnCheckGroup', id))
                .then(response => {
                    this.getGroups();
                })
                .catch(error => {
                    this.$root.errorDisplay(error);
                });
        },
        disableGroup(id) {
            axios.get(route('disableSnCheckGroup', id))
                .then(response => {
                    this.getGroups();
                })
                .catch(error => {
                    this.$root.errorDisplay(error);
                });
        },
    },
}
</script>

<style scoped>
.p-dialog-content {
    overflow-y: scroll !important;
}
</style>
