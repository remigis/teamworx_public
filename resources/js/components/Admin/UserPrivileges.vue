<template>
    <div class="row my-3">
        <div class="col-md-12">
            <Panel class="left">
                <template #header>
                    <i class="fa-solid fa-book"></i> Manage user privileges
                </template>

                <div class="row">
                <span class="p-float-label col-md-6 my-4">
                <AutoComplete id="user" class="col-md-12" v-model="selectedUser" :suggestions="filteredUsers"
                              @complete="searchUser($event)" @item-select="userSelect()" @item-unselect="unselect()"
                              @clear="unselect()" field="name"></AutoComplete>
                    <label for="user" style="margin-left: 38px;">Name</label>
                </span>
                    <span class="col-md-6 py-3 my-3" style="font-size: 20px;">
                        Selected User: <strong v-if="selectedUser">{{ selectedUser.name }}</strong>
                    </span>

                    <div class="col-md-6 p3">
                        <Fieldset class="col-md-12">
                            <template #legend>
                                <div class="text-center">
                                    <i class="fa-solid fa-lock-open"></i> User can do this:
                                </div>
                            </template>

                            <div class="list-group set-height-300">
                                <div v-for="privilege in selectedUsersPrivileges"
                                     @click="removePrivilege(selectedUser.id, privilege.privilege.id)"
                                     class="list-group-item list-group-item-action cursor-pointer"><i
                                    class="fa-solid fa-arrow-left fa-rotate-180 text-danger"></i>
                                    {{ privilege.privilege.text }}
                                </div>
                            </div>

                        </Fieldset>
                    </div>

                    <div class="col-md-6 p3">
                        <Fieldset class="col-md-12">
                            <template #legend>
                                <div class="text-center">
                                    <i class="fa-solid fa-lock"></i> User can't do this:
                                </div>
                            </template>
                            <div class="list-group set-height-300">
                                <div v-for="privilege in selectedUsersNotOwnedPrivileges"
                                     @click="addPrivilege(selectedUser.id, privilege.id)"
                                     class="list-group-item list-group-item-action cursor-pointer"><i
                                    class="fa-solid fa-arrow-left text-success"></i> {{ privilege.text }}
                                </div>
                            </div>
                        </Fieldset>
                    </div>

                </div>
            </Panel>
        </div>

    </div>
</template>

<script>
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
import ToggleButton from "primevue/togglebutton";
import DataTable from "primevue/datatable";
import Column from "primevue/column";
import Card from "primevue/card";
import AutoComplete from "primevue/autocomplete";
import Slider from "primevue/slider";
import Dropdown from "primevue/dropdown";
import Fieldset from 'primevue/fieldset';

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
        'Slider': Slider,
        'Dropdown': Dropdown,
        'Fieldset': Fieldset,

    },
    name: "UserPrivileges",
    data() {
        return {
            selectedUser: null,
            filteredUsers: [],
            selectedUsersPrivileges: null,
            selectedUsersNotOwnedPrivileges: null,
            allPrivileges: null,
        }
    },
    created() {
    },
    mounted() {

    },
    methods: {
        searchUser() {
            this.unselect();
            axios.post(route('searchUserForPrivilegeEdit'), {string: this.selectedUser})
                .then(response => {
                    this.filteredUsers = response.data;
                })
                .catch(error => {
                    this.$root.errorDisplay(error);
                });
        },
        userSelect() {
            this.getPrivilegeSetsForUser();
        },
        unselect() {
            this.selectedUsersPrivileges = null;
            this.selectedUsersNotOwnedPrivileges = null;
            this.allPrivileges = null;
        },
        removePrivilege(userId, privilege) {
            axios.post(route('removePrivilege'), {userId: userId, privilegeId: privilege})
                .then(response => {
                    this.getPrivilegeSetsForUser();
                })
                .catch(error => {
                    this.$root.errorDisplay(error);
                });
        },
        addPrivilege(userId, privilege) {
            axios.post(route('addPrivilege'), {userId: userId, privilegeId: privilege})
                .then(response => {
                    this.getPrivilegeSetsForUser();
                })
                .catch(error => {
                    this.$root.errorDisplay(error);
                });
        },
        getPrivilegeSetsForUser() {
            axios.post(route('getPrivilegeSetsForUser'), {userId: this.selectedUser.id})
                .then(response => {
                    this.selectedUsersNotOwnedPrivileges = response.data.notOwnedPrivileges;
                    this.selectedUsersPrivileges = response.data.ownedPrivileges;
                    this.allPrivileges = response.data.allPrivileges;
                    console.log(this.selectedUsersPrivileges);
                })
                .catch(error => {
                    this.$root.errorDisplay(error);
                });
        }
    }
}
</script>

<style scoped>

</style>
