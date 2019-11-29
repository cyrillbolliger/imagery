const Schemes = {
    white: {
        background: '#ffffff',
        text: '#84b414',
    },
    green: {
        background: '#84b414',
        text: '#ffffff',
    },
    magenta: {
        background: '#e10078',
        text: '#ffffff',
    }
};

const Alignments = {
    left: 0,
    right: 1
};

const Types = {
    headline: 'SanukFat',
    subline: 'SanukBold'
};

/**
 * Specifies the oversize of the bar in relation to the canvas width.
 *
 * 0.2 means that we'll add 20% of the canvas width to the bar.
 *
 * @type {number}
 */
const barSizeFactor = 0.2;

/**
 * If we multiply the font size with factor we should get the height of
 * the character M in pixels (without any padding).
 *
 * @type {number}
 */
const realCharHeightFactor = 0.74;

/**
 * The design guide specifies to have a padding of 1/4 of the character height.
 * However the examples in the guide have 1/5 of padding. Since this is nicer,
 * we'll stick to 1/5 :)
 *
 * @type {number}
 */
const charPaddingFactor = 0.2;

class Bar {
    constructor(context, schema, alignment, type) {
        this._c = context;
        this._schema = schema;
        this._alignment = alignment;
        this._font = type;
        this._fontSize = 16;
        this._text = '';
        this._x = 0;
        this._top = 100;
        this._barOversize = 0;
        this._textDims = {
            width: null,
            height: null,
            padding: null,
        };
    }

    set text(text) {
        this._text = text.toLocaleUpperCase();
        this._draw();
    }

    set fontSize(fontSize) {
        this._fontSize = fontSize;
        this._draw();
    }

    set alignment(alignment) {
        this._alignment = alignment;
        this._draw();
    }

    get barWidth() {
        const textWidth = this._textDims.width;
        const padding = this._textDims.padding;

        return this._barOversize + textWidth + padding;
    }

    get barHeight() {
        const textHeight = this._textDims.height;
        const padding = this._textDims.padding;

        return textHeight + 2 * padding;
    }

    _draw() {
        this._setBarOversize();
        this._setFont();
        this._setTextDims();
        this._setX();

        this._drawBackground();
        this._drawFont();
    }

    _setBarOversize() {
        this._barOversize = this._getCanvasWidth() * barSizeFactor;
    }

    _setTextDims() {
        this._textDims.width = this._c.measureText(this._text).width;
        this._textDims.height = parseInt(this._fontSize) * realCharHeightFactor;
        this._textDims.padding = parseInt(this._fontSize) * charPaddingFactor;
    }

    _setFont() {
        this._c.font = `${this._fontSize}px ${this._font}`;
    }

    _setX() {
        if (this._alignment === Alignments.left) {
            this._x = 0;
        } else {
            this._x = this._getCanvasWidth() - this.barWidth;
        }
    }

    _getCanvasWidth() {
        return this._c.canvas.width;
    }

    _drawBackground() {
        if (this._schema === Schemes.white) {
            this._setGradientBackground();
        } else {
            this._c.fillStyle = this._schema.background;
        }

        this._c.fillRect(this._x, this._top, this.barWidth, this.barHeight);
    }

    _setGradientBackground() {
        let x, x1;
        const canvasWidth = this._getCanvasWidth();
        const gradientWidth = this._barOversize;
        const preGradient = this._barOversize / 4;

        if (this._alignment === Alignments.left) {
            x = preGradient;
            x1 = preGradient + gradientWidth;
        } else {
            x = canvasWidth - preGradient;
            x1 = canvasWidth - gradientWidth - preGradient;
        }

        const gradient = this._c.createLinearGradient(x, 0, x1, 0);
        gradient.addColorStop(0, '#aaaaaa');
        gradient.addColorStop(1, '#ffffff');

        this._c.fillStyle = gradient;
    }

    _drawFont() {
        const y = this._top + this.barHeight - this._textDims.padding;
        const x = this._getTextX();

        this._c.fillStyle = this._schema.text;
        this._c.textAlign = 'left';
        this._c.textBaseline = 'baseline';

        this._c.fillText(this._text, x, y);
    }

    _getTextX() {
        if (this._alignment === Alignments.left) {
            return this._x + this._barOversize;
        } else {
            return this._x + this._textDims.padding;
        }
    }
}

export {
    Bar,
    Schemes,
    Alignments,
    Types
}
