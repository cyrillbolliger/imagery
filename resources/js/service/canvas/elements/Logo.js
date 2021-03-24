import {LogoTypes} from "../Constants";

const LogoTypeRatios = {
    [LogoTypes.alternative]: 4,
    [LogoTypes.gruene]: 4,
    [LogoTypes['gruene-vert-e-s']]: 4,
    [LogoTypes['gruene-verts']]: 4,
    [LogoTypes.verda]: 4,
    [LogoTypes.verdi]: 4,
    [LogoTypes['vert-e-s']]: 4,
    [LogoTypes.verts]: 4,
};

class Logo {
    constructor() {
        this._canvas = document.createElement('canvas');
        this._context = this._canvas.getContext('2d');

        this._logo = null;
        this._type = null;

        this._imageWidth = 0;
        this._imageHeight = 0;
    }

    set logo(logo) {
        this._logo = logo;
    }

    set width(width) {
        this._imageWidth = width;
    }

    set height(height) {
        this._imageHeight = height;
    }

    get height() {
        return this._canvas.height;
    }

    set type(type) {
        this._type = type;
    }

    draw() {
        this._context.clearRect(0, 0, this._canvas.width, this._canvas.height);

        if (this._logo) {
            this._drawLogo();
        }

        return this._canvas;
    }

    _drawLogo() {
        this._setSize();

        this._context.drawImage(this._logo, 0, 0, this._canvas.width, this._canvas.height);
    }

    _setSize() {
        let side;

        // for portrait images the width is authoritative for landscape images
        // the surface. this increases the logo on landscape images, but doesn't
        // break the rules for portrait formats.
        if (this._imageWidth < this._imageHeight) {
            side = this._imageWidth;
        } else {
            side = Math.sqrt(this._imageHeight * this._imageWidth);
        }

        const typeRatio = LogoTypeRatios[this._type];
        const ratio = (side / typeRatio) / this._logo.width;

        this._canvas.width = this._logo.width * ratio;
        this._canvas.height = this._logo.height * ratio;
    }
}

export {
    Logo
}
