<template>
    <input type="text" v-model="text">
</template>

<script>
    import {Bar} from "../../service/canvas/Bar";
    import FontFaceObserver from "fontfaceobserver";

    export default {
        name: "ABar",
        data() {
            return {
                text: 'grüne jetzt',
                bar: new Bar(),
            }
        },

        props: {
            alignment: {
                required: true,
            },
            type: {
                required: true,
            },
            schema: {
                required: true,
            },
            fontSize: {
                required: true,
                type: Number,
            },
            imageWidth: {
                required: true,
                type: Number,
            }
        },

        mounted() {
            this.loadFonts().then(() => this.redraw());
        },

        methods: {
            redraw() {
                this.bar.text = this.text;
                this.bar.alignment = this.alignment;
                this.bar.type = this.type;
                this.bar.schema = this.schema;
                this.bar.fontSize = this.fontSize;
                this.bar.imageWidth = this.imageWidth;

                this.$emit('drawn', this.bar.draw());
            },

            loadFonts() {
                const fat = new FontFaceObserver('SanukFat');
                const bold = new FontFaceObserver('SanukBold');

                return Promise.all([fat.load(), bold.load()]);
            },
        },

        watch: {
            text() {
                this.redraw();
            },
            alignment() {
                this.redraw();
            },
            type() {
                this.redraw();
            },
            schema() {
                this.redraw();
            },
            fontSize() {
                this.redraw();
            },
            imageWidth() {
                this.redraw();
            },
        }
    }
</script>

<style lang="scss" scoped>
    .a-canvas {
        border: 1px solid red;
    }
</style>
