<template>
    <div class="col">
        <MHeader>{{$t('users.index.title')}}</MHeader>
        <ODataTable
            :headers="headers"
            :loading="loading"
            :rows="users"
            actionKey="id"
            @details="dialogShow($event)"
            sortBy="first_name"
        ></ODataTable>
        <ODialog
            :title="dialogTitle"
            @close="dialogUser = false"
            v-if="dialogUser"
        >
            <template #default>
                <MUserForm
                    :user="dialogUser"
                    v-if="dialogUser"
                ></MUserForm>
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
    import * as Snackbar from "../../service/Snackbar"
    import {mapGetters} from "vuex";
    import ResourceLoad from "../../mixins/ResourceLoad";

    export default {
        name: "UserIndex",
        components: {MUserForm, ODialog, ODataTable, MHeader},


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


        created() {
            this.resourceLoad('users');
        },


        methods: {
            dialogShow(id) {
                // clone user so changes are only pushed back
                // into the store when saving
                this.dialogUser = _.cloneDeep(this.getUserById(id));

                if (null === this.dialogUser) {
                    const snackbar = new Snackbar.Snackbar(
                        this.$t('user.not_found'),
                        Snackbar.ERROR,
                        this.$t('snackbar.reload')
                    );

                    console.error(`No user with id ${id} in store.`);

                    this.$store.dispatch('snackbar/push', snackbar)
                        .then(() => this.resourceLoad('users'));
                }
            },
        },


        mixins: [ResourceLoad],
    }
</script>

<style scoped>

</style>
