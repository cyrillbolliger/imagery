<template>
    <div>
        <MHeader>{{$t('images.gallery.title')}}</MHeader>
        <MSearch
            @search="search($event)"
            :initial-term="initialTerm"
            class="image-gallery__search mb-3"
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

                return terms ?
                    `/images/final/search/${encodeURIComponent(terms)}` :
                    '/images/final';
            },

            initialTerm() {
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
