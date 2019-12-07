import Layer from "./Layer";

export default class BackgroundLayer extends Layer {
    constructor(canvas) {
        super(canvas);

        this._y = 0;
        this._x = 0;

        this._mousePos = {
            x: 0,
            y: 0,
        };
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

    center() {
        const dX = this._canvas.width - this._block.width;
        const dY = this._canvas.height - this._block.height;

        this._x = dX / 2;
        this._y = dY / 2;
    }

    _clear() {
        this._context.clearRect(0, 0, this._canvas.width, this._canvas.height);
    }

    _drawBlock() {
        this._clear();
        this._context.drawImage(this._block, this._x, this._y);
    }
}
