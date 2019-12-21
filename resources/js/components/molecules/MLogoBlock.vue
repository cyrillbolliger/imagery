<template>
    <div class="form-group">
        <label for="logo" class="mb-0 d-flex align-items-center">
            {{$t('images.create.logo')}}
            <div
                v-if="loadingLogos || loadingLogoImage"
                class="spinner-border spinner-border-sm text-primary ml-2"
                role="status">
            </div>
        </label>

        <ModelSelect
            :options="logoChoices"
            :value="logoIdSelected"
            :isDisabled="loadingLogos"
            @input="setLogo($event)"
            class="form-control"
            id="logo"
            required="false"/>
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

    export default {
        name: "MLogoBlock",
        components: {ModelSelect},
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
            color() {
                return 'white' === this.colorSchema ? 'white' : 'green';
            }
        },

        created() {
            this.resourceLoad('logos')
                .then(() => this.populateLogosSelect());
        },

        mounted() {
            this.$nextTick(() => {
                // we don't know the user before $nextTick
                const logo = this.$store.getters['user/object'].default_logo;

                this.setLogo(logo);
            });
        },

        methods: {
            draw() {
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
