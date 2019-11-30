import {barSizeFactor, Alignments} from "./Bar";

export default class Image {
    constructor(canvas) {
        this._canvas = canvas;

        this._barBlock = null;
        this._context = null; // deferred loading because we have to create this
                              // object before the canvas in the dom is ready
    }

    set width(width) {
        this._canvas.width = width;
    }

    set height(height) {
        this._canvas.height = height;
    }

    set barBlock(block) {
        this._barBlock = block;
    }

    set alignment(alignment) {
        this._alignment = alignment;
    }

    draw() {
        this._setContext();

        this._clear();
        this._drawBarBlock();

        return this._canvas;
    }

    _setContext() {
        this._context = this._canvas.getContext('2d');
    }

    _clear() {
        this._context.clearRect(0, 0, this._canvas.width, this._canvas.height);
    }

    _drawBarBlock() {
        if (!this._barBlock) {
            return;
        }

        //this._context.rotate(-5 * Math.PI / 180);
        this._context.drawImage(this._barBlock, this._getBlockXpos(), 0);
        //this._context.rotate(5 * Math.PI / 180);
    }

    _getBlockXpos() {
        // todo: add border with
        // 2*border width if the border is shown

        const oversize = this._canvas.width * barSizeFactor;

        if (this._alignment === Alignments.left) {
            return -oversize;
        }

        return this._canvas.width - this._barBlock.width + oversize;
    }
}
