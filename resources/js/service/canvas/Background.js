const Types = {
    gradient: 0,
    transparent: 1,
    image: 2
};

class Background {
    constructor() {
        this._canvas = document.createElement('canvas');
        this._context = this._canvas.getContext('2d');

        this._type = Types.gradient;
        this._image = null;
    }

    set type(type) {
        this._type = type;
    }

    set width(width) {
        this._canvas.width = width;
    }

    set height(height) {
        this._canvas.height = height;
    }

    set image(image) {
        this._image = image;
    }

    draw() {
        this._context.fillStyle = 'grey';
        this._context.fillRect(0, 0, this._canvas.width, this._canvas.height);

        return this._canvas;
    }
}

export {
    Types,
    Background
}
