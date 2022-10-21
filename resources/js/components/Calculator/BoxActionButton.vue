<template>
    <div>
        <SplitButton label="Delivery line" icon="fa-solid fa-file-signature" @click="copyDeliveryString" :model="items"
                     class="col-12">
        </SplitButton>
        <Dialog :visible.sync="display">
            <template #header>
                <h3>How many boxes ?</h3>
            </template>

            <InputNumber id="horizontal" v-model="labelCount" showButtons buttonLayout="horizontal" :step="1" :min="1"
                         decrementButtonClass="p-button-secondary" incrementButtonClass="p-button-secondary"
                         incrementButtonIcon="pi pi-plus" decrementButtonIcon="pi pi-minus"/>

            <template #footer>
                <Button label="Cancel" @click="close" icon="fa-solid fa-times" class="p-button-text"/>
                <Button label="Print" @click="print" icon="fa-solid fa-print" autofocus/>
            </template>
        </Dialog>
    </div>
</template>

<script>
import SplitButton from 'primevue/splitbutton';
import Button from 'primevue/button';
import Dialog from 'primevue/dialog';
import InputNumber from 'primevue/inputnumber';

export default {
    name: 'BoxActionButton',
    components: {
        'SplitButton': SplitButton,
        'Button': Button,
        'Dialog': Dialog,
        'InputNumber': InputNumber,
    },
    props: {
        deliveryString: String,
        boxId: String,
    },
    data() {
        return {
            labelCount: 1,
            display: false,
            items: [
                {
                    label: 'Print',
                    icon: 'fa-solid fa-print',
                    command: () => {
                        this.display = true;
                    }
                },
                {
                    label: 'Scan',
                    icon: 'fa-solid fa-barcode',
                    command: () => {
                        this.scan();
                    }
                },

            ]
        }
    },
    methods: {
        copyDeliveryString(event) {
            if (navigator.clipboard && window.isSecureContext) {
                return navigator.clipboard.writeText(this.deliveryString);
            } else {
                let textArea = document.createElement("textarea");
                textArea.value = this.deliveryString;
                textArea.style.position = "fixed";
                textArea.style.left = "-999999px";
                textArea.style.top = "-999999px";
                document.body.appendChild(textArea);
                textArea.focus();
                textArea.select();
                return new Promise((res, rej) => {
                    document.execCommand('copy') ? res() : rej();
                    textArea.remove();
                });
            }
        },
        close() {
            this.display = false;
        },
        print() {
            window.open(window.location.origin + '/print/boxLabel/' + this.boxId + '/' + this.labelCount);
            this.close();
            this.labelCount = 1;
        },
        scan() {
            window.open(window.location.origin + '/scan/box/' + this.boxId);
        }
    }
}
</script>
