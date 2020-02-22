<template>
    <div>
        <MHeader>{{$t('logos.index.title')}}</MHeader>
        <ODataTable
            :headers="headers"
            :loading="loading"
            :rows="logos"
            @details="navigateToLogosEdit($event)"
            @newEntry="navigateToLogosCreate()"
            actionKey="id"
            sortBy="name"
        ></ODataTable>
        <ODialog
            :title="dialogTitle"
            @close="navigateToList"
            v-if="dialogLogo"
        >
            <template #default>
                <OLogo
                    :logo="dialogLogo"
                    @close="navigateToList"
                ></OLogo>
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
    import OLogo from "../organisms/OLogo";
    import cloneDeep from 'lodash/cloneDeep';

    export default {
        name: "LogoIndex",
        components: {OLogo, ODialog, ODataTable, MHeader},


        data() {
            return {
                headers: [
                    {label: this.$t('logo.name'), key: 'name', sortable: true},
                ],
                dialogLogo: null,
                createLogo: false,
            }
        },


        computed: {
            ...mapGetters({
                logos: 'logosManageable/getAll',
                getLogoById: 'logosManageable/getById',
                loading: 'logosManageable/loading',
            }),
            dialogTitle() {
                return this.dialogLogo && !this.createLogo ?
                    this.dialogLogo.name :
                    this.$t('logos.index.create');
            }
        },


        props: {
            logoId: {
                default: null
            },
            create: {
                default: false
            }
        },


        created() {
            const loading = this.resourceLoad('logosManageable', true);

            // navigate directly to logo, if one is set
            loading.then(() => {
                if (this.isRouteLogoCreate()) {
                    this.dialogShowCreate();
                } else if (this.isRouteLogoEdit()) {
                    this.dialogShowEdit(parseInt(this.logoId));
                } else {
                    this.dialogClose();
                }
            });
        },


        methods: {
            isRouteLogoEdit() {
                return this.logoId !== null;
            },

            isRouteLogoCreate() {
                return this.create;
            },

            navigateToLogosEdit(id) {
                this.$router.push({name: 'logosEdit', params: {logoId: id}});
            },

            navigateToLogosCreate() {
                this.$router.push({name: 'logosCreate'});
            },

            navigateToList() {
                this.$router.push({name: 'logosAll'});
            },

            dialogShowEdit(id) {
                this.createLogo = false;

                // clone logo so changes are only pushed back
                // into the store when saving
                this.dialogLogo = cloneDeep(this.getLogoById(id));

                if (null === this.dialogLogo) {
                    this.snackErrorRetry(
                        `No logo with id ${id} in store.`,
                        this.$t('logo.not_found')
                    ).then(() => this.resourceLoad('logosManageable', true))
                        .then(() => this.dialogShowEdit(id));
                }
            },

            dialogClose() {
                this.dialogLogo = null;
                this.createLogo = false;
            },

            dialogShowCreate() {
                this.createLogo = true;
                this.dialogLogo = {groups: []};
            }
        },


        watch: {
            logoId(value) {
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
