import SnackbarMixin from "./SnackbarMixin";

export default {
    mixins: [SnackbarMixin],

    methods: {
        resourceLoad(resource, refresh = false) {
            return this.$store.dispatch(`${resource}/load`, refresh)
                .catch(reason => {
                    this.snackErrorRetry(reason)
                        .then(() => this.resourceLoad(resource, refresh));
                });
        },
    }
}

