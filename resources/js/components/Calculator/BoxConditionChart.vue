<template>
    <div>
        <Chart type="pie" :data="chartData" :options="lightOptions"/>
    </div>
</template>

<script>
import Chart from "primevue/chart";

export default {
    name: 'ConditionChart',
    components: {
        'Chart': Chart,
    },
    props: {
        conditionACount: String,
        conditionBCount: String,
        conditionCCount: String,
        conditionDCount: String,
        conditionRCount: String,
        conditionSCount: String,
        conditionUCount: String,
    },
    data() {
        return {
            chartData: {
                labels: ['A (' + this.procentage(this.conditionACount) + '%)',
                    'B (' + this.procentage(this.conditionBCount) + '%)',
                    'C (' + this.procentage(this.conditionCCount) + '%)',
                    'D (' + this.procentage(this.conditionDCount) + '%)',
                    'R (' + this.procentage(this.conditionRCount) + '%)',
                    'S (' + this.procentage(this.conditionSCount) + '%)',
                    'U (' + this.procentage(this.conditionUCount) + '%)'],
                datasets: [
                    {
                        data: [this.conditionACount, this.conditionBCount, this.conditionCCount, this.conditionDCount, this.conditionRCount, this.conditionSCount, this.conditionUCount],
                        backgroundColor: ["#49b445", "#61a15e", "#92a692", "#75a2a8", "#e78d8d", "#e13838", "#ffffff"],
                        hoverBackgroundColor: ["#49b445", "#61a15e", "#92a692", "#75a2a8", "#e78d8d", "#e13838", "#ffffff"],
                    }
                ]
            },
            lightOptions: {
                plugins: {
                    legend: {
                        labels: {
                            color: '#495057'
                        }
                    }
                }
            },
        }
    },
    methods: {
        procentage($items) {
            let $all = this.sum();
            if ($all > 0) {
                return (($items * 100) / $all).toFixed(1);
            } else {
                return 0;
            }

        },
        sum() {
            return parseInt(this.conditionACount) + parseInt(this.conditionBCount) + parseInt(this.conditionCCount) + parseInt(this.conditionDCount) + parseInt(this.conditionRCount) + parseInt(this.conditionSCount) + parseInt(this.conditionUCount);
        }
    }
}
</script>
