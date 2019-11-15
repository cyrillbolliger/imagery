<template>
    <div class="col">
        <MHeader>{{$t('groups.index.title')}}</MHeader>
        <ODataTable
            :headers="headers"
            :loading="loading"
            :rows="groups"
            @details="dialogShow($event)"
            actionKey="id"
        ></ODataTable>
        <ODialog
            :title="dialogGroup.name"
            @close="dialogGroup = false"
            v-if="dialogGroup"
        >
            <template #default>
                <MUserForm
                    :user="dialogGroup"
                    v-if="dialogGroup"
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
        name: "GroupIndex",
        components: {MUserForm, ODialog, ODataTable, MHeader},


        data() {
            return {
                headers: [
                    {label: this.$t('group.name'), key: 'name', sortable: true},
                ],
                dialogGroup: null,
            }
        },


        computed: {
            ...mapGetters({
                groups: 'groups/getAll',
                getGroupById: 'groups/getById',
                loading: 'groups/loading',
            }),
        },


        created() {
            this.resourceLoad('groups');
        },


        methods: {
            dialogShow(id) {
                // clone group so changes are only pushed back
                // into the store when saving
                this.dialogGroup = _.cloneDeep(this.getGroupById(id));

                if (null === this.dialogGroup) {
                    const snackbar = new Snackbar.Snackbar(
                        this.$t('group.not_found'),
                        Snackbar.ERROR,
                        this.$t('snackbar.reload')
                    );

                    console.error(`No group with id ${id} in store.`);

                    this.$store.dispatch('snackbar/push', snackbar)
                        .then(() => this.resourceLoad('groups'));
                }
            },
        },


        mixins: [ResourceLoad],
    }
</script>

<style scoped>

</style>
