import BorderHelper from "../BorderHelper";

const borderColor = '#ffffff';

class Border {
    constructor() {
        this._canvas = document.createElement('canvas');
        this._context = this._canvas.getContext('2d');

        this._border = true;
        this._borderWidth = 0;
    }

    set border(enabled) {
        this._border = enabled;
    }

    set width(width) {
        this._canvas.width = width;
    }

    set height(height) {
        this._canvas.height = height;
    }

    get borderWidth() {
        return this._borderWidth;
    }

    draw() {
        this._context.clearRect(0, 0, this._canvas.width, this._canvas.height);

        if (this._border) {
            this._drawBorder();
        }

        return this._canvas;
    }

    _drawBorder() {
        this._setBorderWidth();

        this._context.fillStyle = borderColor;
        this._context.fillRect(0, 0, this._canvas.width, this._canvas.height);

        this._context.globalCompositeOperation = 'xor';
        this._setClippingArea();
    }

    _setClippingArea() {
        const ctx = this._context;
        const bWidth = this._borderWidth;
        const radius = BorderHelper.radius(bWidth);
        const width = this._canvas.width - 2 * bWidth;
        const height = this._canvas.height - 2 * bWidth;

        ctx.beginPath();
        ctx.moveTo(bWidth, bWidth + radius);
        ctx.lineTo(bWidth, bWidth + height - radius);
        ctx.arcTo(bWidth, bWidth + height, bWidth + radius, bWidth + height, radius);
        ctx.lineTo(bWidth + width - radius, bWidth + height);
        ctx.arcTo(bWidth + width, bWidth + height, bWidth + width, bWidth + height - radius, radius);
        ctx.lineTo(bWidth + width, bWidth + radius);
        ctx.arcTo(bWidth + width, bWidth, bWidth + width - radius, bWidth, radius);
        ctx.lineTo(bWidth + radius, bWidth);
        ctx.arcTo(bWidth, bWidth, bWidth, bWidth + radius, radius);
        ctx.fill();
    }

    _setBorderWidth() {
        this._borderWidth = BorderHelper.width(this._canvas.width, this._canvas.height);
    }
}

export {
    Border
}
