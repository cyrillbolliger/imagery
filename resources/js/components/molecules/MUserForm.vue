<template>
    <form autocomplete="off" class="m-user-form">
        <h5>{{$t('user.name')}}</h5>
        <AInput
            :label="$t('user.first_name')"
            :required="true"
            :validation="validations.first_name"
            v-model.trim="currentUser.first_name"
        ></AInput>
        <AInput
            :label="$t('user.last_name')"
            :required="true"
            :validation="validations.last_name"
            v-model.trim="currentUser.last_name"
        ></AInput>
        <h5 class="pt-3">{{$t('user.login')}}</h5>
        <AInput
            :label="$t('user.email')"
            :required="true"
            type="email"
            :validation="validations.email"
            v-model.trim="currentUser.email"
        ></AInput>
        <APasswordSet
            v-model.trim="currentUser.password"
            :required="newUser"
        ></APasswordSet>

        <h5 class="pt-3">{{$t('user.misc_fields')}}</h5>
        <ASelect
            :label="$t('user.language')"
            :options="options"
            :required="true"
            :validation="validations.language"
            v-model="currentUser.lang"
        ></ASelect>
        <ASelect
            :helptext="$t('user.managed_by_helptext')"
            :label="$t('user.managed_by')"
            :options="groupsSelect"
            :required="true"
            :validation="validations.managed_by"
            v-model="currentUser.managed_by"
        ></ASelect>

        <h5 class="pt-3">{{$t('user.privileges')}}</h5>
        <AFormGroup v-if="amISuperAdmin">
            <template #label>
                {{ $t('user.super_admin') }}
            </template>
            <template #input>
                <ACheckbox
                    :label="$t('user.super_admin_desc')"
                    v-model="currentUser.super_admin"
                ></ACheckbox>
            </template>
        </AFormGroup>
        <AFormGroup v-if="!currentUser.super_admin">
            <template #label>
                {{ $t('user.group_privileges') }}
            </template>
            <template #input>
                <MGroupTree
                    :groups="groups"
                    :roles="detachedRoles"
                    :user="currentUser"
                    @change="updateRoles"
                    v-if="!rolesLoading"
                ></MGroupTree>
                <div class="d-flex justify-content-center"
                     v-if="rolesLoading">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
            </template>
        </AFormGroup>

        <AButtonWait
            :button-text="$t('user.save')"
            :working="saving"
            :working-text="$t('user.saving')"
            @buttonClicked="save"
        ></AButtonWait>

        <AButtonWait
            :button-text="$t('user.remove')"
            :working="removing"
            :working-text="$t('user.removing')"
            @buttonClicked="remove"
            button-class="btn btn-sm btn-link text-danger pl-0 mt-2"
            v-if="currentUser.id"
        ></AButtonWait>

    </form>
</template>

<script>
    import AInput from "../atoms/AInput";
    import APasswordSet from "../atoms/APasswordSet";
    import ACheckbox from "../atoms/ACheckbox";
    import AFormGroup from "../atoms/AFormGroup";
    import ASelect from "../atoms/ASelect";
    import ResourceLoadMixin from "../../mixins/ResourceLoadMixin";
    import {mapGetters} from "vuex";
    import Api from "../../service/Api";
    import SnackbarMixin from "../../mixins/SnackbarMixin";
    import MGroupTree from "./MGroupTree";
    import PrepareSelectMixin from "../../mixins/PrepareSelectMixin";
    import {required, email, maxLength} from 'vuelidate/lib/validators';
    import AButtonWait from "../atoms/AButtonWait";

    export default {
        name: "MUserForm",
        components: {AButtonWait, MGroupTree, ASelect, AFormGroup, ACheckbox, AInput, APasswordSet},
        mixins: [ResourceLoadMixin, SnackbarMixin, PrepareSelectMixin],


        data() {
            return {
                options: [
                    {value: 'de', text: this.$t('languages.de')},
                    {value: 'fr', text: this.$t('languages.fr')},
                    {value: 'en', text: this.$t('languages.en')},
                ],
                rolesLoading: false,
                rolesLoadingPromise: null,
                groupsLoadingPromise: null,
                roles: [],
                editedRoles: [],
                adminOf: [],
                userOf: [],
                saving: false,
                removing: false,
                savedUser: null,
                validations: {
                    first_name: {
                        rules: {
                            required,
                            maxLength: maxLength(80)
                        },
                        message: this.$t('validation.required')
                    },
                    last_name: {
                        rules: {
                            required,
                            maxLength: maxLength(80)
                        },
                        message: this.$t('validation.required')
                    },
                    email: {
                        rules: {
                            required,
                            email,
                            maxLength: maxLength(80)
                        },
                        messages: {
                            required: this.$t('validation.required'),
                            email: this.$t('validation.email'),
                            maxLength: this.$t('validation.max_length', {max: 80}),
                            unique: this.$t('user.email_exists'),
                        }
                    },
                    language: {
                        rules: {
                            required,
                        },
                        message: this.$t('validation.required')
                    },
                    managed_by: {
                        rules: {
                            required,
                        },
                        message: this.$t('validation.required')
                    },
                }
            }
        },


        props: {
            user: {
                required: true,
                type: Object,
            }
        },


        computed: {
            ...mapGetters({
                groups: 'groups/getAll',
                getGroupById: 'groups/getById',
                getUserById: 'users/getById',
            }),
            amISuperAdmin() {
                return this.$store.getters['user/object'].super_admin;
            },
            groupsSelect() {
                return this.prepareSelectData(this.groups, 'id', 'tree_name');
            },
            detachedRoles() {
                return _.cloneDeep(this.roles);
            },
            currentUser() {
                return this.savedUser ? this.savedUser : this.user;
            },
            newUser() {
                return !('id' in this.currentUser);
            },
        },


        created() {
            this.groupsLoadingPromise = this.resourceLoad('groups');
            this.rolesLoadingPromise = this.rolesLoad();
        },


        methods: {
            rolesLoad() {
                this.rolesLoading = true;

                if (this.newUser) {
                    return new Promise(resolve => resolve())
                        .then(() => this.setRolesReady());
                }

                return Api().get(`users/${this.currentUser.id}/roles`)
                    .then(response => response.data)
                    .then(roles => {
                        this.roles = roles;
                        this.editedRoles = _.cloneDeep(roles);
                    })
                    .then(() => this.setRolesReady())
                    .catch(reason => {
                        this.snackErrorRetry(reason, this.$t('user.roles_loading_failed'))
                            .then(() => this.rolesLoad());
                    });
            },

            setRolesReady() {
                Promise.all([this.groupsLoadingPromise, this.rolesLoadingPromise])
                    .then(() => this.rolesLoading = false);
            },

            updateRoles(data) {
                this.editedRoles = data;
            },

            remove() {
                const full_name = `${this.currentUser.first_name} ${this.currentUser.last_name}`;
                const confirmation = this.$t('user.remove_confirmation', {user: full_name});
                if (!confirm(confirmation)) {
                    return;
                }

                this.removing = true;
                this.$store.dispatch('users/delete', this.currentUser)
                    .finally(() => this.removing = false)
                    .then(() => {
                        this.$emit('removed', true);
                        this.snackSuccessDismiss(this.$t('user.removed'))
                    })
                    .catch(error => {
                        this.snackErrorRetry(error, this.$t('user.removing_failed'))
                            .then(() => this.remove());
                    });
            },

            save() {
                this.saving = true;

                this.saveUser()
                // saving roles must come after saving the user,
                // else we don't have a user id on creation
                    .then(() => this.saveRoles())
                    .finally(() => this.saving = false)
                    .then(() => {
                        this.$emit('saved', true);
                        this.snackSuccessDismiss(this.$t('user.saved'))
                    })
                    .catch(error => {
                        if (error.response && 422 === error.response.status) {
                            this.handleValidationError(error);
                        } else {
                            this.snackErrorRetry(error, this.$t('user.saving_failed'))
                                .then(() => this.save());
                        }
                    });
            },

            handleValidationError(error) {
                if ('email' in error.response.data.errors
                    && error.response.data.errors.email[0] === 'The email has already been taken.') {
                    const email = this.user.email;
                    this.$set(this.validations.email.rules, 'unique', (value) => value !== email);
                }

                this.snackErrorDismiss(error, this.$t('validation.double_check_form'));
            },

            saveUser() {
                if (_.isEqual(this.currentUser, this.getUserById(this.currentUser.id))) {
                    return new Promise(resolve => resolve());
                }

                if (this.newUser) {
                    return this.$store.dispatch('users/add', this.currentUser)
                        .then(user => this.savedUser = user);
                }

                return this.$store.dispatch('users/update', this.currentUser);
            },

            saveRoles() {
                const before = this.roles.map(role => role.group_id);
                const after = this.editedRoles.map(role => role.group_id);

                const toAdd = after.filter(id => !before.includes(id));
                const toDelete = before.filter(id => !after.includes(id));
                const toUpdate = after.filter(id => before.includes(id));

                const rolesToAdd = this.editedRoles.filter(role => toAdd.includes(role.group_id));
                const rolesToDelete = this.roles.filter(role => toDelete.includes(role.group_id));
                const rolesToUpdate = this.editedRoles.filter(edited => {
                    if (toUpdate.includes(edited.group_id)) {
                        const original = this.roles.filter(o => o.group_id === edited.group_id)[0];
                        return edited.admin !== original.admin;
                    }
                });

                let promises = [];

                rolesToAdd.forEach(role => {
                    role.user_id = this.currentUser.id; // needed for newly created users
                    promises.push(this.roleAdd(role))
                });

                rolesToDelete.forEach(
                    role => promises.push(this.roleDelete(role))
                );

                rolesToUpdate.forEach(
                    role => promises.push(this.roleUpdate(role))
                );

                return Promise.all(promises);
            },

            getById(id, objects) {
                const hits = objects.find(obj => obj.id === id);
                return hits && hits.length ? hits[0] : null;
            },

            roleAdd(role) {
                return Api().post(`users/${role.user_id}/roles`, role)
                    .then(r => {
                        this.roles.push(r.data);

                        const idx = this.editedRoles.findIndex(edited => edited.group_id === role.group_id);
                        this.editedRoles.splice(idx, 1, r.data);
                    });
            },

            roleDelete(role) {
                return Api().delete(`users/${role.user_id}/roles/${role.id}`)
                    .then(() => {
                        const idx = this.roles.findIndex(r => r.id === role.id);
                        this.roles.splice(idx, 1);
                    });
            },

            roleUpdate(role) {
                return Api().put(`users/${role.user_id}/roles/${role.id}`, role)
                    .then(updatedRole => {
                        const idx = this.roles.findIndex(r => r.id === role.id);
                        this.roles.splice(idx, 1, updatedRole.data);
                    });
            }
        },
    }
</script>

<style scoped>

</style>
