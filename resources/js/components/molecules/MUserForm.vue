<template>
    <form autocomplete="off" class="m-user-form">
        <h5>{{$t('user.name')}}</h5>
        <AInput
            :label="$t('user.first_name')"
            :required="true"
            :validation="validations.first_name"
            v-model.trim="user.first_name"
        ></AInput>
        <AInput
            :label="$t('user.last_name')"
            :required="true"
            v-model.trim="user.last_name"
        ></AInput>
        <h5 class="pt-3">{{$t('user.login')}}</h5>
        <AInput
            :label="$t('user.email')"
            :required="true"
            type="email"
            v-model.trim="user.email"
        ></AInput>
        <APasswordSet
            v-model.trim="user.password"
        ></APasswordSet>

        <h5 class="pt-3">{{$t('user.misc_fields')}}</h5>
        <ASelect
            :label="$t('user.language')"
            :options="options"
            :required="true"
            v-model="user.lang"
        ></ASelect>
        <ASelect
            :helptext="$t('user.managed_by_helptext')"
            :label="$t('user.managed_by')"
            :options="groupsSelect"
            :required="true"
            v-model="user.managed_by"
        ></ASelect>

        <h5 class="pt-3">{{$t('user.privileges')}}</h5>
        <AFormGroup v-if="amISuperAdmin">
            <template #label>
                {{ $t('user.super_admin') }}
            </template>
            <template #input>
                <ACheckbox
                    :label="$t('user.super_admin_desc')"
                    v-model="user.super_admin"
                ></ACheckbox>
            </template>
        </AFormGroup>
        <AFormGroup v-if="!user.super_admin">
            <template #label>
                {{ $t('user.group_privileges') }}
            </template>
            <template #input>
                <MGroupTree
                    :groups="groups"
                    :roles="detachedRoles"
                    :user="user"
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

        <div class="d-flex align-items-center">
            <button
                :disabled="saving"
                @click.prevent="save"
                class="btn btn-primary"
            >{{$t('user.save')}}
            </button>
            <span class="d-block ml-3" v-if="saving">{{$t('user.saving')}}</span>
            <div class="spinner-border spinner-border-sm text-primary ml-3" role="status" v-if="saving">
                <span class="sr-only">Saving...</span>
            </div>
        </div>
    </form>
</template>

<script>
    import AInput from "../atoms/AInput";
    import APasswordSet from "../atoms/APasswordSet";
    import ACheckbox from "../atoms/ACheckbox";
    import AFormGroup from "../atoms/AFormGroup";
    import ASelect from "../atoms/ASelect";
    import AMultiSelect from "../atoms/AMultiSelect";
    import ResourceLoadMixin from "../../mixins/ResourceLoadMixin";
    import {mapGetters} from "vuex";
    import Api from "../../service/Api";
    import SnackbarMixin from "../../mixins/SnackbarMixin";
    import MGroupTree from "./MGroupTree";
    import PrepareSelectMixin from "../../mixins/PrepareSelectMixin";
    import {required, minLength, maxLength} from 'vuelidate/lib/validators';

    export default {
        name: "MUserForm",
        components: {MGroupTree, ASelect, AFormGroup, ACheckbox, AInput, APasswordSet, AMultiSelect},
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
                validations: {
                    first_name: {
                        rules: {
                            required,
                            maxLength: maxLength(80)
                        },
                        message: this.$t('validation.required')
                    },
                }
            }
        },


        props: {
            user: {
                required: true,
            }
        },


        computed: {
            ...mapGetters({
                groups: 'groups/getAll',
                getGroupById: 'groups/getById',
                getUserById: 'users/getById',
            }),
            amISuperAdmin() {
                return true;
            },
            groupsSelect() {
                return this.prepareSelectData(this.groups, 'id', 'tree_name');
            },
            detachedRoles() {
                return _.cloneDeep(this.roles);
            },
        },


        created() {
            this.groupsLoadingPromise = this.resourceLoad('groups');
            this.rolesLoadingPromise = this.rolesLoad();
        },


        methods: {
            rolesLoad() {
                this.rolesLoading = true;
                return Api().get(`users/${this.user.id}/roles`)
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

            save() {
                this.saving = true;

                Promise.all([this.saveUser(), this.saveRoles()])
                    .finally(() => this.saving = false)
                    .then(() => this.$emit('saved', true))
                    .catch(reason => {
                        this.snackErrorRetry(reason, this.$t('user.saving_failed'))
                            .then(() => this.save());
                    });
            },

            saveUser() {
                if (_.isEqual(this.user, this.getUserById(this.user.id))) {
                    return new Promise(resolve => resolve());
                }

                return this.$store.dispatch('users/update', this.user);
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

                rolesToAdd.forEach(
                    role => promises.push(this.roleAdd(role))
                );

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
                return Api().post(`users/${this.user.id}/roles`, role)
                    .then(r => {
                        this.roles.push(r.data);

                        const idx = this.editedRoles.findIndex(edited => edited.group_id === role.group_id);
                        this.editedRoles.splice(idx, 1, r.data);
                    });
            },

            roleDelete(role) {
                return Api().delete(`users/${this.user.id}/roles/${role.id}`)
                    .then(() => {
                        const idx = this.roles.findIndex(r => r.id === role.id);
                        this.roles.splice(idx, 1);
                    });
            },

            roleUpdate(role) {
                return Api().put(`users/${this.user.id}/roles/${role.id}`, role)
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
