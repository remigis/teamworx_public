<template>
    <div>
        <SplitButton label="Reload" icon="fa-solid fa-arrows-rotate" @click="reloadPage" :model="items" class="col-12">
        </SplitButton>
    </div>
</template>

<script>
import SplitButton from 'primevue/splitbutton';
import Button from 'primevue/button';

export default {
    name: 'BoxLinkButton',
    components: {
        'SplitButton': SplitButton,
        'Button': Button,
    },
    props: {
        boxId: String,
        domain: String,
    },
    data() {
        return {
            items: [
                {
                    label: 'GF Testing',
                    icon: 'fa-solid fa-flask',
                    command: () => {
                        window.open(this.domain + '/system/goodsflow/index.php?r=testing/process&karton=' + this.boxId);
                    }
                },
                {
                    label: 'GF Box',
                    icon: 'fa-solid fa-box',
                    command: () => {
                        window.open(this.domain + '/system/goodsflow/index.php?r=testing/artikel&karton=' + this.boxId);
                    }
                }

            ]
        }
    },
    methods: {
        reloadPage() {
            window.location.reload();
        }
    }
}
</script>
