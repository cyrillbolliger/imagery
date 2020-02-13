<template>
    <div>
        <MHeader>{{$t('images.create.title')}}</MHeader>
        <OImagery
            @save="imageData = $event"
        />

        <OImageDialog
            :image-data="imageData"
            @close="imageData = null"
            v-if="imageData"
        />
    </div>
</template>

<script>
    import OImagery from "../organisms/OImagery";
    import OImageDialog from "../organisms/OImageDialog";
    import MHeader from "../molecules/MHeader";

    export default {
        name: "ImageCreate",
        components: {OImageDialog, OImagery, MHeader},

        data() {
            return {
                imageData: null
            }
        },
    }
</script>

<style scoped>

</style>
