<template>
    <form autocomplete="off" class="m-user-form">
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
        <AInput
            :label="$t('user.email')"
            :required="true"
            type="email"
            v-model="user.email"
        ></AInput>
        <APasswordSet
            v-model="user.password"
        ></APasswordSet>
        <AFormGroup>
            <template #label>
                {{ $t('user.super_admin') }}
            </template>
            <template #input>
                <ACheckbox
                    :label="$t('user.super_admin_desc')"
                    v-if="amISuperAdmin"
                    v-model="user.super_admin"
                ></ACheckbox>
            </template>
        </AFormGroup>
        <ASelect
            :label="$t('user.language')"
            :options="options"
            :required="true"
            v-model="user.lang"
        ></ASelect>
        <ASelect
            :label="$t('user.managed_by')"
            :options="groupsSelect"
            :required="true"
            v-model="user.managed_by"
            :helptext="$t('user.managed_by_helptext')"
        ></ASelect>
        <AMultiSelect
            :helptext="$t('user.admin_of_helptext')"
            :label="$t('user.admin_of')"
            :options="groupsAdminOfSelect"
            :required="false"
            @input="updateUserOfSelection"
            v-if="!user.super_admin"
            v-model="adminOf"
        ></AMultiSelect>
        <AMultiSelect
            :helptext="$t('user.user_of_helptext')"
            :label="$t('user.user_of')"
            :options="groupsUserOfSelect"
            :required="false"
            v-if="!user.super_admin"
            v-model="userOf"
        ></AMultiSelect>

        <!-- todo: default logo (if role; only role logos selectable) -->

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

    export default {
        name: "MUserForm",
        components: {ASelect, AFormGroup, ACheckbox, AInput, APasswordSet, AMultiSelect},
        mixins: [ResourceLoadMixin, SnackbarMixin],


        data() {
            return {
                options: [
                    {value: 'de', text: this.$t('languages.de')},
                    {value: 'fr', text: this.$t('languages.fr')},
                    {value: 'en', text: this.$t('languages.en')},
                ],
                rolesLoading: false,
                rolesLodingPromise: null,
                groupsLoadingPromise: null,
                roles: [],
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
                logos: 'logos/getAll',
            }),
            amISuperAdmin() {
                return true;
            },
            adminGroupIds() {
                return this.adminOf.map(item => item.value);
            },
            groupsSelect() {
                return this.groupsPrepareSelectData(this.groups);
            },
            groupsAdminOfSelect() {
                // get all groups that are not below the already selected groups
                const groups = this.groups.filter(
                    ({root_path}) => !this.groupDescendsFrom(root_path, this.adminGroupIds)
                );

                return this.groupsPrepareSelectData(groups);
            },
            groupsUserOfSelect() {
                // all groups without the groups the user is admin of
                return this.groupsSelect
                    .filter(item => !this.adminGroupIds.includes(item.value));
            }
        },


        created() {
            this.groupsLoadingPromise = this.resourceLoad('groups');
            this.rolesLodingPromise = this.rolesLoad();

            this.resourceLoad('logos');
        },


        methods: {
            groupsPrepareSelectData(groups) {
                return groups.map(group => ({
                        value: group.id,
                        text: group.tree_name
                    })
                ).sort((a, b) => a.text.localeCompare(b.text));
            },

            rolesLoad() {
                this.rolesLoading = true;
                return Api().get(`users/${user.id}/roles`)
                    .then(response => response.data)
                    .then(roles => this.roles = roles)
                    .then(() => this.loadRolesGroups())
                    .finally(() => this.rolesLoading = false)
                    .catch(reason => {
                        this.snackErrorRetry(reason, this.$t('user.roles_loading_failed'))
                            .then(() => this.rolesLoad());
                    });
            },

            loadRolesGroups() {
                Promise.all([this.groupsLoadingPromise, this.rolesLodingPromise])
                    .then(() => {
                        let adminGroups = [];
                        let userGroups = [];

                        for (let role of this.roles) {
                            const group = this.getGroupById(role.group_id);

                            if (!group) {
                                // we don't have the rights to change this group
                                continue;
                            }

                            if (role.admin) {
                                adminGroups.push(group);
                            } else {
                                userGroups.push(group);
                            }
                        }

                        this.adminOf = this.groupsPrepareSelectData(adminGroups);
                        this.userOf = this.groupsPrepareSelectData(userGroups);
                    });
            },

            updateUserOfSelection() {
                this.userOf = this.userOf
                    .filter(item => !this.adminGroupIds.includes(item.value));
            },

            groupDescendsFrom(group, possibleAncestorsIds) {
                return group.root_path.some(
                    groupId => possibleAncestorsIds.includes(groupId)
                );
            },
        },


        watch: {

            adminOf() {
                // todo: ensure we don't have any descending groups (only the top most)
            }
        },

    }
</script>

<style scoped>

</style>
