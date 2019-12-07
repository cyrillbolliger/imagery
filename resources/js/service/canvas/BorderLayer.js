export default class BorderLayer {
    constructor(canvas) {
        this._canvas = canvas;

        this._block = null;
        this._context = null; // deferred loading because we have to create this
                              // object before the canvas in the dom is ready
    }

    set block(block) {
        this._block = block;
    }

    draw() {
        this._setContext();
        this._drawBorder();
    }

    _setContext() {
        this._context = this._canvas.getContext('2d');
    }

    _drawBorder() {
        if (!this._block) {
            return;
        }

        this._context.drawImage(this._block, 0, 0);
    }
}
