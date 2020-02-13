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

        <button
            @click="downloadAndClose($event)"
            class="btn btn-primary"
            v-if="downloadReady"
        >{{$t('images.create.imageDownload')}}
        </button>
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

                const raw = this.rawImageDataUrl.length;
                const final = this.imageData.canvas.toDataURL().length;

                return 100 * raw / (raw + final);
            },

            uploadFinalWeight() {
                return 100 - this.uploadRawWeight;
            },

            rawImageExportType() {
                return 'image/jpeg' === this.imageData.rawImage.mimeType ? 'image/jpeg' : 'image/png';
            },

            rawImageExtension() {
                return 'image/jpeg' === this.rawImageExportType ? 'jpeg' : 'png';
            },

            rawImageDataUrl() {
                return this.imageData.rawImage.image.toDataURL(this.rawImageExportType);
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
                this.imageData.filenameRaw = `raw-${this.imageData.filename}.${this.rawImageExtension}`;

                const image = this.rawImageDataUrl;
                const filename = this.imageData.filenameRaw;
                const uploader = new ImageUpload(image, filename);

                uploader.subscribe(
                    status => this.uploadStatus = status * this.uploadRawWeight
                );

                return uploader.upload('files/images')
                    .then(() => this.uploadRawImageMeta())
                    .catch(error => {
                        this.snackErrorRetry(error, this.$t('images.create.uploadFailed'))
                            .then(this.uploadRawImage);
                    });
            },

            uploadFinalImage() {
                this.imageData.filenameFinal = `final-${this.imageData.filename}.png`;

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
                            .then(this.uploadFinalImage);
                    });
            },

            uniqueFilename() {
                this.$store.dispatch('counter/increment');
                return this.$store.getters['counter/get'];
            },

            uploadFinalImageMeta() {
                const payload = {
                    logo_id: this.imageData.logoId,
                    background: this.imageData.backgroundType,
                    type: 'final',
                    original_id: this.imageData.originalId,
                    filename: this.imageData.filenameFinal,
                    keywords: this.imageData.keywords,
                };

                return this.uploadImageMeta(payload);
            },

            uploadRawImageMeta() {
                const payload = {
                    background: this.imageData.backgroundType,
                    type: 'raw',
                    filename: this.imageData.filenameRaw,
                    keywords: this.imageData.keywords,
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
            },

            executeDownload() {
                /**
                 * There is some picky stuff in here, especially for chrome.
                 *
                 * @see https://stackoverflow.com/questions/3916191/download-data-url-file
                 * @see https://stackoverflow.com/questions/37135417/download-canvas-as-png-in-fabric-js-giving-network-error/
                 * @see https://developer.mozilla.org/en-US/docs/Web/API/HTMLCanvasElement/toBlob
                 */
                this.imageData.canvas.toBlob(imageBlob => {
                    const link = document.createElement('a');

                    link.download = 'image.png';
                    link.href = URL.createObjectURL(imageBlob);

                    document.body.appendChild(link);

                    link.onclick = () => {
                        requestAnimationFrame(() => {
                            URL.revokeObjectURL(link.href);
                            link.removeAttribute('href');
                            document.body.removeChild(link);
                        });
                    };

                    link.click();
                });

            },

            downloadAndClose() {
                this.executeDownload();
                this.$emit('close');
            }
        }
    }
</script>

<style scoped>

</style>
