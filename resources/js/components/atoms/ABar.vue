<template>
    <input type="text" v-model="text">
</template>

<script>
    const sublineHeadlineSizeRatio = 0.4;

    import Bar from "../../service/canvas/Bar";
    import {BarTypes as Types} from "../../service/canvas/Constants";
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
            baseFontSize: {
                required: true,
                type: Number,
            },
            imageWidth: {
                required: true,
                type: Number,
            }
        },

        computed: {
            fontSize() {
                if (this.type === Types.headline) {
                    return this.baseFontSize;
                } else {
                    return this.baseFontSize * sublineHeadlineSizeRatio;
                }
            },
        },

        mounted() {
            this.draw('create');
            this.loadFonts().then(() => this.draw('font'));
        },

        destroyed() {
            this.$emit('removed');
        },

        methods: {
            draw(action) {
                this.bar.text = this.text;
                this.bar.alignment = this.alignment;
                this.bar.type = this.type;
                this.bar.schema = this.schema;
                this.bar.fontSize = this.fontSize;
                this.bar.imageWidth = this.imageWidth;

                this.$emit('drawn', this.bar.draw(), action);
            },

            loadFonts() {
                const fat = new FontFaceObserver('SanukFat');
                const bold = new FontFaceObserver('SanukBold');

                return Promise.all([fat.load(), bold.load()]);
            },
        },

        watch: {
            text() {
                this.draw('text');
            },
            alignment() {
                this.draw('alignment');
            },
            type() {
                this.draw('type');
            },
            schema() {
                this.draw('schema');
            },
            baseFontSize() {
                this.draw('baseFontSize');
            },
            imageWidth() {
                this.draw('imageWidth');
            },
        }
    }
</script>

<style scoped>

</style>
