<template>
    <div>
        <button
            @click="save"
            class="btn btn-link btn-sm pt-0 pb-0"
            v-if="ready && !isDefaultSelected"
            v-show="!saveing"
        >{{$t('images.create.logoDefault')}}
        </button>

        <small
            class="text-muted pl-2"
            v-if="ready && isDefaultSelected && !saveing"
        >{{$t('images.create.logoDefaultSelected')}}</small>
    </div>
</template>

<script>
    import Api from "../../service/Api";

    export default {
        name: "ADefaultLogo",

        data() {
            return {
                saveing: false,
            }
        },

        props: {
            selectedId: {
                type: Number,
            },
            ready: {
                type: Boolean,
                default: false
            },
        },

        computed: {
            isDefaultSelected() {
                return this.selectedId === this.defaultId;
            },

            defaultId() {
                return this.$store.getters['user/object'].default_logo;
            },
        },

        methods: {
            save() {
                this.saveing = true;

                const userId = this.$store.getters['user/id'];
                const payload = {default_logo: this.selectedId};

                Api().put(`users/${userId}`, payload)
                    .then(resp => this.$store.dispatch('user/set', resp.data))
                    .catch(reason => {
                        this.snackErrorRetry(reason, this.$t('images.create.logoDefaultSaveFailed'))
                            .then(() => this.save());
                    })
                    .then(() => this.saveing = false);
            },
        },

        watch: {
            saveing(value) {
                this.$emit(value ? 'saveing' : 'saved');
            },
        }
    }
</script>

<style scoped>

</style>
