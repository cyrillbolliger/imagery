<template>
    <div class="col">
        <MHeader>{{$t('users.index.title')}}</MHeader>
        <ODataTable
            :headers="headers"
            :loading="loading"
            :rows="users"
            actionKey="id"
            @details="navigateToSingleUser($event)"
            sortBy="first_name"
        ></ODataTable>
        <ODialog
            :title="dialogTitle"
            @close="navigateToList"
            v-if="dialogUser"
        >
            <template #default>
                <OUser :user="dialogUser"></OUser>
            </template>
            <template #footer>
                Footer
            </template>
        </ODialog>
    </div>
</template>

<script>
    import MHeader from "../molecules/MHeader";
    import ODataTable from "../organisms/ODataTable";
    import ODialog from "../organisms/ODialog";
    import MUserForm from "../molecules/MUserForm";
    import {mapGetters} from "vuex";
    import ResourceLoadMixin from "../../mixins/ResourceLoadMixin";
    import SnackbarMixin from "../../mixins/SnackbarMixin";
    import OUser from "../organisms/OUser";

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
            }
        },


        computed: {
            ...mapGetters({
                users: 'users/getAll',
                getUserById: 'users/getById',
                loading: 'users/loading',
            }),
            dialogTitle() {
                return this.dialogUser ?
                    `${this.dialogUser.first_name} ${this.dialogUser.last_name}` :
                    this.$t('users.index.create');
            }
        },


        props: {
            userId: {
                default: null
            }
        },


        created() {
            const loading = this.resourceLoad('users');

            // navigate directly to user, if one is set
            loading.then(() => {
                if (this.isSingleUserRoute()) {
                    this.dialogShow(parseInt(this.userId));
                } else {
                    this.dialogClose();
                }
            });
        },


        methods: {
            isSingleUserRoute() {
                return this.userId !== null;
            },

            navigateToSingleUser(id) {
                this.$router.push({name: 'usersSingle', params: {userId: id}});
            },

            navigateToList() {
                this.$router.push({name: 'usersAll'});
            },

            dialogShow(id) {
                // clone user so changes are only pushed back
                // into the store when saving
                this.dialogUser = _.cloneDeep(this.getUserById(id));

                if (null === this.dialogUser) {
                    this.snackErrorRetry(
                        `No user with id ${id} in store.`,
                        this.$t('user.not_found')
                    ).then(() => this.resourceLoad('users'))
                        .then(() => this.dialogShow(id));
                }
            },

            dialogClose() {
                this.dialogUser = null;
            }
        },


        watch: {
            userId(value) {
                if (value) {
                    this.dialogShow(parseInt(value));
                } else {
                    this.dialogClose();
                }
            }
        },


        mixins: [ResourceLoadMixin, SnackbarMixin],
    }
</script>

<style scoped>

</style>
