<template>
    <div class="form-group">
        <label class="mb-0 d-block">{{$t('images.create.background')}}</label>
        <div class="btn-group btn-group-toggle">
            <label :class="{'active': background === types.gradient}"
                   class="btn btn-secondary btn-sm">
                <input
                    :value="types.gradient"
                    name="background"
                    type="radio"
                    v-model="background"
                >{{$t('images.create.backgroundGreen')}}
            </label>
            <label :class="{'active': background === types.transparent}"
                   class="btn btn-secondary btn-sm">
                <input
                    :value="types.transparent"
                    name="background"
                    type="radio"
                    v-model="background"
                >{{$t('images.create.backgroundTransparent')}}
            </label>
            <label :class="{'active': background === types.image}"
                   class="btn btn-secondary btn-sm">
                <input
                    :value="types.image"
                    name="background"
                    type="radio"
                    v-model="background"
                    @click="$refs.uploader.click()"
                >{{$t('images.create.backgroundImage')}}
            </label>
        </div>

        <div class="form-group" v-if="background === types.image && image && !imageTooSmall">
            <label
                class="mb-0 mt-2"
                for="image-zoom"
            >{{$t('images.create.imageZoom')}}</label>
            <input
                :max="1"
                :min="0"
                @input="zoomImage()"
                class="form-control-range"
                id="image-zoom"
                step="0.01"
                type="range"
                v-model.number="zoom"
            >
        </div>

        <input
            @change="setImage($event)"
            class="custom-file-input"
            ref="uploader"
            type="file"
        >

        <div
            class="alert alert-warning"
            role="alert"
            v-if="background === types.image && imageTooSmall">
            {{$t('images.create.imageTooSmall')}}
        </div>
    </div>
</template>

<script>
    import {BackgroundTypes as Types} from "../../service/canvas/Constants";
    import SnackbarMixin from "../../mixins/SnackbarMixin";
    import BackgroundGradient from "../../service/canvas/elements/BackgroundGradient";
    import BackgroundTransparent from "../../service/canvas/elements/BackgroundTransparent";
    import BackgroundImage from "../../service/canvas/elements/BackgroundImage";
    import loadImage from "blueimp-load-image";

    const mimeTypesAllowed = [
        'image/jpeg',
        'image/png',
        'image/svg',
        'image/svg+xml'
    ];

    let requestedAnimationFrame;

    export default {
        name: "MBackgroundBlock",
        components: {},
        mixins: [SnackbarMixin],

        data() {
            return {
                block: null,
                background: Types.gradient,
                image: null,
                mimeType: null,
                types: Types,
                zoom: 0,
            }
        },

        props: {
            imageWidth: {
                required: true,
                type: Number,
            },
            imageHeight: {
                required: true,
                type: Number
            }
        },

        computed: {
            imageTooSmall() {
                if (!this.image) {
                    return false;
                }

                if (this.image.width < this.imageWidth) {
                    return true;
                }

                if (this.image.height < this.imageHeight) {
                    return true;
                }

                return false;
            }
        },

        mounted() {
            this.draw()
        },

        methods: {
            draw() {
                let canvas;

                switch (this.background) {
                    case Types.gradient:
                        canvas = this.drawGradient();
                        break;

                    case Types.transparent:
                        canvas = this.drawTransparent();
                        break;

                    case Types.image:
                        canvas = this.drawImage();
                        break;
                }

                this.$emit('drawn', canvas);
            },

            drawGradient() {
                this.block = this.backgroundFactory(BackgroundGradient);
                return this.block.draw();
            },

            drawTransparent() {
                this.block = this.backgroundFactory(BackgroundTransparent);
                return this.block.draw();
            },

            drawImage() {
                this.block = this.backgroundFactory(BackgroundImage);
                this.block.image = this.image;
                this.block.zoom = this.zoom;

                try {
                    return this.block.draw();
                } catch (e) {
                    this.snackErrorDismiss(
                        e,
                        this.$t('images.create.uploadedImageNotProcessable')
                    );
                }
            },

            backgroundFactory(type) {
                const bg = new type();
                bg.width = this.imageWidth;
                bg.height = this.imageHeight;

                return bg;
            },

            zoomImage() {
                if (requestedAnimationFrame) {
                    window.cancelAnimationFrame(requestedAnimationFrame);
                }

                requestedAnimationFrame = window.requestAnimationFrame(() => {
                    this.block.zoom = this.zoom;
                    this.$emit('drawn', this.block.draw());
                });
            },

            setImage(event) {
                if (!event.target.files.length) {
                    return; // no file was selected
                }

                const blob = event.target.files[0];

                if (this.mimeValidate(blob.type)) {
                    this.mimeType = blob.type;

                    loadImage(
                        blob,
                        this.onImageLoaded,
                        {orientation: true, canvas: true}
                    );
                }
            },

            onImageLoaded(image) {
                this.image = image;
                this.$emit('typeChanged', Types.image);
                this.$emit('imageChanged', {image: image, mimeType: this.mimeType});
            },

            mimeValidate(type) {
                if (mimeTypesAllowed.indexOf(type) === -1) {
                    this.snackErrorDismiss(
                        `"${type}" is not a supported mime type.`,
                        this.$t('images.create.mimeInvalid')
                    );

                    return false;
                }

                return true;
            },

            adjustZoom(dimNew, dimOld) {
                const ratio = dimNew / dimOld;
                this.zoom *= ratio;
            }
        },

        watch: {
            background(value) {
                if (Types.image === value && !this.image) {
                    return;
                }

                this.draw();
                this.$emit('typeChanged', value);
            },
            image() {
                this.zoom = 0;
                this.draw();
            },
            imageWidth(valueNew, valueOld) {
                this.adjustZoom(valueNew, valueOld);
                this.draw();
            },
            imageHeight(valueNew, valueOld) {
                this.adjustZoom(valueNew, valueOld);
                this.draw();
            },
        },
    }
</script>

<style lang="scss" scoped>
    .custom-file-input {
        display: none;
    }
</style>
