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

/**
 * Specifies the margin below the bar in relation to the bars inner height.
 * This should be one 1/12, but this is too close (especially when using the
 * character J) so we'll go with 1/10;
 *
 * @type {number}
 */
const barMarginFactor = 0.1;

class Bar {
    constructor() {
        this._canvas = document.createElement('canvas');
        this._context = this._canvas.getContext('2d');

        this._schema = Schemes.green;
        this._alignment = Alignments.left;
        this._fontSize = 16;
        this._text = '';

        this._imageWidth = 0;
        this._barOversize = 0;
        this._textDims = {
            width: null,
            height: null,
            padding: null,
        };
    }

    set text(text) {
        this._text = text.toLocaleUpperCase().trim();
    }

    set fontSize(fontSize) {
        this._fontSize = fontSize;
    }

    set alignment(alignment) {
        this._alignment = alignment;
    }

    set schema(schema) {
        this._schema = schema;
    }

    set type(type) {
        this._font = type;
    }

    set imageWidth(width) {
        this._imageWidth = width;
    }

    draw() {
        this._setFont();
        this._setBarOversize();
        this._setTextDims();
        this._setCanvasWidth();
        this._setCanvasHeight();
        this._setFont(); // the resizing kills the font settings

        this._drawBackground();
        this._drawFont();

        return this._canvas;
    }

    _setFont() {
        this._context.font = `${this._fontSize}px ${this._font}`;
    }

    _setBarOversize() {
        this._barOversize = this._imageWidth * barSizeFactor;
    }

    _setTextDims() {
        this._textDims.width = this._context.measureText(this._text).width;
        this._textDims.height = parseInt(this._fontSize) * realCharHeightFactor;
        this._textDims.padding = parseInt(this._fontSize) * charPaddingFactor;
    }

    _setCanvasWidth() {
        const textWidth = this._textDims.width;
        const padding = this._textDims.padding;

        this._canvas.width = this._barOversize + textWidth + padding;
    }

    _setCanvasHeight() {
        const innerHeight = this._getBarHeight();

        this._canvas.height = innerHeight * (1 + barMarginFactor);
    }

    /**
     * Since we have a margin below the bar the bar height is smaller than the
     * canvas height.
     *
     * @returns {Number}
     * @private
     */
    _getBarHeight() {
        const textHeight = this._textDims.height;
        const padding = this._textDims.padding;
        return textHeight + 2 * padding;
    }

    _drawBackground() {
        if (this._schema === Schemes.white) {
            this._setGradientBackground();
        } else {
            this._context.fillStyle = this._schema.background;
        }

        this._context.fillRect(0, 0, this._canvas.width, this._getBarHeight());
    }

    _setGradientBackground() {
        let x, x1;
        const canvasWidth = this._canvas.width;
        const gradientWidth = this._barOversize;
        const preGradient = this._barOversize / 4;

        if (this._alignment === Alignments.left) {
            x = preGradient;
            x1 = preGradient + gradientWidth;
        } else {
            x = canvasWidth - preGradient;
            x1 = canvasWidth - gradientWidth - preGradient;
        }

        const gradient = this._context.createLinearGradient(x, 0, x1, 0);
        gradient.addColorStop(0, '#aaaaaa');
        gradient.addColorStop(1, '#ffffff');

        this._context.fillStyle = gradient;
    }

    _drawFont() {
        const y = this._getBarHeight() - this._textDims.padding;
        const x = this._getTextX();

        this._context.fillStyle = this._schema.text;
        this._context.textAlign = 'left';
        this._context.textBaseline = 'alphabetic';

        this._context.fillText(this._text, x, y);
    }

    _getTextX() {
        if (this._alignment === Alignments.left) {
            return this._barOversize;
        } else {
            return this._textDims.padding;
        }
    }
}

export {
    Bar,
    Schemes,
    Alignments,
    Types,
    barSizeFactor
}
