import {Alignments} from "./../Constants";
import Layer from "./Layer";
import BorderHelper from "../BorderHelper";

const orthogonal = -Math.PI / 2;

export default class CopyrightLayer extends Layer {
    constructor(canvas) {
        super(canvas);

        this._alignment = Alignments.left;
        this._border = true;

        this._x = 0;
        this._y = 0;
    }

    set border(bool) {
        this._border = bool;
    }

    set alignment(alignment) {
        this._alignment = alignment;
    }

    _drawBlock() {
        // rotate the canvas
        const [x, y] = this._getRotationOrigin();
        this._context.translate(x, y);
        this._context.rotate(orthogonal);

        // place the block
        this._determinePos();
        this._context.drawImage(this._block, this._x, this._y);

        // reset the transformation matrix
        this._context.setTransform(1, 0, 0, 1, 0, 0);
    }

    _getRotationOrigin() {
        const x = this._alignment === Alignments.right ? 0 : this._canvas.width;
        const y = this._canvas.height;

        return [x, y];
    }

    _determinePos() {
        const borderWidth = BorderHelper.width(this._canvas.width, this._canvas.height);
        let borderX, borderY;

        if (this._border) {
            borderX = BorderHelper.radius(borderWidth);
            borderY = 0;
        } else {
            borderX = 0;
            borderY = borderWidth;
        }

        this._x = borderWidth + borderX;

        if (this._alignment === Alignments.right) {
            this._y = borderY;
        } else {
            this._y = -this._block.height - borderY;
        }
    }
}
