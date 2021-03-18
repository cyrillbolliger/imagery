<template>
    <div class="o-imagery" ref="container">
        <div class="o-imagery__controls-1">
            <MLogoBlock
                :image-height="height"
                :image-width="width"
                :alignment="alignment"
                :color-schema="schema"
                @drawn="updateLogoLayer($event)"
            />

            <MSizeBlock
                @sizeChanged="setSize($event)"
            />

            <MBackgroundBlock
                :image-height="height"
                :image-width="width"
                @drawn="updateBackgroundLayer($event)"
                @imageChanged="rawImage = $event"
                @typeChanged="backgroundType = $event"
            />
        </div>

        <div class="o-imagery__preview">
            <h3 class="d-block">{{$t('images.create.preview')}}</h3>
            <div class="o-imagery__canvas-zone" ref="canvasZone">
                <canvas
                    :class="canvasClasses"
                    :style="canvasStyleSize"
                    @mousedown.stop="mouseDragStart($event)"
                    @mousemove.stop="mouseMove($event)"
                    @mouseout.stop="mouseLeave($event)"
                    @mouseup.stop="mouseDragStop($event)"
                    @touchcancel.stop="touchDragStop($event)"
                    @touchend.stop="touchDragStop($event)"
                    @touchmove.stop.prevent="touchDragMove($event)"
                    @touchstart.stop="touchDragStart($event)"
                    class="o-imagery__canvas"
                    id="canvas"
                    ref="canvas"
                >You definitely need a newer browser!
                </canvas>
            </div>
            <small v-if="hasImageBackground">{{$t('images.create.dragHelp')}}</small>
            <small v-else>{{$t('images.create.dragHelpBar')}}</small>
        </div>

        <div class="o-imagery__controls-2">
            <MBarBlock
                :alignment="alignment"
                :color-schema="schema"
                :image-height="height"
                :image-width="width"
                @drawn="updateBarLayer($event)"
                @textChanged="keywords = $event"
                @paddingChanged="textPadding = $event"
                class="mt-2"
            />

            <MCopyright
                :color="hasBorder ? colorCopyrightBorder : colorCopyrightNoBorder"
                :image-height="height"
                :image-width="width"
                @drawn="updateCopyrightLayer($event)"
                v-if="hasImageBackground"
            />

            <MAlignment
                v-model="alignment"
            />

            <MColorScheme
                v-if="this.backgroundType !== backgroundTypes.gradient"
                v-model="schema"
            />

            <MBorderBlock
                :image-height="height"
                :image-width="width"
                @drawn="updateBorderLayer($event)"
                @widthChanged="borderWidth = $event"
                @borderSettingChanged="hasBorder = $event"
            />

            <button
                @click="save()"
                class="btn btn-primary mb-3">{{$t('images.create.generate')}}
            </button>
        </div>
    </div>
</template>

<script>
    import {Alignments, BackgroundTypes, ColorSchemes} from "../../service/canvas/Constants";
    import MBarBlock from "../molecules/MBarBlock";
    import BarLayer from "../../service/canvas/layers/BarLayer";
    import BackgroundLayer from "../../service/canvas/layers/BackgroundLayer";
    import MBackgroundBlock from "../molecules/MBackgroundBlock";
    import MBorderBlock from "../molecules/MBorderBlock";
    import BorderLayer from "../../service/canvas/layers/BorderLayer";
    import MLogoBlock from "../molecules/MLogoBlock";
    import LogoLayer from "../../service/canvas/layers/LogoLayer";
    import MSizeBlock from "../molecules/MSizeBlock";
    import MAlignment from "../molecules/MAlignment";
    import MColorScheme from "../molecules/MColorScheme";
    import debounce from 'lodash/debounce';
    import MCopyright from "../molecules/MCopyright";
    import CopyrightLayer from "../../service/canvas/layers/CopyrightLayer";

    export default {
        name: "OImagery",
        components: {
            MCopyright,
            MAlignment,
            MSizeBlock,
            MBorderBlock,
            MBackgroundBlock,
            MBarBlock,
            MColorScheme,
            MLogoBlock,
        },

        data() {
            return {
                canvas: null,
                alignment: Alignments.left,
                schema: ColorSchemes.white,
                width: 1080,
                height: 1080,
                fontSize: 50,
                textPadding: 0,
                backgroundType: BackgroundTypes.gradient,
                backgroundTypes: BackgroundTypes,
                rawImage: null,
                borderWidth: 0,
                colorCopyrightBorder: '#666666',
                colorCopyrightNoBorder: '#ffffff',
                hasBorder: true,
                logoId: null,
                keywords: '',

                viewHeight: document.documentElement.clientHeight,
                viewWidth: document.documentElement.clientWidth,
                canvasZoneLeft: 0,
                canvasPos: {
                    x: 0,
                    y: 0,
                    width: 0,
                    height: 0,
                },

                barBlock: null,
                backgroundBlock: null,
                borderBlock: null,
                logoBlock: null,
                copyrightBlock: null,
                borderLayer: null,
                barLayer: null,
                backgroundLayer: null,
                logoLayer: null,
                copyrightLayer: null,

                dragObj: null,
            }
        },

        computed: {
            canvasClasses() {
                return {
                    'bar-dragging': this.dragObj,
                    'bar-touching': this.barLayer && this.barLayer.touching,
                    'transparent': this.backgroundType === BackgroundTypes.transparent,
                    'image': this.backgroundType === BackgroundTypes.image,
                }
            },

            canvasStyleSize() {
                const paddingX = 30;
                const paddingY = 160;
                const vh = this.viewHeight;
                const vw = this.viewWidth;

                const maxHeight = vw < 768 ? 250 : vh - paddingY;
                const maxWidth = vw < 768 ? vw - paddingX : vw - this.canvasZoneLeft - paddingX;

                const imgHeight = this.height / 2;
                const imgWidth = this.width / 2;

                const hRatio = imgHeight / maxHeight;
                const wRatio = imgWidth / maxWidth;

                let ratio = Math.max(hRatio, wRatio);
                ratio = ratio < 1 ? 1 : ratio;

                const height = imgHeight / ratio;
                const width = imgWidth / ratio;

                return `height: ${height}px; width: ${width}px;`;
            },

            hasImageBackground() {
                return this.backgroundType === BackgroundTypes.image && this.rawImage;
            }
        },

        created() {
            window.addEventListener('resize', this.setViewDims);
            window.addEventListener('resize', this.setCanvasZoneLeft);
            window.addEventListener('resize', this.setCanvasPos);
            window.addEventListener('scroll', this.setCanvasPos);
        },

        mounted() {
            this.canvas = this.$refs.canvas;

            this.initializeCanvasPos();
            this.setCanvasZoneLeft();

            this.$nextTick(() => {
                this.canvas.width = this.width;
                this.canvas.height = this.height;

                this.backgroundLayer = new BackgroundLayer(this.canvas);
                this.borderLayer = new BorderLayer(this.canvas);
                this.barLayer = new BarLayer(this.canvas);
                this.logoLayer = new LogoLayer(this.canvas);
                this.copyrightLayer = new CopyrightLayer(this.canvas);

                this.updateBackgroundLayer(this.backgroundBlock);
                this.updateBorderLayer(this.borderBlock);
                this.updateBarLayer(this.barBlock);
                this.updateLogoLayer(this.logoBlock);
                this.updateCopyrightLayer(this.copyrightBlock);
            });
        },

        destroyed() {
            window.removeEventListener('resize', this.setViewDims);
            window.removeEventListener('resize', this.setCanvasZoneLeft);
            window.removeEventListener('resize', this.setCanvasPos);
            window.removeEventListener('scroll', this.setCanvasPos);
        },

        methods: {
            initializeCanvasPos() {
                // on startup, this is zero. since there is no hook
                // to catch the fully rendered event, we have to retry
                // until the browser has positioned the canvas.
                window.requestAnimationFrame(() => {
                    this.setCanvasPos();
                    if (this.canvasPos.y === 0) {
                        this.initializeCanvasPos();
                    }
                });
            },

            updateBarLayer(barBlock) {
                this.barBlock = barBlock;

                if (!this.barLayer) {
                    return;
                }

                this.barLayer.alignment = this.alignment;
                this.barLayer.block = this.barBlock;
                this.barLayer.borderWidth = this.borderWidth;
                this.barLayer.textPadding = this.textPadding;

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

            updateLogoLayer(data) {
                if (!data) {
                    this.logoBlock = null;
                    this.logoId = null;
                } else {
                    this.logoBlock = data.block;
                    this.logoId = data.id;
                }

                if (!this.logoLayer) {
                    return;
                }

                this.logoLayer.block = this.logoBlock;
                this.draw();
            },

            updateCopyrightLayer(copyrightBlock) {
                this.copyrightBlock = copyrightBlock;

                if (!this.copyrightLayer) {
                    return;
                }

                this.copyrightLayer.block = this.copyrightBlock;
                this.draw();
            },

            draw() {
                this.backgroundLayer.draw();
                this.borderLayer.draw();
                this.barLayer.draw();

                this.logoLayer.alignment = this.alignment;
                this.logoLayer.barPos = this.barLayer.boundingRect;
                this.logoLayer.draw();

                if (BackgroundTypes.image === this.backgroundType) {
                    this.copyrightLayer.alignment = this.alignment;
                    this.copyrightLayer.border = this.hasBorder;
                    this.copyrightLayer.draw();
                }
            },

            setSize(dims) {
                this.width = dims.width;
                this.height = dims.height;
            },

            setCanvasZoneLeft: debounce(function () {
                this.canvasZoneLeft = this.$refs.canvasZone
                    ? this.$refs.canvasZone.getBoundingClientRect().left
                    : 0;
            }, 100),

            setCanvasPos: debounce(function () {
                this.$nextTick(() => {
                    const pos = this.canvas.getBoundingClientRect();
                    this.canvasPos.x = pos.x + window.scrollX;
                    this.canvasPos.y = pos.y + window.scrollY;
                    this.canvasPos.width = pos.width;
                    this.canvasPos.height = pos.height;
                });
            }, 100, {leading: true, trailing: true}),

            setViewDims: debounce(function () {
                this.viewHeight = document.documentElement.clientHeight;
                this.viewWidth = document.documentElement.clientWidth;
            }, 100),

            mouseDragStart() {
                this.dragStart();
            },
            mouseMove(event) {
                this.move(event);
            },
            mouseDragStop(event) {
                this.dragStop();
                this.move(event);
            },
            mouseLeave() {
                this.dragStop();
            },
            touchDragStart(event) {
                const touch = event.touches[0];
                const pos = this.relImagePos(touch.pageX, touch.pageY);
                this.propagateMousePos(pos);

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

                this.propagateMousePos({x: -1, y: -1});

                this.draw();
            },
            move(event) {
                const pos = this.relImagePos(event.pageX, event.pageY);

                if (this.dragObj) {
                    this.dragObj.drag(pos);
                }

                this.propagateMousePos(pos);

                this.draw();
            },
            relImagePos(absX, absY) {
                return {
                    x: (absX - this.canvasPos.x) * this.width / this.canvasPos.width,
                    y: (absY - this.canvasPos.y) * this.height / this.canvasPos.height,
                };
            },
            propagateMousePos(pos) {
                this.backgroundLayer.mousePos = pos;
                this.barLayer.mousePos = pos;
            },

            save() {
                this.$emit('save', {
                    canvas: this.canvas,
                    backgroundType: this.backgroundType,
                    rawImage: this.rawImage,
                    logoId: this.logoId,
                    keywords: this.keywords,
                });
            },
        },

        watch: {
            backgroundType(value) {
                if (BackgroundTypes.gradient === value) {
                    this.schema = ColorSchemes.white;
                } else {
                    this.schema = ColorSchemes.green;
                }

                this.setCanvasPos();
            },
            width(value) {
                this.canvas.width = value;
                this.setCanvasPos();
            },
            height(value) {
                this.canvas.height = value;
                this.setCanvasPos();
            }
        }
    }
</script>

<style lang="scss" scoped>
    .o-imagery {
        @include media-breakpoint-up(lg) {
            width: 550px;
        }

        &__preview {
            background-color: $gray-600;
            padding: 0.25em 0.5em 0.125em;
            color: $white;

            @include media-breakpoint-up(lg) {
                position: fixed;
                top: 82px;
                left: 600px;
                padding: 0.5em 1em 0.25em;
            }

            @include media-breakpoint-up(xl) {
                left: calc(25vw + 600px);
                top: 3.5em;
            }
        }

        &__canvas-zone {
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        &__canvas {
            &.transparent {
                // https://stackoverflow.com/a/35362074
                background-image: linear-gradient(45deg, #d7d7d7 25%, transparent 25%),
                linear-gradient(-45deg, #d7d7d7 25%, transparent 25%),
                linear-gradient(45deg, transparent 75%, #d7d7d7 75%),
                linear-gradient(-45deg, transparent 75%, #d7d7d7 75%);
                background-size: 20px 20px;
                background-position: 0 0, 0 10px, 10px -10px, -10px 0px;
            }

            &.image {
                cursor: grab;
            }

            &.bar-touching {
                cursor: grab;
            }

            &.bar-dragging {
                cursor: grabbing !important;
            }
        }
    }
</style>
