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
            :image-height="height"
            :color-schema="schema"
            @drawn="updateBarBlock($event)"
        ></MBarBlock>
        <br>
        <button @click="alignLeft()">left</button>
        <button @click="alignRight()">right</button>
        <br>
        <button @click="makeGreen()">green</button>
        <button @click="makeWhite()">white</button>
        <button @click="makeGreenGreen()">green-green</button>
        <br>
        <button @click="scaleUp()">scaleUp</button>
        <button @click="scaleDown()">scaleDown</button>
    </div>
</template>

<script>
    import {Alignments} from "../../service/canvas/Bar";
    import MBarBlock from "../molecules/MBarBlock";
    import Image from "../../service/canvas/Image";

    export default {
        name: "OImagery",
        components: {MBarBlock},
        data() {
            return {
                alignment: Alignments.left,
                schema: 'green',
                width: 800,
                height: 800,
                fontSize: 50,
                barBlock: null,
                image: null
            }
        },

        mounted() {
            this.image = new Image(this.$refs.canvas);
            this.draw();
        },

        methods: {
            updateBarBlock(barBlock) {
                this.image.width = this.width;
                this.image.height = this.height;
                this.image.alignment = this.alignment;
                this.image.barBlock = barBlock;
                this.draw();
            },

            draw() {
                this.image.draw();
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
