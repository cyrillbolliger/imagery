<template>
    <div>
        <canvas
            :style="`width: ${width/2}px; height: ${height/2}px;`"
            class="a-canvas"
            :class="{'bar-dragging': dragObj, 'bar-touching': barLayer && barLayer.touching}"
            @mousedown.stop="mouseDragStart($event)"
            @mousemove.stop="mouseMove($event)"
            @mouseup.stop="mouseDragStop($event)"
            @mouseout.stop="mouseLeave($event)"
            @touchcancel.stop="touchDragStop($event)"
            @touchend.stop="touchDragStop($event)"
            @touchmove.stop.prevent="touchDragMove($event)"
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
            @imageChanged="rawImage = $event"
        ></MBackgroundBlock>
        <br>
        <MBorderBlock
            :image-height="height"
            :image-width="width"
            @drawn="updateBorderLayer($event)"
            @widthChanged="borderWidth = $event"
        ></MBorderBlock>

        <br>
        <button @click="scaleUp()">scaleUp</button>
        <button @click="scaleDown()">scaleDown</button>

        <br>
        <button @click="alignLeft()">left</button>
        <button @click="alignRight()">right</button>
        <br>
        <button @click="makeGreen()">green</button>
        <button @click="makeWhite()">white</button>
        <button @click="makeGreenGreen()">green-green</button>
        <br>

        <button @click="save()">Save</button>
    </div>
</template>

<script>
    import {Alignments, BackgroundTypes} from "../../service/canvas/Constants";
    import MBarBlock from "../molecules/MBarBlock";
    import BarLayer from "../../service/canvas/layers/BarLayer";
    import BackgroundLayer from "../../service/canvas/layers/BackgroundLayer";
    import MBackgroundBlock from "../molecules/MBackgroundBlock";
    import MBorderBlock from "../molecules/MBorderBlock";
    import BorderLayer from "../../service/canvas/layers/BorderLayer";

    export default {
        name: "OImagery",
        components: {MBorderBlock, MBackgroundBlock, MBarBlock},
        data() {
            return {
                canvas: null,
                alignment: Alignments.left,
                schema: 'white',
                width: 800,
                height: 800,
                fontSize: 50,
                backgroundType: BackgroundTypes.gradient,
                rawImage: null,
                borderWidth: 0,
                canvasPos: {
                    x: 0,
                    y: 0,
                    width: 0,
                    height: 0,
                },

                barBlock: null,
                backgroundBlock: null,
                borderBlock: null,
                borderLayer: null,
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
                this.borderLayer = new BorderLayer(this.canvas);
                this.barLayer = new BarLayer(this.canvas);

                this.updateBackgroundLayer(this.backgroundBlock);
                this.updateBorderLayer(this.borderBlock);
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
                this.barLayer.borderWidth = this.borderWidth;
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

            updateBorderLayer(borderBlock) {
                this.borderBlock = borderBlock;

                if (!this.borderLayer) {
                    return;
                }

                this.borderLayer.block = this.borderBlock;
                this.draw();
            },

            draw() {
                this.backgroundLayer.draw();
                this.borderLayer.draw();
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
                this.canvasPos.x = pos.x + window.scrollX;
                this.canvasPos.y = pos.y + window.scrollY;
                this.canvasPos.width = pos.width;
                this.canvasPos.height = pos.height;
            },

            mouseDragStart() {
                this.dragStart();
            },
            mouseMove(event) {
                this.move(event);
            },
            mouseDragStop() {
                this.dragStop();
            },
            mouseLeave() {
                this.dragStop();
                this.move({pageX: -1, pageY: -1});
            },
            touchDragStart() {
                this.dragStart();
            },
            touchDragMove(event) {
                this.move(event.touches[0]);
            },
            touchDragStop() {
                this.dragStop();
            },

            dragStart() {
                if (this.barLayer.touching) {
                    this.dragObj = this.barLayer;
                    this.dragObj.dragging = true;
                } else if (this.backgroundType === BackgroundTypes.image) {
                    this.dragObj = this.backgroundLayer;
                    this.dragObj.dragging = true;
                }
            },
            dragStop() {
                if (this.dragObj) {
                    this.dragObj.dragging = false;
                    this.dragObj = null;
                }
            },
            move(event) {
                const pos = {
                    x: (event.pageX - this.canvasPos.x) * this.width / this.canvasPos.width,
                    y: (event.pageY - this.canvasPos.y) * this.height / this.canvasPos.height,
                };

                if (this.dragObj) {
                    this.dragObj.drag(pos);
                }

                this.backgroundLayer.mousePos = pos;
                this.barLayer.mousePos = pos;

                this.draw();
            },

            save() {
                this.$emit('save', {
                    canvas: this.canvas,
                    backgroundType: this.backgroundType,
                    rawImage: this.rawImage,
                    logoId: null, // todo
                });
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
            width(value) {
                this.canvas.width = value;
                this.$nextTick(() => this.setCanvasPos());
            },
            height(value) {
                this.canvas.height = value;
                this.$nextTick(() => this.setCanvasPos());
            }
        }
    }
</script>

<style lang="scss" scoped>
    .a-canvas {
        border: 1px solid black;

        // https://stackoverflow.com/a/35362074
        background-image: linear-gradient(45deg, #d7d7d7 25%, transparent 25%),
        linear-gradient(-45deg, #d7d7d7 25%, transparent 25%),
        linear-gradient(45deg, transparent 75%, #d7d7d7 75%),
        linear-gradient(-45deg, transparent 75%, #d7d7d7 75%);
        background-size: 20px 20px;
        background-position: 0 0, 0 10px, 10px -10px, -10px 0px;

        &.bar-touching {
            cursor: grab;
        }

        &.bar-dragging {
            cursor: grabbing !important;
        }
    }
</style>
