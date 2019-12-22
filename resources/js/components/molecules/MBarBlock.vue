<template>
    <div>
        <div class="form-group">
            <label
                class="mb-0"
                for="font-size"
            >{{$t('images.create.fontSize')}}</label>
            <input
                :disabled="tooMuchText"
                :max="fontSizeMax"
                :min="fontSizeMin"
                @input="draw()"
                class="form-control-range"
                id="font-size"
                step="1"
                type="range"
                v-model.number="fontSize"
            >
        </div>

        <div class="form-group">
            <label
                class="mb-0"
                for="font-size"
            >{{$t('images.create.bars')}}</label>

            <ABar
                :alignment="alignment"
                :base-font-size="fontSize"
                :cloneable="headlinesCount < 3"
                :deletable="headlinesPrimaryCount > 1"
                :image-width="imageWidth"
                :initialText="initialText ? initialText : 'Headline 1'"
                :key="`headlinePrimary-${n}`"
                :schema="schemaHeadlinePrimary"
                :type="typeHeadline"
                @clone="headlinesPrimaryAdd($event)"
                @drawn="update(headlinesPrimary, n, ...arguments)"
                @remove="headlinesPrimaryCount--"
                @removed="remove('headlinesPrimary', n)"
                @textChanged="updateText(`headlinesPrimary-${n}`, $event)"
                v-for="n in headlinesPrimaryCount"
            />

            <ABar
                :alignment="alignment"
                :base-font-size="fontSize"
                :cloneable="headlinesCount < 3"
                :deletable="headlinesSecondaryCount > 1"
                :image-width="imageWidth"
                :initialText="initialText ? initialText : 'Headline 2'"
                :key="`headlineSecondary-${n}`"
                :schema="schemaHeadlineSecondary"
                :type="typeHeadline"
                @clone="headlinesSecondaryAdd($event)"
                @drawn="update(headlinesSecondary, n, ...arguments)"
                @remove="headlinesSecondaryCount--"
                @removed="remove('headlinesSecondary', n)"
                @textChanged="updateText(`headlinesSecondary-${n}`, $event)"
                v-for="n in headlinesSecondaryCount"
            />

            <ABar
                :alignment="alignment"
                :base-font-size="fontSize"
                :cloneable="sublinesCount < 2"
                :deletable="sublinesCount > 0"
                :image-width="imageWidth"
                :initialText="initialText ? initialText : 'Subline'"
                :key="`subline-${n}`"
                :schema="schemaSubline"
                :type="typeSubline"
                @clone="sublinesAdd($event)"
                @drawn="update(sublines, n, ...arguments)"
                @remove="sublinesCount--"
                @removed="remove('sublines', n)"
                @textChanged="updateText(`sublines-${n}`, $event)"
                v-for="n in sublinesCount"
            />

            <button
                :class="buttonClassSubline"
                @click="sublinesCount++"
                class="btn"
                v-if="sublinesCount === 0"
            >{{$t('images.create.sublineAdd')}}
            </button>

            <div class="alert alert-warning" role="alert" v-if="tooMuchText">
                {{$t('images.create.tooMuchText')}}
            </div>
        </div>
    </div>
</template>

<script>
    const minFontSizeFactor = 0.08; // the correct 175% would be 0.0925
    const maxFontSizeFactor = 1.08;

    import {BarSchemes as Schemes, BarTypes as Types, ColorSchemes} from "../../service/canvas/Constants";
    import BarBlock from "../../service/canvas/blocks/BarBlock";
    import ABar from "../atoms/ABar";

    export default {
        name: "MBarBlock",
        components: {ABar},

        data() {
            return {
                headlinesPrimary: [],
                headlinesSecondary: [],
                sublines: [],
                headlinesPrimaryCount: 1,
                headlinesSecondaryCount: 1,
                sublinesCount: 1,
                fontSizeMax: 100,
                fontSize: 100,
                typeHeadline: Types.headline,
                typeSubline: Types.subline,
                tooMuchText: false,
                block: null,
                eventCounter: {},
                initialText: null,
                texts: {},
            }
        },

        props: {
            alignment: {
                required: true,
            },
            colorSchema: {
                required: true,
            },
            imageWidth: {
                required: true,
                type: Number,
            },
            imageHeight: {
                required: true,
                type: Number
            }
        },

        computed: {
            schemaHeadlinePrimary() {
                if (ColorSchemes.white === this.colorSchema) {
                    return Schemes.white;
                } else {
                    return Schemes.green;
                }
            },

            schemaHeadlineSecondary() {
                return Schemes.magenta;
            },

            schemaSubline() {
                if (ColorSchemes.greengreen === this.colorSchema) {
                    return Schemes.green;
                } else {
                    return Schemes.white;
                }
            },

            buttonClassSubline() {
                if (ColorSchemes.greengreen === this.colorSchema) {
                    return 'btn-secondary';
                } else {
                    return 'btn-outline-secondary';
                }
            },

            fontSizeMin() {
                // base the minimal font size on a normalized side length of
                // the image.
                // to get a normalized side length, square the image width,
                // and multiply it with the height, then take the third root.
                // this way we only violate the corporate design rules on slim
                // portrait images (without violation, we can't write anything
                // meaning full on a instagram story)
                const cube = this.imageHeight * this.imageWidth ** 2;
                const sideNormalized = Math.pow(cube, 1 / 3);
                const min = sideNormalized * minFontSizeFactor;
                return Math.ceil(min);
            },

            barCount() {
                return this.headlinesCount
                    + this.sublinesCount;
            },

            headlinesCount() {
                return this.headlinesPrimaryCount
                    + this.headlinesSecondaryCount;
            }
        },

        created() {
            this.block = new BarBlock(
                this.headlinesPrimary,
                this.headlinesSecondary,
                this.sublines,
            );
        },

        mounted() {
            this.draw()
        },

        methods: {
            update(array, index, bar, event) {
                this.$set(array, index, bar);

                if (this.isSingleBarEvent(event)) {
                    this.draw();
                    return;
                }

                if (!this.eventCounter[event]) {
                    this.eventCounter[event] = 0;
                }

                this.eventCounter[event]++;

                if (this.eventCounter[event] === this.barCount) {
                    this.eventCounter[event] = 0;
                    this.draw();
                }
            },

            remove(type, index) {
                this.removeBar(this[type], index);
                this.removeText(`${type}-${index}`);
            },

            removeBar(array, index) {
                this.$delete(array, index);
                this.draw();
            },

            removeText(index) {
                this.$delete(this.texts, index);
                this.updateText();
            },

            draw() {
                this.block.alignment = this.alignment;

                this.block.draw(); // called twice. first call is needed to determine size for content based font adjustment
                const fitsInImage = this.adjustFontSize();

                if (fitsInImage) {
                    this.$emit('drawn', this.block.draw());
                }
            },

            adjustFontSize() {
                const min = this.fontSizeMin;
                const maxWidth = this.imageWidth * maxFontSizeFactor;
                const imageToBlockRatio = maxWidth / this.block.width;
                let max = this.fontSize * imageToBlockRatio;
                max = Math.floor(max); // the range slider wants integers

                if (this.fontSize < min) {
                    this.fontSize = min;
                    return false;
                }

                if (max < min) {
                    this.fontSize = min;
                    this.fontSizeMax = min;
                    this.tooMuchText = true;
                    return false;
                } else {
                    this.tooMuchText = false;
                }

                if (this.block.width > maxWidth) {
                    this.fontSizeMax = max;
                    this.fontSize = max;
                    return false;
                }

                this.fontSizeMax = max;

                return true;
            },

            isSingleBarEvent(event) {
                return ['text', 'schema', 'create'].indexOf(event) !== -1;
            },

            headlinesPrimaryAdd(text) {
                this.initialText = text;
                this.headlinesPrimaryCount++;
            },

            headlinesSecondaryAdd(text) {
                this.initialText = text;

                const message = this.$t('images.create.headlineSecondaryAdd');

                if (!confirm(message)) {
                    this.headlinesSecondaryCount++;
                }
            },

            sublinesAdd(text) {
                this.initialText = text;
                this.sublinesCount++;
            },

            updateText(key = null, text = null) {
                if (key && text) {
                    this.texts[key] = text;
                }

                let flatText = '';
                Object.keys(this.texts)
                    .sort()
                    .forEach(key => flatText += ` ${this.texts[key]}`);

                this.$emit('textChanged', flatText.trim());
            }
        },

        watch: {
            imageWidth() {
                this.draw();
            },
            imageHeight() {
                this.draw();
            }
        }
    }
</script>

<style scoped>

</style>
