<template>
    <div class="m-user-stats">
        <h4>{{$t('user.stats')}}</h4>

        <ALoader v-if="loading"/>

        <div class="m-user-stats__table" v-else>
            <div class="m-user-stats__row d-flex justify-content-between">
                <div>{{$t('user.last_login')}}</div>
                <div>{{last_login}}</div>
            </div>
            <div class="m-user-stats__row d-flex justify-content-between">
                <div>{{$t('user.login_count')}}</div>
                <div>{{stats.login_count}}</div>
            </div>
            <div class="m-user-stats__row d-flex justify-content-between">
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
    import ALoader from "../atoms/ALoader";
    import UnauthorizedHandlerMixin from "../../mixins/UnauthorizedHandlerMixin";

    const lang = {
        en: english,
        fr: french,
        de: german
    };

    TimeAgo.addLocale(lang[user.lang]);
    const timeAgo = new TimeAgo(`${user.lang}-CH`);

    export default {
        name: "MUserStats",
        mixins: [UnauthorizedHandlerMixin],
        components: {ALoader},
        data() {
            return {
                loading: true,
                stats: null
            }
        },

        computed: {
            last_login() {
                const time = this.stats.last_login;

                if (time) {
                    return timeAgo.format(new Date(time));
                } else {
                    return this.$t('user.never_logged_in');
                }
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
                    .catch(error => this.handleUnauthorized(error))
                    .catch(reason => {
                        this.snackErrorRetry(reason, this.$t('user.stats_loading_failed'))
                            .then(() => this.statsLoad());
                    });
            },
        },
    }
</script>

<style lang="scss" scoped>
    .m-user-stats {
        &__row {
            padding: 0.5em 0;

            &:not(:last-of-type) {
                border-bottom: solid 1px $gray-300;
            }
        }
    }
</style>
