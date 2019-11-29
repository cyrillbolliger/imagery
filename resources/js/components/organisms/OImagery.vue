<template>
    <div>
        <canvas
            :style="`width: ${width/2}px; height: ${height/2}px;`"
            class="a-canvas"
            ref="canvas"></canvas>
        <br>
        <ABar
            :alignment="alignment"
            :font-size="fontSize"
            :image-width="width"
            :schema="schema"
            :type="type"
            @drawn="drawBar($event)"
        ></ABar>
        <br>
        <button @click="alignLeft()">left</button>
        <button @click="alignRight()">right</button>
        <br>
        <button @click="makeGreen()">green</button>
        <button @click="makeMagenta()">magenta</button>
        <button @click="makeWhite()">white</button>
        <br>
        <button @click="makeHeadline()">headline</button>
        <button @click="makeSubline()">subline</button>
        <br>
        <button @click="scaleUp()">scaleUp</button>
        <button @click="scaleDown()">scaleDown</button>
    </div>
</template>

<script>
    import {Schemes, Alignments, Types} from "../../service/canvas/Bar";
    import ABar from "../atoms/ABar";

    export default {
        name: "OImagery",
        components: {ABar},
        data() {
            return {
                canvas: null,
                context: null,
                alignment: Alignments.left,
                type: Types.headline,
                schema: Schemes.green,
                width: 800,
                height: 800,
                fontSize: 50,
            }
        },

        mounted() {
            this.canvas = this.$refs.canvas;
            this.context = this.canvas.getContext('2d');
            this.canvas.width = this.width;
            this.canvas.height = this.height;

            // this ensures the bar will get drawn on load
            this.fontSize = 60;
        },

        methods: {
            drawBar(bar) {
                // use the if because it might be called before the context is
                // ready (child component is mounted but parent isn't)
                if (this.context) {
                    this.context.drawImage(bar, 0, 0);
                }
            },

            alignLeft() {
                this.alignment = Alignments.left;
            },

            alignRight() {
                this.alignment = Alignments.right;
            },

            makeGreen() {
                this.schema = Schemes.green;
            },

            makeMagenta() {
                this.schema = Schemes.magenta;
            },

            makeWhite() {
                this.schema = Schemes.white;
            },

            makeHeadline() {
                this.type = Types.headline;
            },

            makeSubline() {
                this.type = Types.subline;
            },

            scaleUp() {
                this.width *= 2;
                this.height *= 2;
                this.fontSize *= 2;
            },

            scaleDown() {
                this.width /= 2;
                this.height /= 2;
                this.fontSize /= 2;
            },
        },
    }
</script>

<style lang="scss" scoped>
    .a-canvas {
        border: 1px solid red;
    }
</style>
