<template>
    <div>
        <canvas
            :style="`width: ${width/2}px; height: ${height/2}px;`"
            class="a-canvas"
            @mousedown.stop="mouseDragStart($event)"
            @mousemove.stop="mouseMove($event)"
            @mouseup.stop="mouseDragStop($event)"
            @touchcancel.stop="touchDragStop($event)"
            @touchend.stop="touchDragStop($event)"
            @touchmove.stop="touchDragMove($event)"
            @touchstart.stop="touchDragStart($event)"
            ref="canvas"
        ></canvas>
        <br>
        <MBarBlock
            :alignment="alignment"
            :image-width="width"
            :image-height="height"
            :color-schema="schema"
            @drawn="updateBarLayer($event)"
        ></MBarBlock>
        <br>
        <MBackgroundBlock
            :image-height="height"
            :image-width="width"
            @drawn="updateBackgroundLayer($event)"
            @typeChanged="backgroundType = $event"
        ></MBackgroundBlock>

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
    import {Alignments, BackgroundTypes} from "../../service/canvas/Constants";
    import MBarBlock from "../molecules/MBarBlock";
    import BarLayer from "../../service/canvas/BarLayer";
    import BackgroundLayer from "../../service/canvas/BackgroundLayer";
    import MBackgroundBlock from "../molecules/MBackgroundBlock";

    export default {
        name: "OImagery",
        components: {MBackgroundBlock, MBarBlock},
        data() {
            return {
                canvas: null,
                alignment: Alignments.left,
                schema: 'white',
                width: 800,
                height: 800,
                fontSize: 50,
                backgroundType: null,
                canvasPos: {
                    x: 0,
                    y: 0,
                    width: 0,
                    height: 0,
                },

                barBlock: null,
                backgroundBlock: null,
                barLayer: null,
                backgroundLayer: null,

                dragObj: null,
            }
        },

        mounted() {
            this.canvas = this.$refs.canvas;

            this.setCanvasPos();
            window.addEventListener('resize', this.setCanvasPos());

            this.$nextTick(() => {
                this.canvas.width = this.width;
                this.canvas.height = this.height;

                this.backgroundLayer = new BackgroundLayer(this.canvas);
                this.barLayer = new BarLayer(this.canvas);

                this.updateBackgroundLayer(this.backgroundBlock);
                this.updateBarLayer(this.barBlock);
            });
        },

        destroyed() {
            window.removeEventListener('resize', this.setCanvasPos());
        },

        methods: {
            updateBarLayer(barBlock) {
                this.barBlock = barBlock;

                if (!this.barLayer) {
                    return;
                }

                this.barLayer.alignment = this.alignment;
                this.barLayer.block = this.barBlock;
                this.draw();
            },

            updateBackgroundLayer(backgroundBlock) {
                this.backgroundBlock = backgroundBlock;

                if (!this.backgroundLayer) {
                    return;
                }

                this.backgroundLayer.block = this.backgroundBlock;
                this.draw();
            },

            draw() {
                this.backgroundLayer.draw();
                this.barLayer.draw();
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

            setCanvasPos() {
                const pos = this.canvas.getBoundingClientRect();
                this.canvasPos.x = pos.x;
                this.canvasPos.y = pos.y;
                this.canvasPos.width = pos.width;
                this.canvasPos.height = pos.height;
            },

            mouseDragStart(event) {
                console.log(event);
            },
            mouseMove(event) {
                const pos = {
                    x: (event.clientX - this.canvasPos.x) * this.width / this.canvasPos.width,
                    y: (event.clientY - this.canvasPos.y) * this.height / this.canvasPos.height,
                };

                if (this.dragObj) {
                    // todo: notify drag object
                } else {
                    this.backgroundLayer.mousePos = pos;
                    this.barLayer.mousePos = pos;
                }

                this.draw();
            },
            mouseDragStop(event) {
                console.log(event);
            },
            touchDragStart(event) {
                console.log(event);
            },
            touchDragMove(event) {
                console.log(event);
            },
            touchDragStop(event) {
                console.log(event);
            },
        },

        watch: {
            backgroundType(value) {
                if (BackgroundTypes.gradient === value) {
                    this.makeWhite();
                } else {
                    this.makeGreen();
                }
            },
        }
    }
</script>

<style lang="scss" scoped>
    .a-canvas {
        border: 1px solid black;
    }
</style>
