export default class BackgroundLayer {
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

        this._clear();
        this._drawBackground();
    }

    drag(pos) {
        console.log('dragBackground', pos);
    }

    _setContext() {
        this._context = this._canvas.getContext('2d');
    }

    _clear() {
        this._context.clearRect(0, 0, this._canvas.width, this._canvas.height);
    }

    _drawBackground() {
        if (!this._block) {
            return;
        }

        this._context.drawImage(this._block, 0, 0);
    }
}
