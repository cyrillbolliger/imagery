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
            :title="dialog.title"
            @close="dialog.show = false"
            v-if="dialog.show"
        >
            <template #default>
                Main content
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

    export default {
        name: "UserIndex",
        components: {ODialog, ODataTable, MHeader},
        data() {
            return {
                headers: [
                    {label: this.$t('user.first_name'), key: 'first_name', sortable: true},
                    {label: this.$t('user.last_name'), key: 'last_name', sortable: true},
                    {label: this.$t('user.email'), key: 'email', sortable: true},
                ],
                dialog: {
                    show: true,
                    title: 'Peter Moser'
                }
            }
        },
        computed: {
            ...mapGetters('users', ['list', 'get']),
        },
        methods: {
            show(id) {
                //this.get(id);
            }
        },
        created() {
            this.get(1);
        }
    }
</script>

<style scoped>

</style>
