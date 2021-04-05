import {Alignments} from "./../Constants";
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
        this._setPos();
        this._context.drawImage(this._block, this._x, this._y);
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
