import {barSizeFactor, Alignments} from "./Bar";

const rotationAngle = -0.0872664626; // 5 degrees ccw in radians cw

export default class BarLayer {
    constructor(canvas) {
        this._canvas = canvas;

        this._block = null;
        this._context = null; // deferred loading because we have to create this
                              // object before the canvas in the dom is ready
    }

    set block(block) {
        this._block = block;
    }

    set alignment(alignment) {
        this._alignment = alignment;
    }

    draw() {
        this._setContext();

        this._drawBlock();

        return this._canvas;
    }

    _setContext() {
        this._context = this._canvas.getContext('2d');
    }

    _drawBlock() {
        if (!this._block) {
            return;
        }

        // move the origin to the desired position, then rotate. apply the
        // image to the origin afterwards. this way we the offsets are measured
        // horizontal and vertical respectively (unrotated). reset the origin
        // and rotation afterwards.
        this._context.translate(this._getBlockXpos(), this._getBlockYpos());
        this._context.rotate(rotationAngle);
        this._context.drawImage(this._block, 0, 0);
        this._context.setTransform(1, 0, 0, 1, 0, 0);
    }

    _getBlockXpos() {
        if (this._alignment === Alignments.left) {
            return -this._getBlockOversize();
        }

        return this._canvas.width - this._block.width + this._getBlockOversize();
    }

    _getBlockOversize() {
        // todo: add border with
        // 2*border width if the border is shown

        if (this._alignment === Alignments.left) {
            return this._canvas.width * barSizeFactor;
        }

        const rotationCorr = Math.sin(rotationAngle) * this._block.height;
        return this._canvas.width * barSizeFactor + rotationCorr;
    }

    _getBlockYpos() {
        if (this._alignment === Alignments.left) {
            return -Math.sin(rotationAngle) * this._block.width;
        }

        const visibleBlockWidth = this._block.width - this._getBlockOversize();

        return -Math.tan(rotationAngle) * visibleBlockWidth;
    }
}
