<template>
    <figure class="m-image mb-0">
        <img
            class="m-image__image"
            :class="{transparent: data.background === 'transparent'}"
            @click="toggleDetails()"
            :src="data.thumb_src"
            :alt="data.keywords">
        <transition name="fade"
                    enter-active-class="fadeInUp"
                    leave-active-class="fadeOutDown">
            <figcaption
                class="m-image__caption"
                v-if="detailsShow"
            >
                <p class="" v-if="loading">{{$t('images.gallery.loading')}}</p>
                <p v-else v-html="created"/>
                <a :href="data.src" download="image.png">
                    <button class="btn btn-outline-primary btn-sm">
                        {{$t('images.gallery.download')}}
                    </button>
                </a>
                <button
                    v-if="canDeleteImage"
                    @click="remove()"
                    class="btn btn-link btn-sm"
                >{{$t('images.gallery.delete')}}
                </button>
            </figcaption>
        </transition>
    </figure>
</template>

<script>
    import TimeAgo from 'javascript-time-ago';
    import english from 'javascript-time-ago/locale/en';
    import french from 'javascript-time-ago/locale/fr';
    import german from 'javascript-time-ago/locale/de';
    import {mapGetters} from "vuex";
    import Api from "../../service/Api";
    import SnackbarMixin from "../../mixins/SnackbarMixin";
    import escape from 'lodash/escape';
    import UnauthorizedHandlerMixin from "../../mixins/UnauthorizedHandlerMixin";

    const lang = {
        en: english,
        fr: french,
        de: german
    };

    TimeAgo.addLocale(lang[user.lang]);
    const timeAgo = new TimeAgo(`${user.lang}-CH`);

    export default {
        name: "MImage",
        mixins: [SnackbarMixin, UnauthorizedHandlerMixin],

        data() {
            return {
                detailsShow: false,
                creator: null,
            }
        },

        props: {
            data: {required: true, type: Object}
        },

        computed: {
            ...mapGetters({
                getUserById: 'users/getById',
                loading: 'users/loading',
                currentUser: 'user/object',
            }),

            created() {
                const time = timeAgo.format(new Date(this.data.created_at));
                return this.$t('images.gallery.createdAtBy', {timeAgo: time, user: this.creatorString});
            },

            creatorString() {
                // loading
                if (null === this.creator) {
                    return '...';
                }

                // deleted
                if (false === this.creator) {
                    return this.$t('images.gallery.userUnknown');
                }

                const name = escape(`${this.creator.first_name} ${this.creator.last_name}`); // XSS: OK
                const email = encodeURIComponent(escape(this.creator.email)); // XSS: OK

                return `<a href="mailto:${email}">${name}</a>`;
            },

            canDeleteImage() {
                const superAdmin = this.currentUser.super_admin;
                const myImage = this.data.user_id === this.currentUser.id;

                return superAdmin || myImage;
            },
        },

        methods: {
            toggleDetails() {
                this.detailsShow = !this.detailsShow;

                if (null === this.creator){
                    // do not use users store as non admins can't use the index
                    // method. however they can query single users by id.
                    Api().get(`/users/${this.data.user_id}`)
                        .then(resp => this.creator = resp.data)
                        .catch(error => this.handleUnauthorized(error))
                        .catch(() => this.creator = false);
                }
            },

            remove() {
                Api().delete(`/images/${this.data.id}`)
                    .then(() => this.$emit('removed'))
                    .catch(error => this.handleUnauthorized(error))
                    .catch(error =>
                        this.snackErrorRetry(error, this.$t('images.gallery.deleteError'))
                            .then(this.remove)
                    );
            },
        }
    }
</script>

<style lang="scss" scoped>
    .m-image {
        width: 100%;
        position: relative;
        cursor: pointer;
        overflow: hidden;

        @include media-breakpoint-up(md) {
            max-width: calc(50vw - 15px);
        }

        @include media-breakpoint-up(lg) {
            max-width: 300px;
        }

        &__image {
            width: 100%;

            &.transparent {
                // https://stackoverflow.com/a/35362074
                background-image: linear-gradient(45deg, #d7d7d7 25%, transparent 25%),
                linear-gradient(-45deg, #d7d7d7 25%, transparent 25%),
                linear-gradient(45deg, transparent 75%, #d7d7d7 75%),
                linear-gradient(-45deg, transparent 75%, #d7d7d7 75%);
                background-size: 20px 20px;
                background-position: 0 0, 0 10px, 10px -10px, -10px 0px;
            }
        }

        &__caption {
            animation-duration: 0.4s;
            cursor: default;
            position: absolute;
            bottom: 0;
            width: 100%;
            background: rgba(255, 255, 255, 0.95);
            padding: 0.5em;
        }
    }
</style>
