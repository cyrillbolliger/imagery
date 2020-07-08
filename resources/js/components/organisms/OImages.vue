<template>
    <div class="o-images">
        <div class="d-flex align-items-center">
            <h3>{{title}}</h3>
            <div
                v-if="loading"
                class="spinner-border text-primary ml-3"
                role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>

        <div
            v-if="!loading"
            class="o-images__grid"
            v-masonry
            transition-duration="0.3s"
            item-selector=".o-images__image"
        >
            <MImage
                v-masonry-tile
                class="o-images__image"
                v-for="(image, idx) in images"
                :data="image"
                :key="idx"
                @removed="images.splice(idx, 1)"
            />
        </div>

        <div style="clear:both"></div>

        <nav :aria-label="$t('pagination.title')" class="mt-3">
            <ul class="pagination justify-content-center">
                <li class="page-item" :class="{disabled: isFirstPage}">
                    <router-link class="page-link"
                                 :to="{name: 'gallery', query: { q: $route.query.q, page: previousPage }}"
                                 :tabindex="isFirstPage ? -1 : false"
                                 :aria-disabled="isFirstPage"
                                 @click.native="scrollToTop()"
                    >{{$t('pagination.previous')}}
                    </router-link>
                </li>
                <li v-for="n in pageCount"
                    class="page-item"
                    :class="{active: currentPageRouter == n}"
                >
                    <router-link
                        class="page-link"
                        :to="{name: 'gallery', query: { q: $route.query.q, page: n }}"
                        @click.native="scrollToTop()"
                    >{{n}}
                    </router-link>
                </li>
                <li class="page-item" :class="{disabled: isLastPage}">
                    <router-link class="page-link"
                                 :to="{name: 'gallery', query: {
                                     q: $route.query.q,
                                     page: nextPage
                                 }}"
                                 :tabindex="isLastPage ? -1 : false"
                                 :aria-disabled="isLastPage"
                                 @click.native="scrollToTop()"
                    >{{$t('pagination.next')}}
                    </router-link>
                </li>
            </ul>
        </nav>

    </div>
</template>

<script>
    import Api from "../../service/Api";
    import MImage from "../molecules/MImage";
    import SnackbarMixin from "../../mixins/SnackbarMixin";

    export default {
        name: "OImages",
        components: {MImage},
        mixins: [SnackbarMixin],

        data() {
            return {
                images: [],
                data: null,
                loading: true,
            }
        },

        props: {
            endpoint: {type: String, required: true},
        },

        computed: {
            title() {
                if (this.loading) {
                    return this.$t('images.gallery.loading');
                }

                if (this.filtered) {
                    return this.$tc(
                        'images.gallery.searchTitle',
                        this.data.total,
                        {number: this.data.total}
                    );
                }

                return this.$tc(
                    'images.gallery.imageCount',
                    this.data.total,
                    {number: this.data.total}
                );
            },

            filtered() {
                if (!this.$route.query.q) {
                    return false;
                }

                return this.$route.query.q.length > 0;
            },

            isFirstPage() {
                if (!this.data) {
                    return true;
                }

                return this.data.current_page === 1;
            },

            isLastPage() {
                if (!this.data) {
                    return true;
                }

                return this.data.current_page === this.data.last_page;
            },

            pageCount() {
                if (!this.data) {
                    return 0;
                }

                return Math.ceil(this.data.total / this.data.per_page);
            },

            currentPage() {
                if (!this.data) {
                    return 1;
                }

                return this.data.current_page;
            },

            previousPage() {
                return 1 < this.currentPage
                    ? this.currentPage - 1
                    : this.currentPage;
            },

            nextPage() {
                return this.pageCount > this.currentPage
                    ? this.currentPage + 1
                    : this.currentPage;
            },

            currentPageRouter() {
                if (!this.$route.query.page) {
                    return 1;
                }

                return this.$route.query.page;
            }
        },

        methods: {
            loadImages() {
                this.loading = true;
                Api().get(this.endpoint)
                    .then(resp => this.data = resp.data)
                    .then(data => this.images = data.data)
                    .catch(error =>
                        this.snackErrorRetry(error, this.$t('images.gallery.loadingFailed'))
                            .then(this.loadImages)
                    )
                    .finally(() => this.loading = false);
            },

            scrollToTop() {
                setTimeout(() =>
                        window.scroll({
                            top: 0,
                            behavior: 'smooth'
                        }),
                    200);
            },

        },

        created() {
            this.loadImages();
            this.$store.dispatch('users/load');
        },

        watch: {
            endpoint() {
                this.loadImages();
            }
        }
    }
</script>

<style lang="scss" scoped>
    .o-images {
        &__grid {
            width: 100%;
        }
    }
</style>
