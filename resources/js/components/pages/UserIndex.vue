<template>
    <div class="col">
        <MHeader>{{$t('users.index.title')}}</MHeader>
        <ODataTable
            :headers="headers"
            :loading="loading"
            :rows="users"
            actionKey="id"
            @details="dialogShow($event)"
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
            users() {
                return this.$store.getters['users/getAll'];
            },
            loading() {
                return this.$store.getters['users/loading'];
            },
            dialogTitle() {
                return this.dialogUser ?
                    `${this.dialogUser.first_name} ${this.dialogUser.last_name}` :
                    this.$t('users.index.create');
            }
        },

        created() {
            this.$store.dispatch('users/load')
                .catch(reason => alert(`Failed retrieving users: ${reason}`));
        },

        methods: {
            dialogShow(id) {
                // clone user so changes are only pushed back into the store when saving
                this.dialogUser = _.cloneDeep(this.$store.getters['users/getById'](id));

                if (null === this.dialogUser) {
                    alert('Error: unknown user id');
                }
            },
        }
    }
</script>

<style scoped>

</style>
