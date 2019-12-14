<template>
    <div class="form-group">
        <label
            class="mb-0"
            for="border-switch"
        >{{$t('images.create.border')}}</label>
        <div class="custom-control custom-switch">
            <input
                class="custom-control-input"
                id="border-switch"
                type="checkbox"
                v-model="border"
            >
            <label
                class="custom-control-label"
                for="border-switch"
            >{{$t('images.create.borderShow')}}</label>
            <small
                class="d-block"
                v-if="!border"
            >{{$t('images.create.borderNone')}}</small>
        </div>
    </div>
</template>

<script>
    import {Border} from "../../service/canvas/elements/Border";
    import ASelect from "../atoms/ASelect";

    export default {
        name: "MBorderBlock",
        components: {ASelect},
        data() {
            return {
                block: new Border(),
                border: true
            }
        },

        props: {
            imageWidth: {
                required: true,
                type: Number,
            },
            imageHeight: {
                required: true,
                type: Number
            }
        },

        mounted() {
            this.draw();
            this.$emit('widthChanged', this.block.borderWidth);
        },

        methods: {
            draw() {
                this.block.width = this.imageWidth;
                this.block.height = this.imageHeight;
                this.block.border = this.border;
                this.$emit('drawn', this.block.draw());
            },
        },

        watch: {
            imageWidth() {
                this.draw();
                this.$emit('widthChanged', this.block.borderWidth);
            },
            imageHeight() {
                this.draw();
                this.$emit('widthChanged', this.block.borderWidth);
            },
            border() {
                this.draw();
            }
        },
    }
</script>

<style scoped>

</style>
