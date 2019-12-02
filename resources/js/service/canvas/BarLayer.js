import {BarSizeFactor, Alignments, RotationAngle} from "./Constants";

const shadowColorMouseOver = 'rgba(0,0,0,0.5)';
const shadowMouseOverSize = 0.01;

export default class BarLayer {
    constructor(canvas) {
        this._canvas = canvas;

        this._block = null;
        this._context = null; // deferred loading because we have to create this
                              // object before the canvas in the dom is ready

        this._y = 0;

        this._touching = false;
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

        if (this._isHover() && !this._touching) {
            this._touching = true;
            this._canvas.classList.add('bar-touching');
        }

        if (!this._isHover() && this._touching) {
            this._touching = false;
            this._canvas.classList.remove('bar-touching');
        }
    }

    get touching() {
        return this._touching;
    }

    draw() {
        this._setContext();

        this._drawBlock();

        return this._canvas;
    }

    drag(pos) {
        const deltaY = pos.y - this._mousePos.y;
        const y = this._y + deltaY;

        if (this._inBounds(y)) {
            this._y = y;
        }
    }

    _setContext() {
        this._context = this._canvas.getContext('2d');
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
        this._context.translate(this._getBlockXpos(), this._y + this._getBlockYpos());
        this._context.rotate(RotationAngle);
        this._context.drawImage(this._block, 0, 0);
        this._context.setTransform(1, 0, 0, 1, 0, 0);
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
        // todo: add border with
        // 2*border width if the border is shown

        if (this._alignment === Alignments.left) {
            return this._canvas.width * BarSizeFactor;
        }

        const rotationCorr = Math.sin(RotationAngle) * this._block.height;
        return this._canvas.width * BarSizeFactor + rotationCorr;
    }

    _getBlockYpos() {
        if (this._alignment === Alignments.left) {
            return -Math.sin(RotationAngle) * this._block.width;
        }

        return -Math.tan(RotationAngle) * this._getVisibleBlockWidth();
    }

    _getVisibleBlockWidth() {
        return this._block.width - this._getBlockOversize();
    }

    _isHover() {
        const mouseX = this._mousePos.x;
        const mouseY = this._mousePos.y;
        const posX = this._getBlockXpos();
        const posY = this._y;

        const xTouch = mouseX >= posX && mouseX <= posX + this._getRotatedVisibleWidth();
        const yTouch = mouseY >= posY && mouseY <= posY + this._getRotatedVisibleHeight();

        return xTouch && yTouch;
    }

    _getRotatedVisibleHeight() {
        return this._block.height + (-Math.sin(RotationAngle)) * this._getVisibleBlockWidth();
    }

    _getRotatedVisibleWidth() {
        return this._block.width + (-Math.sin(RotationAngle)) * this._block.height;
    }

    _inBounds(y) {
        const belowUpperBound = y > 0;
        const aboveLowerBound = y + this._getRotatedVisibleHeight() < this._canvas.height;

        return belowUpperBound && aboveLowerBound;
    }
}
