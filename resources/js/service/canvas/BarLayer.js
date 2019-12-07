import {BarSizeFactor, Alignments, RotationAngle} from "./Constants";

const shadowColorMouseOver = 'rgba(0,0,0,0.5)';
const shadowMouseOverSize = 0.01;

const borderMarginFactor = 2;
const borderMarginFactorRadius = 3;

export default class BarLayer {
    constructor(canvas) {
        this._canvas = canvas;

        this._borderWidth = null;
        this._block = null;
        this._context = null; // deferred loading because we have to create this
                              // object before the canvas in the dom is ready

        this._y = 0;

        this._touching = false;
        this._dragging = true;
        this._mousePos = {
            x: 0,
            y: 0,
        };
    }

    set block(block) {
        this._block = block;
    }

    set alignment(alignment) {
        this._alignment = alignment;
    }

    set mousePos(mousePos) {
        this._mousePos = mousePos;
        this._touching = this._isHover();
    }

    set dragging(value) {
        this._dragging = value;
    }

    set borderWidth(value) {
        this._borderWidth = value;
    }

    get touching() {
        return this._touching;
    }

    draw() {
        this._setContext();
        this._drawBlock();
    }

    drag(pos) {
        const deltaY = pos.y - this._mousePos.y;
        this._y += deltaY;
    }

    _setContext() {
        if (!this._context) {
            this._context = this._canvas.getContext('2d');
        }
    }

    _drawBlock() {
        if (!this._block) {
            return;
        }

        this._setTouchEffect();

        // move the origin to the desired position, then rotate. apply the
        // image to the origin afterwards. this way we the offsets are measured
        // horizontal and vertical respectively (unrotated). reset the origin
        // and rotation afterwards.
        this._context.translate(this._getBlockXpos(), this._getBlockYpos());
        this._context.rotate(RotationAngle);
        this._context.drawImage(this._block, 0, 0);
        this._context.setTransform(1, 0, 0, 1, 0, 0);
        this._context.filter = 'none';
    }

    _setTouchEffect() {
        if (this._touching) {
            const shadowSize = Math.sqrt(this._canvas.width * this._canvas.height) * shadowMouseOverSize;

            this._context.filter = `drop-shadow(0 0 ${shadowSize}px ${shadowColorMouseOver})`;
        } else {
            this._context.filter = 'none';
        }
    }

    _getBlockXpos() {
        if (this._alignment === Alignments.left) {
            return -this._getBlockOversize();
        }

        return this._canvas.width - this._block.width + this._getBlockOversize();
    }

    _getBlockOversize() {
        const border = this._borderWidth;

        if (this._alignment === Alignments.left) {
            return this._canvas.width * BarSizeFactor - border;
        }

        const rotationCorr = Math.sin(RotationAngle) * this._block.height;
        return this._canvas.width * BarSizeFactor + rotationCorr - border;
    }

    _getBlockYpos() {
        let y = this._y + this._getBlockYposUnadjusted();
        const height = this._getRotatedVisibleHeight();

        const border = borderMarginFactor * this._borderWidth;
        const borderRadius = borderMarginFactorRadius * this._borderWidth;
        let topLimit = this._getFullHorizontalRotationTriangleHeight() + border;
        let bottomLimit = this._canvas.height + this._getFullHorizontalRotationTriangleHeight() - borderRadius;

        if (this._alignment === Alignments.right) {
            topLimit = this._getVisibleHorizontalRotationTriangleHeight() + borderRadius;
            bottomLimit = this._canvas.height + this._getVisibleHorizontalRotationTriangleHeight() - border;
        }

        if (y < topLimit) {
            y = topLimit;
        }

        if (y + height > bottomLimit) {
            y = bottomLimit - height;
        }

        return y;
    }

    _getBlockYposUnadjusted() {
        if (this._alignment === Alignments.left) {
            return this._getFullHorizontalRotationTriangleHeight();
        }

        return this._getVisibleHorizontalRotationTriangleHeight();
    }

    _getVisibleHorizontalRotationTriangleHeight() {
        return Math.tan(-RotationAngle) * this._getVisibleBlockWidth();
    }

    _getFullHorizontalRotationTriangleHeight() {
        return Math.sin(-RotationAngle) * this._block.width;
    }

    _getVisibleBlockWidth() {
        return this._block.width - this._getBlockOversize();
    }

    _isHover() {
        const mouseX = this._mousePos.x;
        const mouseY = this._mousePos.y;
        const posXstart = this._getBlockXpos();
        const posXend = posXstart + this._getRotatedFullWidth();

        const posYstart = this._alignment === Alignments.left ?
            this._getBlockYpos() - this._getFullHorizontalRotationTriangleHeight() :
            this._getBlockYpos() - this._getVisibleHorizontalRotationTriangleHeight();
        const posYend = posYstart + this._getRotatedVisibleHeight();

        const xTouch = mouseX >= posXstart && mouseX <= posXend;
        const yTouch = mouseY >= posYstart && mouseY <= posYend;

        return xTouch && yTouch;
    }

    _getRotatedVisibleHeight() {
        return this._block.height + Math.sin(-RotationAngle) * this._getVisibleBlockWidth();
    }

    _getRotatedFullWidth() {
        return this._block.width + Math.sin(-RotationAngle) * this._block.height;
    }
}
