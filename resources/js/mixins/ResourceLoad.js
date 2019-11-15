import * as Snackbar from "../service/Snackbar";

export default {
    methods: {
        resourceLoad(resource) {
            this.$store.dispatch(`${resource}/load`)
                .catch(reason => {
                    const snackbar = new Snackbar.Snackbar(
                        this.$t('snackbar.server_error'),
                        Snackbar.ERROR,
                        this.$t('snackbar.retry')
                    );

                    console.error(reason);

                    this.$store.dispatch('snackbar/push', snackbar)
                        .then(() => this.resourceLoad(resource));
                });
        },
    }
}

