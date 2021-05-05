<template>
    <div class="row">
        <div class="col-12 col-xl-8">
            <MHeader>{{ $t('feedback.title') }}</MHeader>
            <p>
                {{ $t('feedback.intro', {recipients}) }}
            </p>
            <form
                v-if="!submitted"
                @submit.prevent="submit"
            >
                <div class="form-group mt-4">
                    <label class="font-weight-bold" for="page-feedback__message">
                        {{$t('feedback.yourMessage')}}
                    </label>
                    <textarea
                        id="page-feedback__message"
                        v-model="message"
                        autocomplete="off"
                        class="form-control"
                        rows="5"
                    ></textarea>
                    <small class="form-text text-muted">
                        {{$t('feedback.additionalInfo')}}
                    </small>
                </div>

                <AButtonWait
                    :button-text="$t('feedback.submit')"
                    :working="sending"
                    :working-text="$t('feedback.submitting')"
                    button-class="btn btn-primary mt-3 mb-2"
                    @buttonClicked="submit"
                />
            </form>
            <div
                v-else
                class="alert alert-success"
                role="alert"
            >
                {{ $t('feedback.messageSubmitted') }}
            </div>
        </div>
    </div>
</template>

<script>
import MHeader from "../molecules/MHeader";
import Api from "../../service/Api";
import AButtonWait from "../atoms/AButtonWait";
import SnackbarMixin from "../../mixins/SnackbarMixin";
import UnauthorizedHandlerMixin from "../../mixins/UnauthorizedHandlerMixin";

export default {
    name: "FeedbackIndex.vue",
    components: {MHeader, AButtonWait},
    mixins: [SnackbarMixin, UnauthorizedHandlerMixin],

    data() {
        return {
            recipients: this.$t('feedback.defaultRecipients'),
            message: 'loading...',
            sending: false,
            submitted: false,
        }
    },

    computed: {
        initialMessage() {
            return this.$t('feedback.hello')
                + ' ' + this.recipients
                + "\n" +
                this.$t('feedback.messageTemplate');
        },

        payload() {
            return {
                message: this.message,
            };
        },
    },

    methods: {
        submit() {
            this.sending = true;
            Api().post('/feedback', this.payload)
                .then(() => this.submitted = true)
                .catch(error => this.handleUnauthorized(error))
                .catch(error => {
                    this.snackErrorRetry(error, this.$t('feedback.sendingFailed'))
                        .then(() => this.submit());
                })
                .finally(() => this.sending = false);
        },

        loadFeedbackRecipientsNames() {
            Api().get('/feedback/recipients')
                .then(resp => {
                    this.recipients = resp.data.recipients;
                    this.message = this.initialMessage;
                })
                .catch(); // do nothing. we have sound defaults
        },
    },

    created() {
        this.loadFeedbackRecipientsNames();
    }
}
</script>

<style scoped>

</style>
