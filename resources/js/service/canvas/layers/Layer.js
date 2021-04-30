export default class Layer {
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

        if (!this._block) {
            return;
        }

        this._drawBlock();
    }

    _setContext() {
        if (!this._context) {
            this._context = this._canvas.getContext('2d');
            this._context.imageSmoothingEnabled = true;
        }
    }
}
