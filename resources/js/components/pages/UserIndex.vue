<template>
    <div class="col">
        <MHeader>{{$t('users.index.title')}}</MHeader>
        <ODataTable
            :error="null !== lastError"
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
    import UserRepository from "../../repositories/UserRepository";

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
                users: {},
                loading: true,
                dialogUser: null,
                dialogUserOriginal: null,
                lastError: null
            }
        },

        computed: {
            dialogTitle() {
                return this.dialogUser ?
                    `${this.dialogUser.first_name} ${this.dialogUser.last_name}` :
                    this.$t('users.index.create');
            }
        },

        created() {
            this.getAll();
        },

        methods: {
            getAll() {
                this.loading = true;
                UserRepository.getAll()
                    .then(({data}) => this.populateUsers(data))
                    .catch(reason => this.lastError = reason)
                    .finally(() => this.loading = false);
            },

            dialogShow(id) {
                const key = this.userPropName(id);

                if (this.users.hasOwnProperty(key)) {
                    this.dialogUser = this.users[key];
                    this.dialogUserOriginal = _.cloneDeep(this.dialogUser);
                } else {
                    alert('Error: unknown user id');
                }
            },

            populateUsers(data) {
                const users = {};
                let key = '';

                for (let idx in data) {
                    key = this.userPropName(data[idx].id);
                    users[key] = data[idx];
                }

                this.users = users;
            },

            userPropName(id) {
                return `id-${id}`;
            },
        }
    }
</script>

<style scoped>

</style>
