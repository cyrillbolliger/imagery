<template>
    <div>
        <MHeader>{{$t('users.index.title')}}</MHeader>
        <ODataTable
            :headers="headers"
            :loading="loading"
            :rows="users"
            actionKey="id"
            @details="navigateToUsersEdit($event)"
            sortBy="first_name"
            @newEntry="navigateToUsersCreate()"
        ></ODataTable>
        <ODialog
            :title="dialogTitle"
            @close="navigateToList"
            v-if="dialogUser"
        >
            <template #default>
                <OUser
                    :user="dialogUser"
                    @close="navigateToList"
                ></OUser>
            </template>
        </ODialog>
    </div>
</template>

<script>
    import MHeader from "../molecules/MHeader";
    import ODataTable from "../organisms/ODataTable";
    import ODialog from "../organisms/ODialog";
    import {mapGetters} from "vuex";
    import ResourceLoadMixin from "../../mixins/ResourceLoadMixin";
    import SnackbarMixin from "../../mixins/SnackbarMixin";
    import OUser from "../organisms/OUser";
    import cloneDeep from 'lodash/cloneDeep';

    export default {
        name: "UserIndex",
        components: {OUser, ODialog, ODataTable, MHeader},


        data() {
            return {
                headers: [
                    {label: this.$t('user.first_name'), key: 'first_name', sortable: true},
                    {label: this.$t('user.last_name'), key: 'last_name', sortable: true},
                    {label: this.$t('user.email'), key: 'email', sortable: true},
                ],
                dialogUser: null,
                createUser: false,
            }
        },


        computed: {
            ...mapGetters({
                users: 'users/getAll',
                getUserById: 'users/getById',
                loading: 'users/loading',
            }),
            dialogTitle() {
                return this.dialogUser && !this.createUser ?
                    `${this.dialogUser.first_name} ${this.dialogUser.last_name}` :
                    this.$t('users.index.create');
            }
        },


        props: {
            userId: {
                default: null
            },
            create: {
                default: false
            }
        },


        created() {
            const loading = this.resourceLoad('users');

            // navigate directly to user, if one is set
            loading.then(() => {
                if (this.isRouteUserCreate()) {
                    this.dialogShowCreate();
                } else if (this.isRouteUserEdit()) {
                    this.dialogShowEdit(parseInt(this.userId));
                } else {
                    this.dialogClose();
                }
            });
        },


        methods: {
            isRouteUserEdit() {
                return this.userId !== null;
            },

            isRouteUserCreate() {
                return this.create;
            },

            navigateToUsersEdit(id) {
                this.$router.push({name: 'usersEdit', params: {userId: id}});
            },

            navigateToUsersCreate() {
                this.$router.push({name: 'usersCreate'});
            },

            navigateToList() {
                this.$router.push({name: 'usersAll'});
            },

            dialogShowEdit(id) {
                this.createUser = false;

                // clone user so changes are only pushed back
                // into the store when saving
                this.dialogUser = cloneDeep(this.getUserById(id));

                if (null === this.dialogUser) {
                    this.snackErrorRetry(
                        `No user with id ${id} in store.`,
                        this.$t('user.not_found')
                    ).then(() => this.resourceLoad('users'))
                        .then(() => this.dialogShowEdit(id));
                }
            },

            dialogClose() {
                this.dialogUser = null;
                this.createUser = false;
            },

            dialogShowCreate() {
                this.createUser = true;
                this.dialogUser = {};
            }
        },


        watch: {
            userId(value) {
                if (value) {
                    this.dialogShowEdit(parseInt(value));
                } else {
                    this.dialogClose();
                }
            },
            create(value) {
                if (value) {
                    this.dialogShowCreate();
                } else {
                    this.dialogClose();
                }
            },
        },


        mixins: [ResourceLoadMixin, SnackbarMixin],
    }
</script>

<style scoped>

</style>
