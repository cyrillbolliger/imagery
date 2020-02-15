import BorderHelper from "../BorderHelper";

const font = 'Arial';

/**
 * Make the font size to fill x times the border height
 *
 * @type {number}
 */
const fontSizeFactor = 0.7;

/**
 * Correction factor to optically center the text baseline 'middle'.
 *
 * This is necessary, because the baseline middle also considers descending
 * characters as y and j. However we don't have any, since it's all in capitals.
 *
 * @type {number}
 */
const opticalCenterFactor = 0.05;

/**
 * Top and bottom padding for the text.
 *
 * @type {number}
 */
const paddingFactor = (1.2 - fontSizeFactor) / 2;

class Copyright {
    constructor() {
        this._canvas = document.createElement('canvas');
        this._context = this._canvas.getContext('2d');

        this._text = '';
        this._color = '#000000';

        this._imageWidth = 0;
        this._imageHeight = 0;

        this._textDims = {
            width: null,
            height: null,
            padding: null,
        };
        this._fontSize = 0;
    }

    set text(value) {
        this._text = value.toLocaleUpperCase().trim();
    }

    set color(value) {
        this._color = value;
    }

    set width(width) {
        this._imageWidth = width;
    }

    set height(height) {
        this._imageHeight = height;
    }

    draw() {
        this._context.clearRect(0, 0, this._canvas.width, this._canvas.height);

        if (this._text) {
            this._drawText();
        }

        return this._canvas;
    }

    _drawText() {
        this._setFontSize();
        this._setFont();
        this._setTextDims();
        this._setCanvasWidth();
        this._setCanvasHeight();
        this._setFont(); // the resizing kills the font settings
        this._setColor();

        this._writeText();
    }

    _setFont() {
        this._context.font = `${this._fontSize}px ${font}`;
    }

    _setFontSize() {
        const borderWidth = BorderHelper.width(this._imageWidth, this._imageHeight);
        this._fontSize = borderWidth * fontSizeFactor;
    }

    _setTextDims() {
        this._textDims.width = this._context.measureText(this._text).width;
        this._textDims.height = parseFloat(this._fontSize);
        this._textDims.padding = this._getPadding();
    }

    _getPadding() {
        return this._fontSize * paddingFactor;
    }

    _setCanvasWidth() {
        this._canvas.width = this._textDims.width + 2; // +2 to fix anti aliasing cutoffs
    }

    _setCanvasHeight() {
        this._canvas.height = this._textDims.height + 2 * this._textDims.padding;
    }

    _setColor() {
        this._context.fillStyle = this._color;
    }

    _writeText() {
        const y = this._canvas.height / 2 + this._fontSize * opticalCenterFactor;
        const x = 1; // 1 instead of 0 to fix anti aliasing cutoffs

        this._context.fillStyle = this._color;
        this._context.textAlign = 'left';
        this._context.textBaseline = 'middle';

        this._context.fillText(this._text, x, y);
    }
}

export {
    Copyright
}
