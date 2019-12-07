import Background from "./Background";

export default class extends Background {
    constructor() {
        super();

        this._image = null;

        this._containerWidth = 0; // width of the imagery canvas (!= background image width)
        this._containerHeight = 0; // height of the imagery canvas (!= background image width)

        this._zoom = 0;
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

    set zoom(zoom) {
        this._zoom = zoom;
    }

    _drawBackground() {
        this._setCanvasSize();

        const width = this._canvas.width;
        const height = this._canvas.height;

        if (this._image) {
            this._context.drawImage(this._image, 0, 0, width, height);

            // if we just draw the image, we dont get an error if the uploaded
            // given document isn't a processable image. if we, however repaint
            // it using the following line, an error is thrown as expected.
            this._context.drawImage(this._canvas, 0, 0, width, height);
        }
    }

    _setCanvasSize() {
        const aspectRatioImage = this._image.width / this._image.height;

        let dW = this._image.width - this._containerWidth;
        let dH = this._image.height - this._containerHeight;

        dW = dW > 0 ? dW : 0;
        dH = dH > 0 ? dH : 0;

        let width, height;

        if (dW < dH) {
            width = this._containerWidth + this._zoom * dW;
            height = width / aspectRatioImage;
        } else {
            height = this._containerHeight + this._zoom * dH;
            width = height * aspectRatioImage;
        }

        this._canvas.width = width;
        this._canvas.height = height;
    }
}
