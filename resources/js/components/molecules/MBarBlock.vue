<template>
    <div>
        <ABar
            :alignment="alignment"
            :base-font-size="fontSize"
            :image-width="imageWidth"
            :key="`headlinePrimary-${n}`"
            :schema="schemaHeadlinePrimary"
            :type="typeHeadline"
            @drawn="update(headlinesPrimary, n, ...arguments)"
            @removed="remove(headlinesPrimary, n)"
            v-for="n in headlinesPrimaryCount"
        ></ABar>
        <button
            :class="buttonClassPrimaryHeadline"
            @click="headlinesPrimaryCount++"
            class="btn"
            v-if="headlinesCount < 3"
        >{{$t('images.create.barAdd')}}
        </button>
        <button
            :class="buttonClassPrimaryHeadline"
            @click="headlinesPrimaryCount--"
            class="btn"
            v-if="headlinesPrimaryCount > 1"
        >{{$t('images.create.barRemove')}}
        </button>
        <br>

        <ABar
            :alignment="alignment"
            :base-font-size="fontSize"
            :image-width="imageWidth"
            :key="`headlineSecondary-${n}`"
            :schema="schemaHeadlineSecondary"
            :type="typeHeadline"
            @drawn="update(headlinesSecondary, n, ...arguments)"
            @removed="remove(headlinesSecondary, n)"
            v-for="n in headlinesSecondaryCount"
        ></ABar>
        <button
            @click="headlinesSecondaryAdd"
            class="btn btn-primary"
            v-if="headlinesCount < 3"
        >{{$t('images.create.barAdd')}}
        </button>
        <button
            @click="headlinesSecondaryCount--"
            class="btn btn-primary"
            v-if="headlinesSecondaryCount > 1"
        >{{$t('images.create.barRemove')}}
        </button>
        <br>

        <ABar
            :alignment="alignment"
            :base-font-size="fontSize"
            :image-width="imageWidth"
            :key="`subline-${n}`"
            :schema="schemaSubline"
            :type="typeSubline"
            @drawn="update(sublines, n, ...arguments)"
            @removed="remove(sublines, n)"
            v-for="n in sublinesCount"
        ></ABar>
        <button
            :class="buttonClassSubline"
            @click="sublinesCount++"
            class="btn"
            v-if="sublinesCount < 2"
        >{{$t('images.create.barAdd')}}
        </button>
        <button
            :class="buttonClassSubline"
            @click="sublinesCount--"
            class="btn"
            v-if="sublinesCount > 1"
        >{{$t('images.create.barRemove')}}
        </button>
        <br>

        <div class="alert alert-warning" role="alert" v-if="tooMuchText">
            {{$t('images.create.tooMuchText')}}
        </div>

        <input
            @input="draw()"
            id="font-size"
            :disabled="tooMuchText"
            :max="fontSizeMax"
            :min="fontSizeMin"
            step="1"
            type="range"
            v-model.number="fontSize"
        >
        <label for="font-size">{{$t('images.create.fontSize')}}</label>
    </div>
</template>

<script>
    const minFontSizeFactor = 0.08; // the correct 175% would be 0.0925

    import {Schemes, Types} from "../../service/canvas/Bar";
    import BarBlock from "../../service/canvas/BarBlock";
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
            }
        },

        props: {
            alignment: {
                required: true,
            },
            colorSchema: {
                required: true,
                validator(value) {
                    return ['white', 'green', 'green-green'].indexOf(value) !== -1;
                }
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
                if ('white' === this.colorSchema) {
                    return Schemes.white;
                } else {
                    return Schemes.green;
                }
            },

            schemaHeadlineSecondary() {
                return Schemes.magenta;
            },

            schemaSubline() {
                if ('green-green' === this.colorSchema) {
                    return Schemes.green;
                } else {
                    return Schemes.white;
                }
            },

            buttonClassSubline() {
                if ('green-green' === this.colorSchema) {
                    return 'btn-secondary';
                } else {
                    return 'btn-outline-secondary';
                }
            },

            buttonClassPrimaryHeadline() {
                if ('white' === this.colorSchema) {
                    return 'btn-outline-secondary';
                } else {
                    return 'btn-secondary';
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

            remove(array, index) {
                this.$delete(array, index);
                this.draw();
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
                const imageToBlockRatio = this.imageWidth / this.block.width;
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

                if (this.block.width > this.imageWidth) {
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

            headlinesSecondaryAdd() {
                const message = this.$t('images.create.headlineSecondaryAdd');

                if (!confirm(message)) {
                    this.headlinesSecondaryCount++;
                }
            }
        },
    }
</script>

<style scoped>

</style>
