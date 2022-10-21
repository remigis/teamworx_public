<template>
    <div>
        <Toast position="top-right"></Toast>
    </div>
</template>

<script>
import Toast from "primevue/toast";

export default {
    components: {
        'Toast': Toast,
    },
    name: "ErrorDisplay"
}
</script>

<style scoped>

</style>
