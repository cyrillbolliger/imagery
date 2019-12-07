import {RotationAngle} from "./../Constants";
import Background from "./Background";

const gradientColorDark = '#66971c';
const gradientColorLight = '#b0c300';

export default class extends Background {
    _drawBackground() {
        const width = this._canvas.width;
        const height = this._canvas.height;
        const shift = Math.tan(RotationAngle) * height;
        const gradient = this._context.createLinearGradient(0, 0, shift, height);

        gradient.addColorStop(0, gradientColorDark);
        gradient.addColorStop(0.5, gradientColorLight);
        gradient.addColorStop(1, gradientColorDark);

        this._context.fillStyle = gradient;
        this._context.fillRect(0, 0, width, height);
    }
}
