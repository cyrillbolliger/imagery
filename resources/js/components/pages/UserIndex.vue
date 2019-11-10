<template>
    <div class="col">
        <MHeader>{{$t('users.index.title')}}</MHeader>
        <ODataTable
            :error="null !== list.lastError"
            :headers="headers"
            :loading="list.loading"
            :rows="list.data"
            actionKey="id"
            @details="show($event)"
        ></ODataTable>
        <ODialog
            :title="dialogTitle"
            @close="dialog.show = false"
            v-if="dialog.show"
        >
            <template #default>
                <MUserForm v-if="dialogUser"></MUserForm>
            </template>
            <template #footer>
                Footer
            </template>
        </ODialog>
    </div>
</template>

<script>
    import MHeader from "../molecules/MHeader";
    import {mapGetters} from "vuex";
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
                dialog: {
                    show: false,
                    id: null
                }
            }
        },
        computed: {
            ...mapGetters('users', ['list', 'get']),
            dialogTitle() {
                return this.dialogUser ?
                    `${this.dialogUser.first_name} ${this.dialogUser.last_name}` :
                    this.$t('users.dialog.loading');
            },
            dialogUser() {
                return this.get(this.dialog.id);
            }
        },
        methods: {
            show(id) {
                this.dialog.id = id;
                this.dialog.show = true;
            }
        },
        created() {
            this.show(415);
        }
    }
</script>

<style scoped>

</style>
