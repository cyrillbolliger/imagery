<template>
    <form autocomplete="off" class="m-group-form">
        <AInput
            :label="$t('group.name')"
            :required="true"
            :validation="validations.name"
            v-model.trim="currentGroup.name"
        />

        <ASelect
            v-if="!isRootGroup && canEditGroup(currentGroup.parent_id)"
            :label="$t('group.parent')"
            :options="groupsSelect"
            :required="true"
            :validation="validations.parent_id"
            v-model.number="currentGroup.parent_id"
        />

        <AButtonWait
            :button-text="$t('group.save')"
            :working="saving"
            :working-text="$t('group.saving')"
            @buttonClicked="save"
        />

        <AButtonWait
            :button-text="$t('group.remove')"
            :working="removing"
            :working-text="$t('group.removing')"
            @buttonClicked="remove"
            button-class="btn btn-sm btn-link text-danger pl-0 mt-2"
            v-if="currentGroup.id"
        />

    </form>
</template>

<script>
    import AInput from "../atoms/AInput";
    import AFormGroup from "../atoms/AFormGroup";
    import ASelect from "../atoms/ASelect";
    import ResourceLoadMixin from "../../mixins/ResourceLoadMixin";
    import {mapGetters} from "vuex";
    import SnackbarMixin from "../../mixins/SnackbarMixin";
    import MGroupTree from "./MGroupTree";
    import PrepareSelectMixin from "../../mixins/PrepareSelectMixin";
    import {required, maxLength} from 'vuelidate/lib/validators';
    import AButtonWait from "../atoms/AButtonWait";
    import isEqual from 'lodash/isEqual';

    export default {
        name: "MGroupForm",
        components: {AButtonWait, MGroupTree, ASelect, AFormGroup, AInput},
        mixins: [ResourceLoadMixin, SnackbarMixin, PrepareSelectMixin],


        data() {
            return {
                saving: false,
                removing: false,
                savedGroup: null,
                validations: {
                    name: {
                        rules: {
                            required,
                            maxLength: maxLength(80)
                        },
                        message: this.$t('validation.required')
                    },
                    parent_id: {
                        rules: {
                            required,
                        },
                        message: this.$t('validation.required')
                    },
                }
            }
        },


        props: {
            group: {
                required: true,
                type: Object,
            }
        },


        computed: {
            ...mapGetters({
                groups: 'groups/getAll',
                getGroupById: 'groups/getById',
            }),
            groupsSelect() {
                // todo: remove self and child groups
                return this.prepareSelectData(this.groups, 'id', 'tree_name');
            },
            currentGroup() {
                return this.savedGroup ? this.savedGroup : this.group;
            },
            newGroup() {
                return !('id' in this.currentGroup);
            },
            isRootGroup() {
                return this.currentGroup.parent_id === null;
            },
        },


        created() {
            this.resourceLoad('groups');
        },


        methods: {
            remove() {
                const confirmation = this.$t('group.remove_confirmation', {group: this.currentGroup.name});
                if (!confirm(confirmation)) {
                    return;
                }

                this.removing = true;
                this.$store.dispatch('groups/delete', this.currentGroup)
                    .finally(() => this.removing = false)
                    .then(() => {
                        this.$emit('removed', true);
                        this.snackSuccessDismiss(this.$t('group.removed'))
                    })
                    .catch(error => {
                        this.snackErrorRetry(error, this.$t('group.removing_failed'))
                            .then(() => this.remove());
                    });
            },

            save() {
                this.saving = true;

                this.saveGroup()
                // saving logos must come after saving the group,
                // else we don't have a group id on creation
                    .finally(() => this.saving = false)
                    .then(() => {
                        this.$emit('saved', true);
                        this.snackSuccessDismiss(this.$t('group.saved'))
                    })
                    .catch(error => {
                        if (error.response && 422 === error.response.status) {
                            this.handleValidationError(error);
                        } else {
                            this.snackErrorRetry(error, this.$t('group.saving_failed'))
                                .then(() => this.save());
                        }
                    });
            },

            handleValidationError(error) {
                this.snackErrorDismiss(error, this.$t('validation.double_check_form'));
            },

            saveGroup() {
                if (isEqual(this.currentGroup, this.getGroupById(this.currentGroup.id))) {
                    return new Promise(resolve => resolve());
                }

                if (this.newGroup) {
                    return this.$store.dispatch('groups/add', this.currentGroup)
                        .then(group => this.savedGroup = group);
                }

                return this.$store.dispatch('groups/update', this.currentGroup);
            },

            canEditGroup(id) {
                return Boolean(this.getGroupById(id));
            }
        },
    }
</script>

<style scoped>

</style>
