<template>
    <form autocomplete="off" class="m-logo-form">
        <ASelect
            :label="$t('logo.baseLogo')"
            :options="types"
            :required="true"
            :validation="validations.type"
            v-model="currentLogo.type"
        />

        <AInput
            :label="nameLabel"
            :required="true"
            :validation="validations.name"
            v-model.trim="currentLogo.name"
        />

        <AMultiSelect
            :helptext="$t('logo.groupsHelptext')"
            :label="$t('logo.groups')"
            :options="groupsSelect"
            :required="true"
            :validation="validations.groups"
            v-model="groupsSelected"
            :loading="groupsSelectedLoading"
        />

        <AButtonWait
            :button-text="$t('logo.save')"
            :working="saving"
            :working-text="$t('logo.saving')"
            @buttonClicked="save"
        />

        <AButtonWait
            :button-text="$t('logo.remove')"
            :working="removing"
            :working-text="$t('logo.removing')"
            @buttonClicked="remove"
            button-class="btn btn-sm btn-link text-danger pl-0 mt-2"
            v-if="currentLogo.id"
        />

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
    import isEqual from 'lodash/isEqual';
    import {LogoTypes} from "../../service/canvas/Constants";

    export default {
        name: "MLogoForm",
        components: {AButtonWait, ASelect, AInput, AMultiSelect},
        mixins: [ResourceLoadMixin, SnackbarMixin, PrepareSelectMixin],


        data() {
            return {
                saving: false,
                removing: false,
                savedLogo: null,
                groupsSelectedLoading: true,
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
                    {value: LogoTypes.alternative, text: 'Alternative Zug'},
                    {value: LogoTypes['alternative-risch'], text: 'Alternative Risch'},
                    {value: LogoTypes.gruene, text: 'GRÜNE'},
                    {value: LogoTypes['gruene-vert-e-s'], text: 'GRÜNE - Les VERT.E.S'},
                    {value: LogoTypes['gruene-verts'], text: 'GRÜNE - Les VERTS'},
                    {value: LogoTypes.verda, text: 'VERDA'},
                    {value: LogoTypes.verdi, text: 'VERDI'},
                    {value: LogoTypes['vert-e-s'], text: 'VERT-E-S'},
                    {value: LogoTypes.verts, text: 'VERTS'},
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
                logos: 'logosManageable/getAll',
                getLogoById: 'logosManageable/getById',
                groups: 'groups/getAll',
                getGroupById: 'groups/getById',
            }),

            nameLabel() {
                if (this.currentLogo.type === 'alternative' || this.currentLogo.type === 'alternative-risch') {
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
            Promise.all([
                this.resourceLoad('logosManageable'),
                this.resourceLoad('groups')
            ]).then(this.populateGroupsSelected);
        },

        methods: {
            remove() {
                const confirmation = this.$t('logo.remove_confirmation', {logo: this.currentLogo.name});
                if (!confirm(confirmation)) {
                    return;
                }

                this.removing = true;
                this.$store.dispatch('logosManageable/delete', this.currentLogo)
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
                if (isEqual(this.currentLogo, this.getLogoById(this.currentLogo.id))) {
                    return new Promise(resolve => resolve());
                }

                if (this.newLogo) {
                    return this.$store.dispatch('logosManageable/add', this.currentLogo)
                        .then(logo => this.savedLogo = logo);
                }

                return this.$store.dispatch('logosManageable/update', this.currentLogo);
            },

            populateGroupsSelected() {
                const groupIds = this.currentLogo.groups;
                let selected = [];

                for (const id of groupIds) {
                    selected.push(this.getGroupById(id));
                }

                this.groupsSelected = this.prepareSelectData(selected, 'id', 'tree_name');
                this.groupsSelectedLoading = false;
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
