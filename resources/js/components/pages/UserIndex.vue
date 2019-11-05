<template>
    <div class="col">
        <MHeader>{{$t('users.index.title')}}</MHeader>
        <ul>
            <li>asdf</li>
            <li>asdf 2</li>
        </ul>
    </div>
</template>

<script>
    import MHeader from "../molecules/MHeader";

    export default {
        name: "UserIndex",
        components: {MHeader},
    }
</script>

<style scoped>

</style>
