<template>
    <form autocomplete="off" class="m-user-form">
        <h5>{{$t('user.name')}}</h5>
        <AInput
            :label="$t('user.first_name')"
            :required="true"
            v-model="user.first_name"
        ></AInput>
        <AInput
            :label="$t('user.last_name')"
            :required="true"
            v-model="user.last_name"
        ></AInput>
        <h5 class="pt-3">{{$t('user.login')}}</h5>
        <AInput
            :label="$t('user.email')"
            :required="true"
            type="email"
            v-model="user.email"
        ></AInput>
        <APasswordSet
            v-model="user.password"
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
                    :roles="roles"
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
                userOf: []
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
            }),
            amISuperAdmin() {
                return true;
            },
            groupsSelect() {
                return this.prepareSelectData(this.groups, 'id', 'tree_name');
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
                    .then(roles => this.roles = roles)
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
        },
    }
</script>

<style scoped>

</style>
