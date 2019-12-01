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
                >{{$t('images.create.backgroundImage')}}
            </label>
        </div>

        <div class="custom-file" v-if="background === types.image">
            <input
                :placeholder="$t('images.create.selectImage')"
                @change="setImage($event)"
                class="custom-file-input"
                id="customFile"
                type="file"
            >
            <label class="custom-file-label" for="customFile">{{$t('images.create.browse')}}</label>
        </div>

        <div
            class="alert alert-warning"
            role="alert"
            v-if="background === types.image && imageTooSmall">
            {{$t('images.create.imageTooSmall')}}
        </div>
    </div>
</template>

<script>
    import {Types, Background} from "../../service/canvas/Background";

    export default {
        name: "MBackgroundBlock",
        components: {},

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

                this.$emit('drawn', this.block.draw());
            },

            setImage(event) {
                const file = event.target.files[0];
                const image = new Image();
                const reader = new FileReader();

                reader.onload = (e) => {
                    image.src = e.target.result;
                    this.image = image;
                };

                reader.readAsDataURL(file);
            },
        },

        watch: {
            background() {
                this.draw();
            },
            image() {
                this.draw();
            },
        },
    }
</script>

<style scoped>

</style>
