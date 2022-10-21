<template>
    <div class="container">
        <div class="row my-3">
            <div class="col-md-12">
                <Panel class="left">
                    <template #header>
                        <i class="fa-solid fa-key"></i> Set up your password
                    </template>
                    <div class="container">
                        <div class="row">
                            <span class="p-float-label col-md-6 my-4">
	                        <InputText :disabled="true" id="name" type="text" v-model="name"/>
	                        <label for="name" class="ml-4">Name</label>
                        </span>
                            <span class="p-float-label col-md-6 my-4">
	                        <InputText :disabled="true" id="email" type="email" v-model="email"/>
	                        <label for="email" class="ml-4">Email</label>
                        </span>
                            <span class="p-float-label col-md-6 my-4">
	                        <InputText id="password" type="password" :class="{'p-invalid': cantSubmit()}"
                                       v-model="password"/>
	                        <label for="password" class="ml-4">Password</label>
                        </span>
                            <span class="p-float-label col-md-6 my-4">
	                        <InputText id="passwordConfirm" type="password" :class="{'p-invalid': cantSubmit()}"
                                       v-model="confirmPassword"/>
	                        <label for="passwordConfirm" class="ml-4">Confirm password</label>
                        </span>
                            <Button :disabled="cantSubmit()" label="Finish registration"
                                    @click="finishRegistration()"
                                    icon="fa-solid fa-person" iconPos="left" class="col-md-12"/>
                        </div>
                    </div>
                </Panel>
            </div>
        </div>
    </div>
</template>

<script>
import InputText from "primevue/inputtext";
import Button from "primevue/button";
import Panel from "primevue/panel";
import TabPanel from "primevue/tabpanel";

export default {
    components: {
        'InputText': InputText,
        'Button': Button,
        'Panel': Panel,
        'TabPanel': TabPanel,
    },
    props: {
        name: String,
        email: String,
        token: String,
    },
    name: "RegisterWithInvitation",
    data() {
        return {
            password: null,
            confirmPassword: null,
        }
    },
    created() {
    },
    mounted() {

    },
    methods: {
        finishRegistration() {
            axios.post(route('finishRegistration'), {
                password: this.password,
                password_confirmation: this.confirmPassword,
                token: this.token
            })
                .then(response => {
                    location.replace('/login');
                })
                .catch(error => {
                    console.log(error);
                    this.$root.errorDisplay(error);
                });
        },
        cantSubmit() {
            let cant = false;

            if (this.password !== null && this.confirmPassword) {
                if (this.password.length < 5 || this.confirmPassword.length < 5) {
                    cant = true;
                }
            } else {
                cant = true;
            }

            if (this.password !== this.confirmPassword) {
                cant = true;
            }
            return cant;
        }
    }
}
</script>

<style scoped>

</style>
