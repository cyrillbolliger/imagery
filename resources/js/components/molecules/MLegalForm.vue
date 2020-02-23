<template>
    <form
        @submit.prevent="submit"
        autocomplete="off"
        class="m-legal-form"
    >
        <h4>{{$t('images.create.legal.personality.title')}}</h4>

        <div class="form-group">
            <p>{{$t('images.create.legal.personality.identifiablePeople')}}</p>
            <div class="btn-group btn-group-toggle" data-toggle="buttons">
                <label :class="{active: people}" class="btn btn-secondary">
                    <input
                        @click="personality = 'unknown'"
                        autocomplete="off"
                        type="radio"
                        :value="true"
                        v-model="people"
                    >{{$t('images.create.legal.personality.yes')}}</label>
                <label :class="{active: !people}" class="btn btn-secondary">
                    <input
                        @click="personality = 'not_applicable'"
                        autocomplete="off"
                        type="radio"
                        :value="false"
                        v-model="people"
                    >{{$t('images.create.legal.personality.no')}}</label>
            </div>
        </div>

        <div class="form-group" v-if="people">
            <p>{{$t('images.create.legal.personality.publicInterest')}}</p>
            <div class="btn-group btn-group-toggle" data-toggle="buttons">
                <label :class="{active: personality === 'agreement'}"
                       class="btn btn-secondary">
                    <input
                        autocomplete="off"
                        type="radio"
                        :value="'agreement'"
                        v-model="personality"
                    >{{$t('images.create.legal.personality.yes')}}</label>
                <label :class="{active: personality === 'unknown'}"
                       class="btn btn-secondary">
                    <input
                        autocomplete="off"
                        type="radio"
                        :value="'unknown'"
                        v-model="personality"
                    >{{$t('images.create.legal.personality.unclear')}}</label>
                <label :class="{active: personality === 'no_agreement'}"
                       class="btn btn-secondary">
                    <input
                        autocomplete="off"
                        type="radio"
                        :value="'no_agreement'"
                        v-model="personality"
                    >{{$t('images.create.legal.personality.no')}}</label>
            </div>
            <div
                class="alert alert-warning mt-3"
                role="alert"
                v-if="showLegalAdvice"
            >
                <strong>{{$t('images.create.legal.personality.warningTitle')}}</strong>
                {{$t('images.create.legal.personality.warningText')}}
            </div>
        </div>


        <h4 class="mt-4">{{$t('images.create.legal.copyright.title')}}</h4>

        <div class="form-group">
            <label for="originator_type">{{$t('images.create.legal.copyright.originatorType')}}</label>
            <select
                :class="{'is-invalid': $v.originatorType.$error, 'is-valid': !$v.originatorType.$invalid}"
                @change="setOriginatorByOriginatorType($event.target.value)"
                class="form-control"
                id="originator_type"
                v-model="$v.originatorType.$model">
                <option disabled value="">{{$t('images.create.legal.copyright.originatorSelect')}}</option>
                <option v-bind:value="option.value" v-for="option in originatorChoices">
                    {{ option.text }}
                </option>
            </select>
            <div class="invalid-feedback" v-if="$v.originatorType.$error">
                {{$t('validation.required')}}
            </div>
        </div>

        <div class="form-group" v-if="originatorType === 'stock'">
            <label for="source">{{$t('images.create.legal.copyright.source')}}</label>
            <input
                :class="{'is-invalid': $v.stockUrl.$error, 'is-valid': !$v.stockUrl.$invalid && $v.originator.$dirty}"
                :placeholder="$t('images.create.legal.copyright.sourcePlaceholder')"
                class="form-control"
                id="source"
                type="url"
                v-model.trim="$v.stockUrl.$model">
            <div class="invalid-feedback" v-if="$v.stockUrl.$error">
                {{$t('validation.url')}}
            </div>
        </div>

        <div class="form-group" v-if="originatorType === 'stock'">
            <label for="licence">{{$t('images.create.legal.copyright.licence')}}</label>
            <select :class="{'is-invalid': $v.licence.$error, 'is-valid': $v.licence.$dirty}"
                    class="form-control"
                    id="licence"
                    v-model="$v.licence.$model">
                <option value="other">{{$t('images.create.legal.copyright.other')}}</option>
                <option value="creative_commons_attribution">{{$t('images.create.legal.copyright.ccby')}}</option>
                <option value="creative_commons">{{$t('images.create.legal.copyright.cc0')}}</option>
            </select>
        </div>


        <div class="form-group" v-if="originatorType !== 'unknown'">
            <label for="originator">{{$t('images.create.legal.copyright.originator')}}</label>
            <input
                :class="{'is-invalid': $v.originator.$error, 'is-valid': !$v.originator.$invalid && $v.originator.$dirty}"
                :placeholder="$t('images.create.legal.copyright.originatorPlaceholder')"
                class="form-control"
                id="originator"
                type="text"
                v-model.trim="$v.originator.$model">
            <div class="invalid-feedback" v-if="$v.originator.$error">
                {{$t('validation.required')}}
            </div>
        </div>

        <div class="alert alert-warning mt-3"
             role="alert"
             v-else>
            <strong>{{$t('images.create.legal.copyright.warningTitle')}}</strong>
            {{$t('images.create.legal.copyright.warningText')}}
        </div>

        <div class="form-group">
            <p class="mt-3 mb-2">{{$t('images.create.legal.copyright.confirmation')}}</p>
            <div class="custom-control custom-switch">
                <input :class="{'is-invalid': $v.rightToUse.$dirty && !rightToUse}"
                       class="custom-control-input"
                       id="rightToUse"
                       type="checkbox"
                       v-model="$v.rightToUse.$model">
                <label class="custom-control-label"
                       for="rightToUse"
                >{{$t('images.create.legal.copyright.rightToUse')}}</label>
                <div class="invalid-feedback" v-if="$v.rightToUse.$dirty && !rightToUse">
                    {{$t('validation.required')}}
                </div>
            </div>
            <div class="custom-control custom-switch mt-2">
                <input :disabled="!shareable"
                       class="custom-control-input"
                       id="rightToShare"
                       type="checkbox"
                       v-model="rightToShare">
                <label class="custom-control-label"
                       for="rightToShare"
                >{{$t('images.create.legal.copyright.rightToShare')}}</label>
            </div>
        </div>

        <AButtonWait
            :button-text="$t('images.create.legal.confirmation')"
            :working="saving"
            :working-text="$t('images.create.legal.saving')"
            @buttonClicked="submit"
            button-class="btn btn-outline-primary mt-3 mb-2"
        />

        <div class="alert alert-danger mt-3 mb-2"
             role="alert"
             v-if="!isValid">
            {{$t('images.create.legal.invalid')}}
        </div>
    </form>
</template>

<script>
    import {required, maxLength, url, requiredIf, requiredUnless} from 'vuelidate/lib/validators';
    import AButtonWait from "../atoms/AButtonWait";
    import Api from "../../service/Api";
    import SnackbarMixin from "../../mixins/SnackbarMixin";

    const isTrue = (value) => true === value;

    export default {
        name: "MLegalForm",
        components: {AButtonWait},
        mixins: [SnackbarMixin],

        data() {
            return {
                originatorChoices: [
                    {value: 'user', text: this.$t('images.create.legal.copyright.me')},
                    {value: 'stock', text: this.$t('images.create.legal.copyright.stock')},
                    {value: 'agency', text: this.$t('images.create.legal.copyright.agency')},
                    {value: 'friend', text: this.$t('images.create.legal.copyright.friend')},
                    {value: 'unknown', text: this.$t('images.create.legal.copyright.unknown')},
                ],
                isValid: true,
                saving: false
            }
        },

        props: {
            imageUpload: {
                required: true,
                type: Promise,
            },
        },

        computed: {
            showLegalAdvice() {
                return this.personality === 'unknown'
                    || this.personality === 'no_agreement';
            },

            shareable() {
                return this.personality !== 'unknown'
                    && this.originatorType !== 'stock'
                    && this.originatorType !== 'unknown';
            },

            payload() {
                const payload = {
                    right_of_personality: this.personality,
                    originator_type: this.originatorType,
                    shared: this.rightToShare
                };

                if ('unknown' !== this.originatorType) {
                    payload.originator = this.originator;
                }

                if ('stock' === this.originatorType) {
                    payload.licence = this.licence;
                    payload.stock_url = this.stockUrl;
                }

                return payload;
            },

            personality: {
                get() {
                    return this.$store.getters['legal/get']('personality');
                },
                set(value) {
                    return this.$store.commit('legal/update', {key: 'personality', value: value});
                },
            },

            people: {
                get() {
                    return this.$store.getters['legal/get']('people');
                },
                set(value) {
                    return this.$store.commit('legal/update', {key: 'people', value: value});
                },
            },

            originatorType: {
                get() {
                    return this.$store.getters['legal/get']('originatorType');
                },
                set(value) {
                    return this.$store.commit('legal/update', {key: 'originatorType', value: value});
                },
            },

            stockUrl: {
                get() {
                    return this.$store.getters['legal/get']('stockUrl');
                },
                set(value) {
                    return this.$store.commit('legal/update', {key: 'stockUrl', value: value});
                },
            },

            licence: {
                get() {
                    return this.$store.getters['legal/get']('licence');
                },
                set(value) {
                    return this.$store.commit('legal/update', {key: 'licence', value: value});
                },
            },

            originator: {
                get() {
                    return this.$store.getters['legal/get']('originator');
                },
                set(value) {
                    return this.$store.commit('legal/update', {key: 'originator', value: value});
                },
            },

            rightToUse: {
                get() {
                    return this.$store.getters['legal/get']('rightToUse');
                },
                set(value) {
                    return this.$store.commit('legal/update', {key: 'rightToUse', value: value});
                },
            },

            rightToShare: {
                get() {
                    return this.$store.getters['legal/get']('rightToShare');
                },
                set(value) {
                    return this.$store.commit('legal/update', {key: 'rightToShare', value: value});
                },
            },
        },

        validations() {
            return {
                originatorType: {
                    required
                },
                stockUrl: {
                    required: requiredIf(() => 'stock' === this.originatorType),
                    url,
                    maxLength: maxLength(2048),
                },
                licence: {
                    required: requiredIf(() => 'stock' === this.originatorType),
                },
                originator: {
                    required: requiredUnless(() => 'unknown' === this.originatorType),
                    maxLength: maxLength(192)
                },
                rightToUse: {
                    required,
                    isTrue
                },
            }
        },

        methods: {
            submit() {
                if (!this.validate()) {
                    return;
                }

                this.saving = true;
                this.$emit('saving');
                this.imageUpload.then(this.save);
            },

            validate() {
                this.$v.$touch();
                this.isValid = !this.$v.$invalid;
                return this.isValid;
            },

            save(imageId) {
                Api().post(`images/${imageId}/legal`, this.payload)
                    .then(() => this.$emit('completed'))
                    .catch(error => {
                        this.snackErrorRetry(error, this.$t('images.create.legal.savingFailed'))
                            .then(() => this.save(imageId));
                    })
                    .finally(() => this.saving = false);
            },

            setOriginatorByOriginatorType(type) {
                const user = this.$store.getters['user/object'];
                const fullName = `${user.first_name} ${user.last_name}`;

                if ('user' === type) {
                    this.originator = fullName;
                } else if (fullName === this.originator) {
                    this.originator = '';
                }
            },
        }
    }
</script>

<style scoped>

</style>
