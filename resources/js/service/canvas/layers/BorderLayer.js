import Layer from "./Layer";

export default class BorderLayer extends Layer {
    _drawBlock() {
        this._context.drawImage(this._block, 0, 0);
    }
}
