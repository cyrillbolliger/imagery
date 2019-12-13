<template>
    <ODialog
        :title="$t('images.create.generating')"
        @close="$emit('close', $event)"
    >
        <div class="progress">
            <div
                :aria-valuenow="uploadStatus"
                aria-valuemax="100"
                aria-valuemin="0"
                class="progress-bar"
                role="progressbar"
            >{{uploadStatus}}%
            </div>
        </div>

        <a download="image.png"
           ref="download"
           v-if="downloadReady">
            <button>{{$t('images.create.imageDownload')}}</button>
        </a>

    </ODialog>
</template>

<script>
    import {BackgroundTypes} from "../../service/canvas/Constants";
    import Api from "../../service/Api";
    import ImageUpload from "../../service/ImageUpload";
    import SnackbarMixin from "../../mixins/SnackbarMixin";
    import ODialog from "../organisms/ODialog";

    export default {
        name: "OImageDialog",
        components: {ODialog},
        mixins: [SnackbarMixin],


        data() {
            return {
                uploadStatus: 0,
                downloadReady: false,
            }
        },


        props: {
            imageData: {
                required: true,
                type: Object
            }
        },


        computed: {},


        mounted() {
            this.save();
        },


        methods: {
            save() {
                this.imageData.filename = this.uniqueFilename();

                if (this.imageData.backgroundType === BackgroundTypes.image && this.imageData.rawImage) {
                    this.showLegalCheck();
                    this.uploadRawImage();
                }

                this.uploadFinalImage()
                    .then(() => this.downloadButtonShow());
            },

            showLegalCheck() {
                // todo
            },

            uploadRawImage() {
                // todo
            },

            uploadFinalImage() {
                const finalImage = this.imageData.canvas.toDataURL();
                const uploader = new ImageUpload(finalImage, this.imageData.filename);

                uploader.subscribe(status => this.uploadStatus = status * 100);

                return uploader.upload('files/images')
                    .then(() => this.uploadFinalImageMeta())
                    .catch(error => {
                        this.snackErrorRetry(error, this.$t('images.create.uploadFailed'))
                            .then(this.uploadFinalImage());
                    });
            },

            uniqueFilename() {
                this.$store.dispatch('counter/increment');
                return this.$store.getters['counter/get'] + '.png';
            },

            uploadFinalImageMeta() {
                const payload = {
                    logo_id: null, // todo
                    background: this.imageData.backgroundType,
                    type: 'final',
                    original_id: null, // todo
                    filename: this.imageData.filename
                };

                return Api().post('images', payload)
                    .catch(error => {
                        this.snackErrorRetry(error, this.$t('images.create.uploadFailed'))
                            .then(() => this.uploadFinalImageMeta());
                    });
            },

            downloadButtonShow() {
                this.downloadReady = true;

                this.$nextTick(() => {
                    const finalImage = this.imageData.canvas.toDataURL()
                        .replace('image/png', 'image/octet-stream');

                    this.$refs.download.setAttribute('href', finalImage);
                });
            },
        }
    }
</script>

<style scoped>

</style>
