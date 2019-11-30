<template>
    <div>
        <ABar
            :alignment="alignment"
            :base-font-size="fontSize"
            :image-width="imageWidth"
            :key="`headlinePrimary-${n}`"
            :schema="schemaHeadlinePrimary"
            :type="typeHeadline"
            @drawn="update(headlinesPrimary, n, $event)"
            v-for="n in countHeadlinesPrimary"
        ></ABar>
        <ABar
            :alignment="alignment"
            :base-font-size="fontSize"
            :image-width="imageWidth"
            :key="`headlineSecondary-${n}`"
            :schema="schemaHeadlineSecondary"
            :type="typeHeadline"
            @drawn="update(headlinesSecondary, n, $event)"
            v-for="n in countHeadlinesSecondary"
        ></ABar>
        <ABar
            :alignment="alignment"
            :base-font-size="fontSize"
            :image-width="imageWidth"
            :key="`subline-${n}`"
            :schema="schemaSubline"
            :type="typeSubline"
            @drawn="update(sublines, n, $event)"
            v-for="n in countSublines"
        ></ABar>

        <input
            @input="draw()"
            id="font-size"
            max="100"
            min="10"
            type="range"
            v-model.number="fontSize"
        >
        <label for="font-size">{{$t('images.create.fontSize')}}</label>
    </div>
</template>

<script>
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
                countHeadlinesPrimary: 1,
                countHeadlinesSecondary: 1,
                countSublines: 1,
                fontSize: 50,
                typeHeadline: Types.headline,
                typeSubline: Types.subline,
                block: null,
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
            update(array, index, bar) {
                this.$set(array, index, bar);
                this.draw();
            },
            draw() {
                this.block.alignment = this.alignment;

                this.$emit('drawn', this.block.draw());
            },
        },
    }
</script>

<style scoped>

</style>
