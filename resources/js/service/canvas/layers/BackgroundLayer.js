import Layer from "./Layer";

export default class BackgroundLayer extends Layer {
    constructor(canvas) {
        super(canvas);

        this._y = 0;
        this._x = 0;

        this._lastWidth = 0;
        this._lastHeight = 0;

        this._mousePos = {
            x: 0,
            y: 0,
        };
    }

    set block(block) {
        if (!this._block) {
            this._lastWidth = block.width;
            this._lastHeight = block.height;
        }

        this._block = block;
    }

    set mousePos(mousePos) {
        this._mousePos = mousePos;
    }

    drag(pos) {
        const deltaX = pos.x - this._mousePos.x;
        const deltaY = pos.y - this._mousePos.y;

        this._x += deltaX;
        this._y += deltaY;
    }

    _clear() {
        this._context.clearRect(0, 0, this._canvas.width, this._canvas.height);
    }

    _drawBlock() {
        this._clear();

        if (this._lastWidth !== this._block.width) {
            this._setZoomPosition();
        }

        this._moveIntoCanvas();

        this._context.drawImage(this._block, this._x, this._y);
    }

    _setZoomPosition() {
        const srcOldW = this._lastWidth;
        const srcOldH = this._lastHeight;
        const srcNewW = this._block.width;
        const srcNewH = this._block.height;

        const dstW = this._canvas.width;
        const dstH = this._canvas.height;

        const visibleCenterX = -this._x + dstW / 2;
        const visibleCenterY = -this._y + dstH / 2;

        const factorX = visibleCenterX / srcOldW;
        const factorY = visibleCenterY / srcOldH;

        const deltaX = (srcNewW - srcOldW) * factorX;
        const deltaY = (srcNewH - srcOldH) * factorY;

        this._x = this._x - deltaX;
        this._y = this._y - deltaY;

        this._lastWidth = srcNewW;
        this._lastHeight = srcNewH;
    }

    _moveIntoCanvas() {
        const dstW = this._canvas.width;
        const dstH = this._canvas.height;

        const srcW = this._block.width;
        const srcH = this._block.height;

        const lowerX = 0;
        const upperX = dstW - srcW;

        const lowerY = 0;
        const upperY = dstH - srcH;

        if (this._x > lowerX) this._x = lowerX;
        if (this._x < upperX) this._x = upperX;

        if (this._y > lowerY) this._y = lowerY;
        if (this._y < upperY) this._y = upperY;
    }
}
