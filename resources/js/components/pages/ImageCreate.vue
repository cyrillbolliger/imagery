<template>
    <div>
        <h1>{{$t('images.create.title')}}</h1>
        <OImagery
            @save="imageData = $event"
        ></OImagery>
        <OImageDialog
            :image-data="imageData"
            @close="imageData = null"
            v-if="imageData"
        ></OImageDialog>
    </div>
</template>

<script>
    import OImagery from "../organisms/OImagery";
    import OImageDialog from "../organisms/OImageDialog";

    export default {
        name: "ImageCreate",
        components: {OImageDialog, OImagery},

        data() {
            return {
                imageData: null
            }
        },
    }
</script>

<style scoped>

</style>
