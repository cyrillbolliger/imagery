<template>
    <div>
        <div class="btn-group btn-group-toggle">
            <label :class="{'active': background === types.gradient}"
                   class="btn btn-secondary">
                <input
                    :value="types.gradient"
                    name="background"
                    type="radio"
                    v-model="background"
                >{{$t('images.create.backgroundGreen')}}
            </label>
            <label :class="{'active': background === types.transparent}"
                   class="btn btn-secondary">
                <input
                    :value="types.transparent"
                    name="background"
                    type="radio"
                    v-model="background"
                >{{$t('images.create.backgroundTransparent')}}
            </label>
            <label :class="{'active': background === types.image}"
                   class="btn btn-secondary">
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
            <label for="image-zoom">{{$t('images.create.imageZoom')}}</label>
            <input
                :max="1"
                :min="0"
                @input="draw()"
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
            id="customFile"
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

    const mimeTypesAllowed = [
        'image/jpeg',
        'image/png',
        'image/svg',
        'image/svg+xml'
    ];

    export default {
        name: "MBackgroundBlock",
        components: {},
        mixins: [SnackbarMixin],

        data() {
            return {
                block: null,
                background: Types.gradient,
                image: null,
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
                        this.$t('images.create.uploaded_image_not_processable')
                    );
                }
            },

            backgroundFactory(type) {
                const bg = new type();
                bg.width = this.imageWidth;
                bg.height = this.imageHeight;

                return bg;
            },

            setImage(event) {
                if (!event.target.files.length) {
                    return; // no file was selected
                }

                const blob = event.target.files[0];
                const image = new Image();
                const reader = new FileReader();

                reader.onload = (e) => {
                    if (this.mimeValidate(blob.type)) {
                        image.src = e.target.result;
                        this.image = image;
                    }
                };

                reader.readAsDataURL(blob);
            },

            mimeValidate(type) {
                if (mimeTypesAllowed.indexOf(type) === -1) {
                    this.snackErrorDismiss(
                        `"${type}" is not a supported mime type.`,
                        this.$t('images.create.invalid_mime')
                    );

                    return false;
                }

                return true;
            },
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
                this.draw();
            },
            imageWidth() {
                this.draw();
            },
            imageHeight() {
                this.draw();
            },
        },
    }
</script>

<style scoped>

</style>
