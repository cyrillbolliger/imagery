<template>
    <div class="m-user-stats">
        <h4>{{$t('user.stats')}}</h4>

        <div class="d-flex justify-content-center"
             v-if="loading"
        >
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>

        <div v-else>
            <div class="d-flex justify-content-between">
                <div>{{$t('user.last_login')}}</div>
                <div>{{last_login}}</div>
            </div>
            <div class="d-flex justify-content-between">
                <div>{{$t('user.login_count')}}</div>
                <div>{{stats.login_count}}</div>
            </div>
            <div class="d-flex justify-content-between">
                <div>{{$t('user.image_count')}}</div>
                <div>{{stats.image_count}}</div>
            </div>
        </div>
    </div>
</template>

<script>
    import Api from "../../service/Api";
    import english from "javascript-time-ago/locale/en";
    import french from "javascript-time-ago/locale/fr";
    import german from "javascript-time-ago/locale/de";
    import TimeAgo from "javascript-time-ago";

    const lang = {
        en: english,
        fr: french,
        de: german
    };

    TimeAgo.addLocale(lang[user.lang]);
    const timeAgo = new TimeAgo(`${user.lang}-CH`);

    export default {
        name: "MUserStats",

        data() {
            return {
                loading: true,
                stats: null
            }
        },

        computed: {
            last_login() {
                return timeAgo.format(new Date(this.stats.last_login));
            },
        },

        props: {
            user: {
                required: true,
                type: Object
            }
        },

        created() {
            this.statsLoad()
                .then(() => this.loading = false);
        },

        methods: {
            statsLoad() {
                return Api().get(`users/${this.user.id}/stats`)
                    .then(response => response.data)
                    .then(stats => this.stats = stats)
                    .catch(reason => {
                        this.snackErrorRetry(reason, this.$t('user.stats_loading_failed'))
                            .then(() => this.statsLoad());
                    });
            },
        },
    }
</script>

<style scoped>

</style>
