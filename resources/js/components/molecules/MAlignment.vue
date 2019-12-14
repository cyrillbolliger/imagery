<template>
    <AButtonGroup
        :options="options"
        :title="$t('images.create.layout')"
        @input="$emit('input', $event)"
        class="form-group"
        v-model="alignment"
    ></AButtonGroup>
</template>
<script>
    import {Alignments} from "../../service/canvas/Constants";
    import AButtonGroup from "../atoms/AButtonGroup";

    export default {
        name: 'MAlignment',
        components: {AButtonGroup},
        data() {
            return {
                alignment: this.value,
                options: [
                    {value: Alignments.left, text: this.$t('images.create.barsLeft')},
                    {value: Alignments.right, text: this.$t('images.create.barsRight')},
                ],
            }
        },
        props: {
            value: {
                required: true,
            },
        },
        watch: {
            value(value) {
                this.alignment = value;
            }
        }
    }
</script>
