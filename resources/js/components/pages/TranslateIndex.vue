<template>
    <div class="row">
        <div class="col-12 col-xl-8">
            <MHeader>{{ $t('translate.title') }}</MHeader>
            <p>
                {{ $t('translate.intro') }}
            </p>
            <p v-html="$t('translate.login', {link: crowdinLink})"></p>
            <div class="alert alert-info" role="alert">
                <h4 class="alert-heading">{{$t('translate.credentials')}}</h4>
                <ul>
                    <li>{{ $t('translate.username', {username}) }}</li>
                    <li>{{ $t('translate.password', {password}) }}</li>
                </ul>
                <a class="btn btn-primary" href="?translate=true&lang=zu" role="button">{{ $t('translate.startButton') }}</a>
            </div>
            <p class="text-sm">
                {{ $t('translate.signInTroubles') }}
            </p>
        </div>
    </div>
</template>

<script>
import MHeader from "../molecules/MHeader";
import Api from "../../service/Api";

export default {
    name: "TranslateIndex.vue",
    components: {MHeader},

    data() {
        return {
            username: 'loading...',
            password: 'loading...',
        }
    },

    computed: {
        crowdinLink() {
            return '<a href="https://crowdin.com/" target="_blank">Crowdin</a>';
        }
    },

    methods: {
        loadCrowdinCredentials() {
            Api().get('/crowdin/credentials')
                .then(resp => {
                    this.username = resp.data.username;
                    this.password = resp.data.password;
                })
                .catch(error => this.handleUnauthorized(error))
                .catch(reason => {
                    this.snackErrorRetry(reason, this.$t('translate.loadingCredentialsFailed'))
                        .then(() => this.loadCrowdinCredentials());
                });
        },
    },

    created() {
        this.loadCrowdinCredentials();
    }
}
</script>

<style scoped>

</style>
