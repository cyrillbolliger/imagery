<template>
    <div>
        <MHeader>{{ $t('logos.download.title') }}</MHeader>
        <p>
            {{ $t('logos.download.intro') }}
            {{ amIadmin ? $t('logos.download.missing_admin') : $t('logos.download.missing_non_admin') }}
            <router-link v-if="amIadmin"
                         :to="{ name: 'logosCreate' }"
                         class="page-logo-download__create-logo-link"
            >{{ $t('logos.download.add_logo') }}
            </router-link>
        </p>

        <ODataTable
            :detailsLabel="$t('logos.download.download')"
            :headers="headers"
            :loading="loading"
            :rows="logos"
            actionKey="id"
            newEntryLabel=""
            sortBy="name"
            @details="downloadLogo($event)"
            @newEntry=""
        ></ODataTable>
    </div>
</template>

<script>
import MHeader from "../molecules/MHeader";
import ODataTable from "../organisms/ODataTable";
import {mapGetters} from "vuex";
import ResourceLoadMixin from "../../mixins/ResourceLoadMixin";
import SnackbarMixin from "../../mixins/SnackbarMixin";
import Api from "../../service/Api";

export default {
    name: "LogoDownload",
    components: {ODataTable, MHeader},


    data() {
        return {
            waitingForDownload: false,
            headers: [
                {label: this.$t('logo.name'), key: 'name', sortable: true},
            ],
        }
    },


    computed: {
        ...mapGetters({
            getLogoById: 'logosUsable/getById',
            logos: 'logosUsable/getAll',
            loadingLogos: 'logosUsable/loading',
        }),
        amIadmin() {
            return this.$store.getters['user/isAdmin'];
        },
        loading() {
            return this.loadingLogos || this.waitingForDownload;
        }
    },


    created() {
        this.resourceLoad('logosUsable', true);
    },


    methods: {
        downloadLogo(id) {
            this.waitingForDownload = true;
            const logo = this.getLogoById(id);

            this.generatePackage(logo)
                .then(() => this.downloadPackage(logo))
                .finally(() => this.waitingForDownload = false);
        },

        generatePackage(logo) {
            return Api().get(logo.download)
                .catch(reason => {
                    this.snackErrorRetry(reason, this.$t('logos.download.failedToCreatePackage'))
                        .then(() => this.generatePackage(logo));
                });
        },

        downloadPackage(logo) {
            const link = document.createElement('a');

            link.download = this.$t('logos.download.filename', {name: logo.name, extension: 'zip'});
            link.href = logo.download;

            document.body.appendChild(link);

            link.click();
        },
    },


    mixins: [ResourceLoadMixin, SnackbarMixin],
}
</script>

<style lang="scss" scoped>
    .page-logo-download {
        &__create-logo-link {
            font-size: $font-size-sm;
         }
    }
</style>
