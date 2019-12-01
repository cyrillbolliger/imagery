import {Alignments} from "./Constants";

export default class BarBlock {
    constructor(primary, secondary, sublines) {
        this._canvas = document.createElement('canvas');
        this._context = this._canvas.getContext('2d');

        this._primary = primary;
        this._secondary = secondary;
        this._sublines = sublines;

        this._bars = [];
    }

    set alignment(alignment) {
        this._alignment = alignment;
    }

    get width() {
        return this._canvas.width;
    }

    draw() {
        this._bars = this._primary
            .concat(this._secondary)
            .concat(this._sublines);

        this._setWidth();
        this._setHeight();
        this._clear();
        this._drawBars();

        return this._canvas;
    }

    _setWidth() {
        let width = 0;

        if (this._bars.length) {
            width = this._bars
                .map(bar => bar.width)
                .reduce((a, b) => Math.max(a, b));
        }

        this._canvas.width = width;
    }

    _setHeight() {
        let height = 0;

        if (this._bars.length) {
            height = this._bars
                .map(bar => bar.height)
                .reduce((a, b) => a + b);
        }

        this._canvas.height = height;
    }

    _clear() {
        this._context.clearRect(0, 0, this._canvas.width, this._canvas.height);
    }

    _drawBars() {
        let y = 0;

        this._bars.forEach(bar => {
            this._drawBar(bar, y);
            y += bar.height;
        });
    }

    _drawBar(bar, y) {
        let x = 0;

        if (this._alignment === Alignments.right) {
            x = this._canvas.width - bar.width;
        }

        this._context.drawImage(bar, x, y);
    }
}
