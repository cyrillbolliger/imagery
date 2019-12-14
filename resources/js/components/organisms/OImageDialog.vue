<template>
    <ODialog
        :title="$t('images.create.generating')"
        @close="$emit('close', $event)"
    >
        <p>{{$t('images.create.waitPlease')}}</p>
        <div class="progress" v-if="!downloadReady">
            <div
                :aria-valuenow="uploadStatus"
                aria-valuemax="100"
                aria-valuemin="0"
                :style="`width: ${uploadStatus}%`"
                class="progress-bar"
                role="progressbar"
            >{{Math.round(uploadStatus)}}%
            </div>
        </div>

        <a download="image.png"
           ref="download"
           @click="$emit('close')"
           v-if="downloadReady"
        >
            <button class="btn btn-primary"
            >{{$t('images.create.imageDownload')}}
            </button>
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


        computed: {
            hasRawImage() {
                return this.imageData.backgroundType === BackgroundTypes.image
                    && this.imageData.rawImage;
            },

            uploadRawWeight() {
                if (!this.hasRawImage) {
                    return 0;
                }

                const raw = this.imageData.rawImage.src.length;
                const final = this.imageData.canvas.toDataURL().length;

                return 100 * raw / (raw + final);
            },

            uploadFinalWeight() {
                return 100 - this.uploadRawWeight;
            },
        },


        mounted() {
            this.save();
        },


        methods: {
            save() {
                this.imageData.filename = this.uniqueFilename();
                let upload;

                if (this.hasRawImage) {
                    this.showLegalCheck();
                    upload = this.uploadRawImage()
                        .then(() => this.uploadFinalImage());
                } else {
                    upload = this.uploadFinalImage();

                }

                upload.then(() => this.downloadButtonShow());
            },

            showLegalCheck() {
                // todo
            },

            uploadRawImage() {
                this.imageData.filenameRaw = `raw-${this.imageData.filename}`;

                const image = this.imageData.rawImage.src;
                const filename = this.imageData.filenameRaw;
                const uploader = new ImageUpload(image, filename);

                uploader.subscribe(
                    status => this.uploadStatus = status * this.uploadRawWeight
                );

                return uploader.upload('files/images')
                    .then(() => this.uploadRawImageMeta())
                    .catch(error => {
                        this.snackErrorRetry(error, this.$t('images.create.uploadFailed'))
                            .then(this.uploadRawImage());
                    });
            },

            uploadFinalImage() {
                this.imageData.filenameFinal = `final-${this.imageData.filename}`;

                const image = this.imageData.canvas.toDataURL();
                const filename = this.imageData.filenameFinal;
                const uploader = new ImageUpload(image, filename);

                uploader.subscribe(
                    status => this.uploadStatus = this.uploadRawWeight + status * this.uploadFinalWeight
                );

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
                    original_id: this.imageData.originalId,
                    filename: this.imageData.filenameFinal
                };

                return this.uploadImageMeta(payload);
            },

            uploadRawImageMeta() {
                const payload = {
                    background: this.imageData.backgroundType,
                    type: 'raw',
                    filename: this.imageData.filenameRaw
                };

                const cb = resp => this.imageData.originalId = resp.data.id;

                return this.uploadImageMeta(payload, cb);
            },

            uploadImageMeta(payload, successCallback = null) {
                return Api().post('images', payload)
                    .then(resp => {
                        if (successCallback instanceof Function) {
                            return successCallback(resp);
                        } else {
                            return resp;
                        }
                    })
                    .catch(error => {
                        this.snackErrorRetry(error, this.$t('images.create.uploadFailed'))
                            .then(() => this.uploadImageMeta(payload));
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
