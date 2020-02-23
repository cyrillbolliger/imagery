<template>
    <ODialog
        :title="$t('images.create.generating')"
        @close="$emit('close', $event)"
    >
        <p v-if="uploadStatus < 100">
            {{$t('images.create.waitPlease')}}
            <span v-if="showLegalCheck && !legalFormHidden">{{$t('images.create.legal.announce')}}</span>
        </p>

        <div class="progress" v-if="uploadStatus < 100 && (!showLegalCheck || legalFormHidden)">
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

        <MLegalForm
            :imageUpload="uploadPromise"
            @completed="onLegalUploadCompleted"
            @saving="legalFormHidden = true"
            v-if="showLegalCheck"
            v-show="!legalFormHidden"
        />

        <button
            @click="downloadAndClose($event)"
            class="btn btn-primary"
            v-if="downloadReady && !showLegalCheck"
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
    import MLegalForm from "../molecules/MLegalForm";

    const metaUploadProgress = 5;
    const legalUploadProgress = 5;

    export default {
        name: "OImageDialog",
        components: {MLegalForm, ODialog},
        mixins: [SnackbarMixin],


        data() {
            return {
                uploadRawStatus: 0,
                uploadFinalStatus: 0,
                uploadMetaStatus: 0,
                uploadLegalStatus: 0,
                downloadReady: false,
                showLegalCheck: false,
                uploadPromise: null,
                resolveUpload: null,
                legalFormHidden: false,
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

            uploadImagesTotalWeight() {
                const uploadComplete = 100;
                const imageCount = this.hasRawImage ? 2 : 1;
                const nonImageProgress = this.hasRawImage
                    ? imageCount * metaUploadProgress + legalUploadProgress
                    : metaUploadProgress;

                return uploadComplete - nonImageProgress;
            },

            uploadRawWeight() {
                if (!this.hasRawImage) {
                    return 0;
                }

                const raw = this.rawImageDataUrl.length;
                const final = this.imageData.canvas.toDataURL().length;

                return this.uploadImagesTotalWeight * raw / (raw + final);
            },

            uploadFinalWeight() {
                return this.uploadImagesTotalWeight - this.uploadRawWeight;
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

            uploadStatus() {
                return this.uploadRawStatus * this.uploadRawWeight
                    + this.uploadFinalStatus * this.uploadFinalWeight
                    + this.uploadMetaStatus * metaUploadProgress
                    + this.uploadLegalStatus * legalUploadProgress;
            },
        },

        created() {
            this.uploadPromise = new Promise(res => this.resolveUpload = res);
        },

        mounted() {
            this.save();
        },


        methods: {
            save() {
                this.imageData.filename = this.uniqueFilename();
                let upload;

                if (this.hasRawImage) {
                    this.showLegalCheck = true;
                    upload = this.uploadRawImage()
                        .then(() => this.resolveUpload(this.imageData.originalId))
                        .then(() => this.uploadFinalImage());
                } else {
                    upload = this.uploadFinalImage();
                }

                upload.then(() => this.downloadButtonShow());
            },

            uploadRawImage() {
                this.imageData.filenameRaw = `raw-${this.imageData.filename}.${this.rawImageExtension}`;

                const image = this.rawImageDataUrl;
                const filename = this.imageData.filenameRaw;
                const uploader = new ImageUpload(image, filename);

                uploader.subscribe(status => this.uploadRawStatus = status);

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

                uploader.subscribe(status => this.uploadFinalStatus = status);

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
                    .then(() => this.uploadMetaStatus++)
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

                    link.click();

                    window.setTimeout(() => {
                        // do this delayed, so the browser has time to navigate
                        // to the URL (chrome) and doesn't release the memory
                        // to early (safari on iOS).
                        URL.revokeObjectURL(link.href);
                        link.removeAttribute('href');
                        document.body.removeChild(link);
                    }, 1000);
                });

            },

            downloadAndClose() {
                this.executeDownload();
                this.$emit('close');
            },

            onLegalUploadCompleted() {
                this.uploadLegalStatus = 1;
                this.showLegalCheck = false;
                this.legalFormHidden = false;
            },
        }
    }
</script>

<style scoped>

</style>
