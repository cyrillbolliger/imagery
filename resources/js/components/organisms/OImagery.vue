<template>
    <div>
        <canvas
            :style="`width: ${width/2}px; height: ${height/2}px;`"
            class="a-canvas"
            ref="canvas"></canvas>
        <br>
        <MBarBlock
            :alignment="alignment"
            :image-width="width"
            :color-schema="schema"
            @drawn="drawBarBlock($event)"
        ></MBarBlock>
        <br>
        <button @click="alignLeft()">left</button>
        <button @click="alignRight()">right</button>
        <br>
        <button @click="makeGreen()">green</button>
        <button @click="makeWhite()">white</button>
        <button @click="makeGreenGreen()">green-green</button>
        <br>
        <button @click="makeHeadline()">headline</button>
        <button @click="makeSubline()">subline</button>
        <br>
        <button @click="scaleUp()">scaleUp</button>
        <button @click="scaleDown()">scaleDown</button>
    </div>
</template>

<script>
    import {Alignments, Types} from "../../service/canvas/Bar";
    import MBarBlock from "../molecules/MBarBlock";

    export default {
        name: "OImagery",
        components: {MBarBlock},
        data() {
            return {
                canvas: null,
                context: null,
                alignment: Alignments.left,
                type: Types.headline,
                schema: 'green',
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
        },

        methods: {
            drawBarBlock(block) {
                // use the if because it might be called before the context is
                // ready (child component is mounted but parent isn't)
                if (this.context) {
                    this.context.clearRect(0, 0, this.width, this.height);
                    this.context.drawImage(block, 0, 0);
                }
            },

            alignLeft() {
                this.alignment = Alignments.left;
            },

            alignRight() {
                this.alignment = Alignments.right;
            },

            makeGreen() {
                this.schema = 'green';
            },

            makeWhite() {
                this.schema = 'white';
            },

            makeGreenGreen() {
                this.schema = 'green-green';
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
