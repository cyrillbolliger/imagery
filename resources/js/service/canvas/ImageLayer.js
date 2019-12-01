export default class ImageLayer {
    constructor(canvas) {
        this._canvas = canvas;

        this._block = null;
        this._context = null; // deferred loading because we have to create this
                              // object before the canvas in the dom is ready
    }

    set width(width) {
        this._canvas.width = width;
    }

    set height(height) {
        this._canvas.height = height;
    }

    set block(block) {
        this._block = block;
    }

    draw() {
        this._setContext();

        this._context.fillStyle = 'red';
        this._context.fillRect(0, 0, this._canvas.width, this._canvas.height);

        return this._canvas;
    }

    _setContext() {
        this._context = this._canvas.getContext('2d');
    }
}
