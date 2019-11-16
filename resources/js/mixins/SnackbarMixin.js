import * as Snackbar from "../service/Snackbar";

export default {
    methods: {

        snackErrorRetry(error, message = null) {
            const snackbar = new Snackbar.Snackbar(
                message || this.$t('snackbar.server_error'),
                Snackbar.ERROR,
                this.$t('snackbar.retry')
            );

            console.error(error);

            return this.$store.dispatch('snackbar/push', snackbar);
        },


        snackSuccessDismiss(message) {
            const snackbar = new Snackbar.Snackbar(
                message,
                Snackbar.SUCCESS,
                this.$t('snackbar.dismiss')
            );

            return this.$store.dispatch('snackbar/push', snackbar);
        }
    }
}
