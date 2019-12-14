import Api from "./Api";

const uploadSliceSize = 1024 * 256; // 256 kb

export default class ImageUpload {
    constructor(image, filename) {
        this._image = image;
        this._start = 0;
        this._filename = filename;
        this._subscribers = [];
    }

    subscribe(callback) {
        this._subscribers.push(callback);
    }

    upload(endpoint) {
        const stop = this._determineSliceStop();
        const slice = this._image.slice(this._start, stop);

        return this._uploadChunk(endpoint, slice);
    }

    _uploadChunk(endpoint, slice) {
        const payload = {
            base64data: slice,
            part: this._start,
            filename: this._filename,
        };

        return Api().post(endpoint, payload)
            .then(resp => {
                this._start = this._start + uploadSliceSize;

                if (this._hasNextChunk()) {
                    this._notify();

                    return this.upload(endpoint);
                } else {
                    this._notify(true);

                    return resp;
                }
            });
    }

    _determineSliceStop() {
        const fullSlice = this._start + uploadSliceSize;
        const imageLen = this._image.length;

        return fullSlice > imageLen ? imageLen : fullSlice;
    }

    _hasNextChunk() {
        return this._start < this._image.length;
    }

    _notify(complete) {
        const progress = complete ? 1 : this._start / this._image.length;

        this._subscribers.forEach(callback => callback(progress));
    }
}
