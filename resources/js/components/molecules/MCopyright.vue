<template>
    <div class="m-copyright form-group">
        <label for="image_copyright">{{$t('images.create.copyright')}}</label>
        <input
            class="form-control"
            id="image_copyright"
            maxlength="192"
            type="text"
            v-model="value"
        >
    </div>
</template>

<script>
    import {Copyright} from "../../service/canvas/elements/Copyright";

    export default {
        name: "MCopyright",

        data() {
            return {
                block: new Copyright(),
                value: '',
            }
        },

        props: {
            imageWidth: {
                required: true,
                type: Number,
            },
            imageHeight: {
                required: true,
                type: Number,
            },
            color: {
                required: true,
                type: String,
            },
        },

        mounted() {
            this.draw();
        },

        methods: {
            updateLegalOriginator(value) {
                this.$store.commit('legal/update', {key: 'originator', value: value});
            },

            draw() {
                this.block.width = this.imageWidth;
                this.block.height = this.imageHeight;
                this.block.text = this.$t('images.create.imageCopyInfo', {photographer: this.value});
                this.block.color = this.color;

                this.$emit('drawn', this.block.draw());
            },
        },

        watch: {
            value(val) {
                this.updateLegalOriginator(val);
                this.draw();
            },

            imageWidth() {
                this.draw();
            },

            imageHeight() {
                this.draw();
            },

            color() {
                this.draw();
            },
        }
    }
</script>

<style scoped>

</style>
