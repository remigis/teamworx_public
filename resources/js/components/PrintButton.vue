<template>
    <Button class="btn btn-primary hide-on-print btn-sm" @click="startPrinting()"><i class="fa-solid fa-print"></i>
        Print
    </Button>
</template>

<script>
import Button from "primevue/button";

export default {
    components: {
        'Button': Button,
    },
    name: "PrintButton",
    methods: {
        startPrinting() {
            window.print();
        }
    }
}
</script>

<style scoped>
.btn-primary {
    color: #fff;
    background-color: rgb(219, 113, 98);
    border-color: #cb412f;
    border-radius: 0px;
}

.btn-primary:active {
    color: #fff;
    background-color: rgb(219, 113, 98);
    border-color: #cb412f;
    border-radius: 0px;
}

.btn-primary:focus {
    color: #fff;
    background-color: rgb(219, 113, 98) !important;
    border-color: #cb412f !important;
    border: 1px solid !important;
    box-shadow: none !important;
    border-radius: 0px !important;
}

.btn-primary:hover {
    color: #cb412f;
    background-color: rgb(250, 233, 231);
    border-color: #cb412f;
    border-radius: 0px;
}
</style>
