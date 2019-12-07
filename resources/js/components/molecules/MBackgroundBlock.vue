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
    import {Types, Background} from "../../service/canvas/elements/Background";
    import SnackbarMixin from "../../mixins/SnackbarMixin";

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
                block: new Background(),
                background: Types.gradient,
                image: null,
                types: Types
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
                this.block.width = this.imageWidth;
                this.block.height = this.imageHeight;
                this.block.type = this.background;
                this.block.image = this.image;

                let canvas;

                try {
                    canvas = this.block.draw();
                    this.$emit('drawn', canvas);
                } catch (e) {
                    this.snackErrorDismiss(
                        e,
                        this.$t('images.create.uploaded_image_not_processable')
                    );
                }
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
