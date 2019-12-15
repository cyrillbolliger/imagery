<template>
    <form autocomplete="off" class="m-logo-form">
        <ASelect
            :label="$t('logo.baseLogo')"
            :options="types"
            :required="true"
            :validation="validations.type"
            v-model="currentLogo.type"
        ></ASelect>

        <AInput
            :label="nameLabel"
            :required="true"
            :validation="validations.name"
            v-model.trim="currentLogo.name"
        ></AInput>

        <AMultiSelect
            :helptext="$t('logo.groupsHelptext')"
            :label="$t('logo.groups')"
            :options="groupsSelect"
            :required="true"
            :validation="validations.groups"
            v-model="groupsSelected"
        ></AMultiSelect>

        <AButtonWait
            :button-text="$t('logo.save')"
            :working="saving"
            :working-text="$t('logo.saving')"
            @buttonClicked="save"
        ></AButtonWait>

        <AButtonWait
            :button-text="$t('logo.remove')"
            :working="removing"
            :working-text="$t('logo.removing')"
            @buttonClicked="remove"
            button-class="btn btn-sm btn-link text-danger pl-0 mt-2"
            v-if="currentLogo.id"
        ></AButtonWait>

    </form>
</template>

<script>
    import AInput from "../atoms/AInput";
    import ASelect from "../atoms/ASelect";
    import AMultiSelect from "../atoms/AMultiSelect";
    import ResourceLoadMixin from "../../mixins/ResourceLoadMixin";
    import {mapGetters} from "vuex";
    import SnackbarMixin from "../../mixins/SnackbarMixin";
    import PrepareSelectMixin from "../../mixins/PrepareSelectMixin";
    import {required, maxLength} from 'vuelidate/lib/validators';
    import AButtonWait from "../atoms/AButtonWait";

    export default {
        name: "MLogoForm",
        components: {AButtonWait, ASelect, AInput, AMultiSelect},
        mixins: [ResourceLoadMixin, SnackbarMixin, PrepareSelectMixin],


        data() {
            return {
                saving: false,
                removing: false,
                savedLogo: null,
                groupsSelected: [],
                validations: {
                    name: {
                        rules: {
                            required,
                            maxLength: maxLength(80)
                        },
                        message: this.$t('validation.required')
                    },
                    type: {
                        rules: {
                            required,
                        },
                        message: this.$t('validation.required')
                    },
                    groups: {
                        rules: {
                            required,
                        },
                        message: this.$t('validation.required')
                    },
                },
                types: [
                    {value: 'alternative', text: 'Alternative'},
                    {value: 'gruene', text: 'GRÜNE'},
                    {value: 'gruene-verts', text: 'GRÜNE - Les VERTS'},
                    {value: 'verda', text: 'VERDA'},
                    {value: 'verdi', text: 'VERDI'},
                    {value: 'verts', text: 'VERTS'},
                ],
            }
        },


        props: {
            logo: {
                required: true,
                type: Object,
            }
        },


        computed: {
            ...mapGetters({
                logos: 'logos/getAll',
                getLogoById: 'logos/getById',
                groups: 'groups/getAll',
            }),

            nameLabel() {
                if (this.currentLogo.type === 'alternative') {
                    return this.$t('logo.name');
                } else {
                    return this.$t('logo.subline');
                }
            },

            currentLogo() {
                return this.savedLogo ? this.savedLogo : this.logo;
            },

            newLogo() {
                return !('id' in this.currentLogo);
            },

            groupsSelect() {
                return this.prepareSelectData(this.groups, 'id', 'tree_name');
            }
        },


        created() {
            this.resourceLoad('logos');
            this.resourceLoad('groups');
        },


        methods: {
            remove() {
                const confirmation = this.$t('logo.remove_confirmation', {logo: this.currentLogo.name});
                if (!confirm(confirmation)) {
                    return;
                }

                this.removing = true;
                this.$store.dispatch('logos/delete', this.currentLogo)
                    .finally(() => this.removing = false)
                    .then(() => {
                        this.$emit('removed', true);
                        this.snackSuccessDismiss(this.$t('logo.removed'))
                    })
                    .catch(error => {
                        this.snackErrorRetry(error, this.$t('logo.removing_failed'))
                            .then(() => this.remove());
                    });
            },

            save() {
                this.saving = true;

                this.saveLogo()
                // saving logos must come after saving the logo,
                // else we don't have a logo id on creation
                    .finally(() => this.saving = false)
                    .then(() => {
                        this.$emit('saved', true);
                        this.snackSuccessDismiss(this.$t('logo.saved'))
                    })
                    .catch(error => {
                        if (error.response && 422 === error.response.status) {
                            this.handleValidationError(error);
                        } else {
                            this.snackErrorRetry(error, this.$t('logo.saving_failed'))
                                .then(() => this.save());
                        }
                    });
            },

            handleValidationError(error) {
                this.snackErrorDismiss(error, this.$t('validation.double_check_form'));
            },

            saveLogo() {
                if (_.isEqual(this.currentLogo, this.getLogoById(this.currentLogo.id))) {
                    return new Promise(resolve => resolve());
                }

                if (this.newLogo) {
                    return this.$store.dispatch('logos/add', this.currentLogo)
                        .then(logo => this.savedLogo = logo);
                }

                return this.$store.dispatch('logos/update', this.currentLogo);
            },
        },

        watch: {
            groupsSelected(values) {
                const ids = values.map(item => item.value);
                this.currentLogo.groups = ids;
            }
        }
    }
</script>

<style scoped>

</style>
