import Layer from "./Layer";

export default class BackgroundLayer extends Layer {
    drag(pos) {
        console.log('dragBackground', pos);
    }

    _clear() {
        this._context.clearRect(0, 0, this._canvas.width, this._canvas.height);
    }

    _drawBlock() {
        this._clear();
        this._context.drawImage(this._block, 0, 0);
    }
}
