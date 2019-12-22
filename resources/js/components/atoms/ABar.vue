<template>
    <div class="container-fluid mb-1">
        <div class="row">
            <input
                :class="inputClass"
                class="form-control col"
                type="text"
                v-model="text"
                @input="$emit('textChanged', text)"
            >
            <button
                :class="buttonClass"
                :title="$t('images.create.barAdd')"
                @click="$emit('clone', text)"
                class="btn ml-1"
                v-if="cloneable"><i class="mdi mdi-add"></i></button>
            <button
                :class="buttonClass"
                :title="$t('images.create.barRemove')"
                @click="$emit('remove')"
                class="btn ml-1"
                v-if="deletable"><i class="mdi mdi-remove"></i></button>
        </div>
    </div>
</template>

<script>
    const sublineHeadlineSizeRatio = 0.4;

    import Bar from "../../service/canvas/elements/Bar";
    import {BarTypes as Types, BarSchemes as Schemes} from "../../service/canvas/Constants";
    import FontFaceObserver from "fontfaceobserver";

    export default {
        name: "ABar",
        data() {
            return {
                text: '',
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
            },
            deletable: {
                required: true,
                type: Boolean,
            },
            cloneable: {
                required: true,
                type: Boolean
            },
            initialText: {
                required: true,
                type: String
            },
        },

        computed: {
            fontSize() {
                if (this.type === Types.headline) {
                    return this.baseFontSize;
                } else {
                    return this.baseFontSize * sublineHeadlineSizeRatio;
                }
            },

            buttonClass() {
                switch (this.schema) {
                    case Schemes.green:
                        return 'btn-secondary';

                    case Schemes.magenta:
                        return 'btn-primary';

                    case Schemes.white:
                        return 'btn-outline-secondary';
                }
            },

            inputClass() {
                return this.schema === Schemes.magenta ? 'magenta' : 'green';
            },
        },

        mounted() {
            this.text = this.initialText;
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

<style lang="scss" scoped>
    .magenta {
        color: $primary;
    }

    .green {
        $lightGreen: rgba($secondary, .25);
        color: darken($secondary, 5);

        &:focus {
            border-color: $secondary;
            box-shadow: 0 0 0 0.2rem $lightGreen;
        }
    }
</style>
