<template>
    <div class="form-group">
        <div class="row no-gutters" id="image-size">
            <div class="col-12 col-sm-6">
                <label
                    class="mt-2 mb-0"
                    for="canvas-format"
                >{{$t('images.create.format')}}</label>
                <div class="input select">
                    <ModelSelect
                        :options="sizes"
                        :value="sizeSelected"
                        @input="setSize($event)"
                        class="form-control"
                        id="canvas-format"
                        required="true"></ModelSelect>
                </div>
            </div>
            <div class="col-6 col-sm-3 pr-1 pr-sm-0 pl-sm-2">
                <div class="input number">
                    <label
                        class="mt-2 mb-0"
                        for="canvas-width-setter"
                    >{{$t('images.create.width')}}</label>
                    <input :disabled="!custom"
                           class="form-control"
                           id="canvas-width-setter"
                           max="10000"
                           min="100"
                           step="1"
                           type="number"
                           v-model.number="width">
                </div>
            </div>
            <div class="col-6 col-sm-3 pl-1 pl-sm-2">
                <div class="input number">
                    <label
                        class="mt-2 mb-0"
                        for="canvas-height-setter"
                    >{{$t('images.create.height')}}</label>
                    <input :disabled="!custom"
                           class="form-control"
                           id="canvas-height-setter"
                           max="10000"
                           min="100"
                           step="1"
                           type="number"
                           v-model.number="height">
                </div>
            </div>
        </div>
        <div
            class="alert alert-warning mt-3"
            role="alert"
            v-if="width*height > 3000**2">
            {{$t('images.create.oversizeWarning')}}
        </div>
    </div>
</template>

<script>
    import {ModelSelect} from 'vue-search-select'

    export default {
        name: "MSizeBlock",
        components: {ModelSelect},

        data() {
            return {
                width: 1080,
                height: 1080,
                custom: false,
                sizes: [
                    {value: '1080x1080', text: this.$t('images.create.sizes.square')},
                    {value: '1200x630', text: this.$t('images.create.sizes.fbTimeline')},
                    {value: '1920x1080', text: this.$t('images.create.sizes.fbEvent')},
                    {value: '1200x628', text: this.$t('images.create.sizes.fbWebsite')},
                    {value: '1920x1080', text: this.$t('images.create.sizes.video')},
                    {value: '1024x512', text: this.$t('images.create.sizes.twFeed')},
                    {value: '1080x1920', text: this.$t('images.create.sizes.instaStory')},
                    {value: 'custom', text: this.$t('images.create.sizes.custom')},
                ],
                sizeSelected: null,
            }
        },

        created() {
            this.sizeSelected = this.sizes[0].value;
        },

        methods: {
            setSize(value) {
                this.sizeSelected = value;

                this.custom = 'custom' === value;

                if (this.custom) {
                    return;
                }

                const dims = value.split('x');

                this.width = parseInt(dims[0]);
                this.height = parseInt(dims[1]);
            },

            emitSizeChanged() {
                const width = this.width > 10 ? this.width : 10;
                const height = this.height > 10 ? this.height : 10;

                this.$emit('sizeChanged', {width, height});
            }
        },

        watch: {
            width() {
                this.emitSizeChanged();
            },

            height() {
                this.emitSizeChanged();
            }
        }
    }
</script>

<style lang="scss" scoped>
    .custom-file-input {
        display: none;
    }
</style>
