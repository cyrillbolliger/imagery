import Background from "./Background";

const transparentSquareSize = 20;
const transparentSquareColor = '#d7d7d7';

export default class extends Background {
    _drawBackground() {
        const square = transparentSquareSize;
        const width = this._canvas.width;
        const height = this._canvas.height;

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
}
