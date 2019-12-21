import {Alignments, RotationAngle} from "./../Constants";
import Layer from "./Layer";

const marginFactor = 0.2;

export default class LogoLayer extends Layer {
    constructor(canvas) {
        super(canvas);

        this._alignment = Alignments.left;
        this._barPos = {
            x0: 0,
            y0: 0,
            x1: 0,
            y1: 0,
        };

        this._x = 0;
        this._y = 0;
        this._margin = 0;
    }

    set barPos(pos) {
        this._barPos = pos;
    }

    set alignment(alignment) {
        this._alignment = alignment;
    }

    _drawBlock() {
        // the position, of the unrotated upper left corner
        this._setPos();

        const halfWidth = this._block.width / 2;
        const halfHeight = this._block.height / 2;

        // the center of the logo
        const x = this._x + halfWidth;
        const y = this._y + halfHeight;

        // rotate on the center of the logo dest
        this._context.translate(x, y);
        this._context.rotate(RotationAngle);

        // place the logo so the logos center hits the given spot
        this._context.drawImage(this._block, -halfWidth, -halfHeight);

        // reset the transformation matrix
        this._context.setTransform(1, 0, 0, 1, 0, 0);
    }

    _setPos() {
        this._setMargin();

        if (!this._barPos) {
            this._x = this._margin;
            this._y = this._margin;
            return;
        }

        let pos = this._determinePos(true);

        if (this._intersects(pos, this._barPos)) {
            pos = this._determinePos(false);
        }

        this._x = pos.x0;
        this._y = pos.y0;
    }

    _setMargin() {
        this._margin = this._block.width * marginFactor;
    }

    _determinePos(top = true) {
        const x0 = this._determineX0();
        const y0 = this._determineY0(top);
        const x1 = x0 + this._block.width;
        const y1 = y0 + this._block.height;

        return {x0, y0, x1, y1};
    }

    _determineX0() {
        if (this._alignment === Alignments.left) {
            return this._canvas.width - this._block.width - this._margin;
        } else {
            return this._margin;
        }
    }

    _determineY0(top) {
        return top
            ? this._margin
            : this._canvas.height - this._block.height - this._margin;
    }


    _intersects(a, b) {
        // inspired by https://stackoverflow.com/a/16012490
        const aLeftOfB = a.x1 < b.x0;
        const aRightOfB = a.x0 > b.x1;
        const aAboveB = a.y0 > b.y1;
        const aBelowB = a.y1 < b.y0;

        return !(aLeftOfB || aRightOfB || aAboveB || aBelowB);
    }
}
