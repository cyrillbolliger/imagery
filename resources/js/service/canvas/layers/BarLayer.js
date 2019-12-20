import {BarSizeFactor, Alignments, RotationAngle} from "./../Constants";
import Layer from "./Layer";

const shadowColorMouseOver = 'rgba(0,0,0,0.5)';
const shadowMouseOverSize = 0.01;

const borderMarginFactor = 2;
const borderMarginFactorRadius = 3;

export default class BarLayer extends Layer {
    constructor(canvas) {
        super(canvas);

        this._borderWidth = null;
        this._y = 0;

        this._touching = false;
        this._dragging = true;
        this._mousePos = {
            x: 0,
            y: 0,
        };
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

    get boundingRect() {
        if (! this._hasBlock()) {
            return null;
        }

        return {
            x0: this._getXstart(),
            y0: this._getYstart(),
            x1: this._getXend(),
            y1: this._getYend(),
        };
    }

    drag(pos) {
        const deltaY = pos.y - this._mousePos.y;
        const y = this._y + deltaY;

        const margins = this._getYMargins();
        const topLimit = margins.top;
        const bottomLimit = this._canvas.height - margins.bottom - this._getRotatedVisibleHeight();

        if (y < topLimit) {
            this._y = topLimit;
        } else if (y > bottomLimit) {
            this._y = bottomLimit;
        } else {
            this._y = y;
        }
    }

    _drawBlock() {
        if (! this._hasBlock()) {
            return;
        }

        this._setTouchEffect();

        // move the origin to the desired position, then rotate. apply the
        // image to the origin afterwards. this way the offsets are measured
        // horizontal and vertical respectively (unrotated). reset the origin
        // and rotation afterwards.
        this._context.translate(this._getBlockXpos(), this._getBlockYpos());
        this._context.rotate(RotationAngle);
        this._context.drawImage(this._block, 0, 0);
        this._context.setTransform(1, 0, 0, 1, 0, 0);
        this._context.filter = 'none';
    }

    _hasBlock() {
        return this._block && 0 < this._block.width && 0 < this._block.height;
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

        const limits = this._getYLimits();

        if (y < limits.top) {
            y = limits.top;
        }

        if (y + height > limits.bottom) {
            y = limits.bottom - height;
        }

        return y;
    }

    _getYLimits() {
        const margins = this._getYMargins();

        const top = this._getVisibleHorizontalRotationTriangleHeight() + margins.top;
        const bottom = this._canvas.height + this._getVisibleHorizontalRotationTriangleHeight() - margins.bottom;

        return {top, bottom};
    }

    _getYMargins() {
        const border = borderMarginFactor * this._borderWidth;
        const borderRadius = borderMarginFactorRadius * this._borderWidth;

        let top, bottom;

        if (this._alignment === Alignments.right) {
            top = borderRadius;
            bottom = border;
        } else {
            top = border;
            bottom = borderRadius;
        }

        return {top, bottom};
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

        const posXstart = this._getXstart();
        const posXend = this._getXend();

        const posYstart = this._getYstart();
        const posYend = this._getYend();

        const xTouch = mouseX >= posXstart && mouseX <= posXend;
        const yTouch = mouseY >= posYstart && mouseY <= posYend;

        return xTouch && yTouch;
    }

    _getXstart() {
        return this._getBlockXpos();
    }

    _getXend() {
        return this._getBlockXpos() + this._getRotatedFullWidth();
    }

    _getYstart() {
        return this._alignment === Alignments.left ?
            this._getBlockYpos() - this._getFullHorizontalRotationTriangleHeight() :
            this._getBlockYpos() - this._getVisibleHorizontalRotationTriangleHeight();
    }

    _getYend() {
        return this._getYstart() + this._getRotatedVisibleHeight();
    }

    _getRotatedVisibleHeight() {
        return this._block.height + Math.sin(-RotationAngle) * this._getVisibleBlockWidth();
    }

    _getRotatedFullWidth() {
        return this._block.width + Math.sin(-RotationAngle) * this._block.height;
    }
}
