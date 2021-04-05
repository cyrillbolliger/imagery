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
                :isDisabled="loadingLogos || ! userHasLogos"
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
        <div
            v-if="! loadingLogos && ! userHasLogos"
            class="alert alert-warning"
            role="alert"
        >{{$t('images.create.userHasNoLogos')}}
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
    import {LogoBlock} from "../../service/canvas/blocks/LogoBlock";
    import {ColorSchemes} from "../../service/canvas/Constants";
    import ADefaultLogo from "../atoms/ADefaultLogo";

    export default {
        name: "MLogoBlock",
        components: {ADefaultLogo, ModelSelect},
        mixins: [ResourceLoadMixin, SnackbarMixin, PrepareSelectMixin],

        data() {
            return {
                logo: new Logo(),
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
                logos: 'logosUsable/getAll',
                getLogoById: 'logosUsable/getById',
                loadingLogos: 'logosUsable/loading',
            }),

            logoIdDefault() {
                return this.$store.getters['user/object'].default_logo;
            },

            color() {
                return ColorSchemes.white === this.colorSchema ? 'white' : 'green';
            },

            logosReady() {
                return !(this.loadingLogos || this.loadingLogoImage);
            },

            userHasLogos() {
                return this.logoChoices.length > 0;
            }
        },

        created() {
            this.resourceLoad('logosUsable', true)
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

                const data = {
                    block: this.block.draw(),
                    id: this.logoIdSelected
                };

                this.$emit('drawn', data);
            },

            drawLogo() {
                this.logo.logo = this.logoImage;

                return this.logo.draw()
            },

            setLogo(logo) {
                this.logoIdSelected = logo;

                if (!logo) {
                    return this.removeLogo();
                }

                this.loadLogo();
            },

            removeLogo() {
                this.logoImage = null;
                this.logoObjSelected = null;
                this.loadingLogoImage = false;
                this.draw();
            },

            loadLogo() {
                this.loadingLogoImage = true;
                this.logoObjSelected = this.getLogoById(this.logoIdSelected);

                this.logo.type = this.logoObjSelected.type;
                this.logo.imageWidth = this.imageWidth;
                this.logo.imageHeight = this.imageHeight;

                const img = new Image();
                img.onload = () => {
                    this.logoImage = img;
                    this.draw();
                    this.loadingLogoImage = false;
                };

                img.src = this.logoObjSelected[`src_${this.color}`]+`/${this.logo.logoWidth}`;
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
                this.setLogo(this.logoIdSelected);
            },
            imageHeight() {
                this.setLogo(this.logoIdSelected);
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
