<template>
    <div>
        <MHeader>{{$t('images.gallery.title')}}</MHeader>
        <MSearch
            @search="search($event)"
            :initial-term="initialTerm"
            class="image-gallery__search mb-3 mt-3"
        />
        <OImages
            :endpoint="this.endpoint"
        />
    </div>
</template>

<script>
    import MHeader from "../molecules/MHeader";
    import MSearch from "../molecules/MSearch";
    import OImages from "../organisms/OImages";

    export default {
        name: "ImageGallery",
        components: {OImages, MSearch, MHeader},

        data() {
            return {}
        },

        computed: {
            endpoint() {
                const terms = this.$route.query.q;
                const page = this.$route.query.page;
                let termsArg = '';
                let pageArg = '';

                if (terms) {
                    termsArg = encodeURIComponent(terms);
                }

                if (page) {
                    pageArg = `?page=${page}`;
                }

                return terms ?
                    `/images/final/search/${termsArg}${pageArg}` :
                    `/images/final${pageArg}`;
            },

            initialTerm() {
                if (!this.$route.query.q) {
                    return '';
                }

                return decodeURIComponent(this.$route.query.q);
            }
        },

        methods: {
            search(terms) {
                const query = encodeURIComponent(terms);

                if (this.$route.query.q !== query) {
                    this.$router.push({
                        name: 'gallery',
                        query: {q: query},
                    });
                }
            },
        },
    }
</script>

<style lang="scss" scoped>
    .image-gallery {
        &__search {
            max-width: 500px;
        }
    }
</style>
