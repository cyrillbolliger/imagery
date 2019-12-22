<template>
    <div
        class="o-images"
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
</template>

<script>
    import Api from "../../service/Api";
    import MImage from "../molecules/MImage";

    export default {
        name: "OImages",
        components: {MImage},
        data() {
            return {
                images: [],
            }
        },
        props: {
            endpoint: {type: String, required: true},
        },
        methods: {
            loadImages() {
                Api().get(this.endpoint)
                    .then(resp => resp.data)
                    .then(data => this.images = data.data);
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

<style scoped>

</style>
