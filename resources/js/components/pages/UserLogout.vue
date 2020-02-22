<template>
    <div>
        <MHeader>{{title}}</MHeader>
        <ALoader/>
    </div>
</template>

<script>
    import MHeader from "../molecules/MHeader";
    import Api from "../../service/Api";
    import SnackbarMixin from "../../mixins/SnackbarMixin";
    import ALoader from "../atoms/ALoader";

    export default {
        name: "UserLogout",
        components: {ALoader, MHeader},
        mixins: [SnackbarMixin],

        data() {
            return {
                title: this.$t('users.logout.loggingOut'),
            }
        },

        mounted() {
            this.logout();
        },

        methods: {
            logout() {
                Api().post('users/logout')
                    .then(this.redirect)
                    .catch(error =>
                        this.snackErrorRetry(error, this.$t('logoutError'))
                            .then(this.logout)
                    );
            },

            redirect() {
                this.title = this.$t('users.logout.redirecting');
                window.location.href = '/';
            }
        }
    }
</script>

<style scoped>

</style>
