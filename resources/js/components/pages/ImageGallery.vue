<template>
    <div>
        <MHeader>{{$t('images.gallery.title')}}</MHeader>
        <MSearch
            @search="search($event)"
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
                const terms = this.$route.params.terms;

                return terms ?
                    `/images/final/search/${encodeURIComponent(terms)}` :
                    '/images/final';
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

<style scoped>

</style>
