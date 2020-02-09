<template>
    <div class="form-group">
        <label for="logo" class="mb-0 d-flex align-items-center">
            {{$t('images.create.logo')}}
            <div
                v-if="!logosReady || logoDefaultSaving"
                class="spinner-border spinner-border-sm text-primary ml-2"
                role="status">
            </div>
            <ADefaultLogo
                :ready="logosReady"
                :selectedId="logoIdSelected"
                @saved="logoDefaultSaving = false"
                @saveing="logoDefaultSaving = true"
                v-if="logoIdSelected"
            />
        </label>

        <div class="d-flex">
            <ModelSelect
                :isDisabled="loadingLogos"
                :options="logoChoices"
                :value="logoIdSelected"
                @input="setLogo($event)"
                class="form-control flex-grow-1"
                id="logo"
                required="false"
            />
            <button
                :title="$t('images.create.logoRemove')"
                @click="setLogo(null)"
                class="btn btn-outline-secondary ml-2"
                v-if="logoIdSelected"
            >&times;
            </button>
        </div>
    </div>
</template>

<script>
    import SnackbarMixin from "../../mixins/SnackbarMixin";
    import {ModelSelect} from 'vue-search-select'
    import {Logo} from "../../service/canvas/elements/Logo";
    import ResourceLoadMixin from "../../mixins/ResourceLoadMixin";
    import {mapGetters} from "vuex";
    import PrepareSelectMixin from "../../mixins/PrepareSelectMixin";
    import Bar from "../../service/canvas/elements/Bar";
    import {LogoBlock} from "../../service/canvas/blocks/LogoBlock";
    import {Alignments, BarSchemes, BarTypes, LogoSublineRatios, LogoTypes} from "../../service/canvas/Constants";
    import ADefaultLogo from "../atoms/ADefaultLogo";

    export default {
        name: "MLogoBlock",
        components: {ADefaultLogo, ModelSelect},
        mixins: [ResourceLoadMixin, SnackbarMixin, PrepareSelectMixin],

        data() {
            return {
                logo: new Logo(),
                subline: new Bar(),
                block: new LogoBlock(),
                logoIdSelected: null,
                logoObjSelected: null,
                logoImage: null,
                logoChoices: [],
                loadingLogoImage: false,
                logoDefaultSaving: false,
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
            },
            colorSchema: {
                required: true,
            }
        },

        computed: {
            ...mapGetters({
                logos: 'logos/getAll',
                getLogoById: 'logos/getById',
                loadingLogos: 'logos/loading',
            }),

            logoIdDefault() {
                return this.$store.getters['user/object'].default_logo;
            },

            color() {
                return 'white' === this.colorSchema ? 'white' : 'green';
            },

            logosReady() {
                return !(this.loadingLogos || this.loadingLogoImage);
            },
        },

        created() {
            this.resourceLoad('logos')
                .then(() => this.populateLogosSelect())
                .then(() => this.setLogo(this.logoIdDefault));
        },

        methods: {
            draw() {
                if (!this.logoObjSelected) {
                    this.$emit('drawn', null);
                    return;
                }

                this.block.logo = this.drawLogo();
                this.block.type = this.logoObjSelected.type;

                if (LogoTypes.alternative !== this.logoObjSelected.type) {
                    this.block.subline = this.drawSubline();
                }

                const data = {
                    block: this.block.draw(),
                    id: this.logoIdSelected
                };

                this.$emit('drawn', data);
            },

            drawLogo() {
                this.logo.width = this.imageWidth;
                this.logo.height = this.imageHeight;
                this.logo.type = this.logoObjSelected.type;
                this.logo.logo = this.logoImage;

                return this.logo.draw()
            },

            drawSubline() {
                const fontSizeFactor = LogoSublineRatios[this.logoObjSelected.type].fontSize;
                const fontSize = this.logo.height * fontSizeFactor;

                this.subline.text = this.logoObjSelected.name;
                this.subline.alignment = Alignments.center;
                this.subline.schema = BarSchemes.magenta;
                this.subline.type = BarTypes.subline;
                this.subline.fontSize = fontSize;

                return this.subline.draw();
            },

            setLogo(logo) {
                this.logoIdSelected = logo;

                if (!logo) {
                    this.logoImage = null;
                    this.logoObjSelected = null;
                    this.draw();
                    return;
                }

                this.logoObjSelected = this.getLogoById(logo);
                this.loadingLogoImage = true;

                const img = new Image();
                img.onload = () => {
                    this.logoImage = img;
                    this.draw();
                    this.loadingLogoImage = false;
                };

                img.src = this.logoObjSelected[`src_${this.color}`];
            },

            populateLogosSelect() {
                this.logoChoices = this.prepareSelectData(
                    this.logos,
                    'id',
                    'name'
                );
            },
        },

        watch: {
            imageWidth() {
                this.draw();
            },
            imageHeight() {
                this.draw();
            },
            colorSchema() {
                this.setLogo(this.logoIdSelected);
            },
        },
    }
</script>

<style lang="scss" scoped>
    .custom-file-input {
        display: none;
    }
</style>
