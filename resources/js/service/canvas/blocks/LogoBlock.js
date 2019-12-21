import {LogoTypes, LogoSublineRatios} from "../Constants";

class LogoBlock {
    constructor() {
        this._canvas = document.createElement('canvas');
        this._context = this._canvas.getContext('2d');

        this._logo = null;
        this._subline = null;

        this._type = null;
    }

    set logo(logo) {
        this._logo = logo;
    }

    set type(type) {
        this._type = type;
    }

    set subline(subline) {
        this._subline = subline;
    }

    draw() {
        this._context.clearRect(0, 0, this._canvas.width, this._canvas.height);

        if (this._hasLogo()) {
            this._drawLogo();
        }

        if (this._hasSubline()) {
            this._drawSubline();
        }

        return this._canvas;
    }

    _hasLogo() {
        return this._logo && this._logo.width > 0 && this._logo.height > 0;
    }

    _hasSubline() {
        return this._subline && this._typeHasSubline();
    }
    _typeHasSubline() {
        return LogoTypes.alternative !== this._type;
    }

    _drawSubline() {
        const top = this._logo.height + this._marginTop();

        this._context.drawImage(this._subline, this._left(), top);
    }

    _drawLogo() {
        this._setSize();

        this._context.drawImage(this._logo, 0, 0);
    }

    _setSize() {
        const sublineWidth = this._subline.width + this._left();
        this._canvas.width = Math.max(this._logo.width, sublineWidth);
        this._canvas.height = this._logo.height + this._subline.height + this._marginTop();
    }

    _marginTop() {
        return this._logo.height * LogoSublineRatios[this._type].topMargin;
    }

    _left() {
        return this._logo.width * LogoSublineRatios[this._type].left;
    }
}

export {
    LogoBlock
}
