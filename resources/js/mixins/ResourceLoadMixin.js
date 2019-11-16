import SnackbarMixin from "./SnackbarMixin";

export default {
    mixins: [SnackbarMixin],

    methods: {
        resourceLoad(resource) {
            return this.$store.dispatch(`${resource}/load`)
                .catch(reason => {
                    this.snackErrorRetry(reason)
                        .then(() => this.resourceLoad(resource));
                });
        },
    }
}

