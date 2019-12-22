<template>
    <figure class="m-image mb-0">
        <img
            class="m-image__image"
            @click="detailsShow = !detailsShow"
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
    import {mapGetters} from "vuex";
    import Api from "../../service/Api";
    import SnackbarMixin from "../../mixins/SnackbarMixin";

    const lang = require(`javascript-time-ago/locale/${user.lang}`);
    TimeAgo.addLocale(lang);
    const timeAgo = new TimeAgo(`${user.lang}-CH`);

    export default {
        name: "MImage",
        mixins: [SnackbarMixin],

        data() {
            return {
                detailsShow: false,
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
                const user = this.user;
                return this.$t('images.gallery.createdAtBy', {timeAgo: time, user});
            },

            user() {
                const user = this.getUserById(this.data.user_id);

                if (!user) {
                    return this.$t('images.gallery.userUnknown');
                }

                const name = _.escape(`${user.first_name} ${user.last_name}`); // XSS: OK
                const email = encodeURIComponent(_.escape(user.email)); // XSS: OK

                return `<a href="mailto:${email}">${name}</a>`;
            },

            canDeleteImage() {
                const superAdmin = this.currentUser.super_admin;
                const myImage = this.data.user_id === this.currentUser.id;

                return superAdmin || myImage;
            },
        },

        methods: {
            remove() {
                Api().delete(`/images/${this.data.id}`)
                    .then(() => this.$emit('removed'))
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
