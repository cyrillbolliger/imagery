<template>
    <div>
        <canvas
            :style="`width: ${width/2}px; height: ${height/2}px;`"
            class="a-canvas"
            ref="canvas"></canvas>
        <br>
        <input type="text" v-model="text">
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
    import {Bar, Schemes, Alignments, Types} from "../../service/canvas/Bar";
    import FontFaceObserver from "fontfaceobserver";

    export default {
        name: "ACanvas",
        data() {
            return {
                canvas: null,
                context: null,
                alignment: null,
                bar: null,
                type: Types.headline,
                schema: Schemes.green,
                text: 'hello',
                width: 800,
                height: 800,
                fontSize: 50,
            }
        },

        mounted() {
            this.canvas = this.$refs.canvas;
            this.context = this.canvas.getContext('2d');
            this.redraw();

            this.loadFonts().then(() => this.redraw());
        },

        methods: {
            redraw() {
                this.canvas.width = this.width;
                this.canvas.height = this.height;

                this.clear();

                this.bar = new Bar(
                    this.context,
                    this.schema,
                    this.alignment,
                    this.type
                );

                this.bar.fontSize = this.fontSize;
                this.bar.text = this.text;
            },

            clear() {
                this.context.fillStyle = 'black';
                this.context.fillRect(0, 0, this.canvas.width, this.canvas.height);
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

            loadFonts() {
                const fat = new FontFaceObserver('SanukFat');
                const bold = new FontFaceObserver('SanukBold');

                return Promise.all([fat.load(), bold.load()]);
            },
        },

        watch: {
            alignment() {
                this.redraw();
            },
            schema() {
                this.redraw();
            },
            text() {
                this.clear();
                this.bar.text = this.text;
            },
            type() {
                this.redraw();
            },
            width() {
                this.redraw();
            },
            height() {
                this.redraw();
            }
        }
    }
</script>

<style lang="scss" scoped>
    .a-canvas {
        border: 1px solid red;
    }
</style>
