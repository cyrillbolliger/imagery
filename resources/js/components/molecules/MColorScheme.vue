<template>
    <div class="form-group">
        <AButtonGroup
            :options="options"
            :title="$t('images.create.colorScheme')"
            @input="$emit('input', $event)"
            v-model="scheme"
        ></AButtonGroup>
        <small v-if="scheme === schemes.greengreen"
        >{{$t('images.create.greengreenWarning')}}</small>
    </div>
</template>
<script>
    import {ColorSchemes} from "../../service/canvas/Constants";
    import AButtonGroup from "../atoms/AButtonGroup";

    export default {
        name: 'MColorScheme',
        components: {AButtonGroup},
        data() {
            return {
                scheme: this.value,
                schemes: ColorSchemes,
                options: [
                    {value: ColorSchemes.white, text: this.$t('images.create.white')},
                    {value: ColorSchemes.green, text: this.$t('images.create.green')},
                    {value: ColorSchemes.greengreen, text: this.$t('images.create.greengreen')},
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
                this.scheme = value;
            }
        }
    }
</script>
