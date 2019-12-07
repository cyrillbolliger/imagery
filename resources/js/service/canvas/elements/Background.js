import {BackgroundTypes as Types, RotationAngle} from "./../Constants";

const transparentSquareSize = 20;
const transparentSquareColor = '#d7d7d7';

const gradientColorDark = '#66971c';
const gradientColorLight = '#b0c300';

class Background {
    constructor() {
        this._canvas = document.createElement('canvas');
        this._context = this._canvas.getContext('2d');

        this._type = Types.gradient;
        this._image = null;

        this._containerWidth = 0; // width of the imagery canvas (!= background image width)
        this._containerHeight = 0; // height of the imagery canvas (!= background image width)
    }

    set type(type) {
        this._type = type;
    }

    get type() {
        return this._type;
    }

    set width(width) {
        this._containerWidth = width;
    }

    set height(height) {
        this._containerHeight = height;
    }

    set image(image) {
        this._image = image;
    }

    draw() {
        switch (this._type) {
            case Types.gradient:
                this._drawGradient();
                break;

            case Types.transparent:
                this._drawTransparent();
                break;

            case Types.image:
                this._drawImage();
        }

        return this._canvas;
    }

    _drawGradient() {
        const width = this._containerWidth;
        const height = this._containerHeight;
        const shift = Math.tan(RotationAngle) * height;

        this._canvas.width = width;
        this._canvas.height = height;

        const gradient = this._context.createLinearGradient(0, 0, shift, height);
        gradient.addColorStop(0, gradientColorDark);
        gradient.addColorStop(0.5, gradientColorLight);
        gradient.addColorStop(1, gradientColorDark);

        this._context.fillStyle = gradient;
        this._context.fillRect(0, 0, width, height);
    }

    _drawTransparent() {
        const square = transparentSquareSize;
        const width = this._containerWidth;
        const height = this._containerHeight;

        this._canvas.width = width;
        this._canvas.height = height;

        this._context.fillStyle = transparentSquareColor;
        let odd = true;

        for (let x = 0; x < width; x += square) {
            for (let y = 0; y < height; y += square) {
                if (odd) {
                    this._context.fillRect(x, y, square, square);
                }
                odd = !odd;
            }
            odd = !odd;
        }
    }

    _drawImage() {
        this._canvas.width = this._image.width;
        this._canvas.height = this._image.height;

        if (this._image) {
            this._context.drawImage(this._image, 0, 0);

            // if we just draw the image, we dont get an error if the uploaded
            // given document isn't a processable image. if we, however repaint
            // it using the following line, an error is thrown as expected.
            this._context.drawImage(this._canvas, 0, 0);
        }
    }
}

export {
    Types,
    Background
}
