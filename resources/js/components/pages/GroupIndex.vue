<template>
    <div>
        <MHeader>{{$t('groups.index.title')}}</MHeader>
        <ODataTable
            :headers="headers"
            :loading="loading"
            :rows="groups"
            actionKey="id"
            @details="navigateToGroupsEdit($event)"
            sortBy="tree_name"
            @newEntry="navigateToGroupsCreate()"
        />
        <ODialog
            :title="dialogTitle"
            @close="navigateToList"
            v-if="dialogGroup"
        >
            <template #default>
                <OGroup
                    :group="dialogGroup"
                    @close="navigateToList"
                />
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
    import OGroup from "../organisms/OGroup";
    import cloneDeep from 'lodash/cloneDeep';

    export default {
        name: "GroupIndex",
        components: {OGroup, ODialog, ODataTable, MHeader},


        data() {
            return {
                headers: [
                    {label: this.$t('group.name'), key: 'tree_name', sortable: true},
                ],
                dialogGroup: null,
                createGroup: false,
            }
        },


        computed: {
            ...mapGetters({
                groups: 'groups/getAll',
                getGroupById: 'groups/getById',
                loading: 'groups/loading',
            }),
            dialogTitle() {
                return this.dialogGroup && !this.createGroup ?
                    this.dialogGroup.name :
                    this.$t('groups.index.create');
            }
        },


        props: {
            groupId: {
                default: null
            },
            create: {
                default: false
            }
        },


        created() {
            const loading = this.resourceLoad('groups');

            // navigate directly to group, if one is set
            loading.then(() => {
                if (this.isRouteGroupCreate()) {
                    this.dialogShowCreate();
                } else if (this.isRouteGroupEdit()) {
                    this.dialogShowEdit(parseInt(this.groupId));
                } else {
                    this.dialogClose();
                }
            });
        },


        methods: {
            isRouteGroupEdit() {
                return this.groupId !== null;
            },

            isRouteGroupCreate() {
                return this.create;
            },

            navigateToGroupsEdit(id) {
                this.$router.push({name: 'groupsEdit', params: {groupId: id}});
            },

            navigateToGroupsCreate() {
                this.$router.push({name: 'groupsCreate'});
            },

            navigateToList() {
                this.$router.push({name: 'groupsAll'});
            },

            dialogShowEdit(id) {
                this.createGroup = false;

                // clone group so changes are only pushed back
                // into the store when saving
                this.dialogGroup = cloneDeep(this.getGroupById(id));

                if (null === this.dialogGroup) {
                    this.snackErrorRetry(
                        `No group with id ${id} in store.`,
                        this.$t('group.not_found')
                    ).then(() => this.resourceLoad('groups', true))
                        .then(() => this.dialogShowEdit(id));
                }
            },

            dialogClose() {
                this.dialogGroup = null;
                this.createGroup = false;
            },

            dialogShowCreate() {
                this.createGroup = true;
                this.dialogGroup = {};
            }
        },


        watch: {
            groupId(value) {
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
