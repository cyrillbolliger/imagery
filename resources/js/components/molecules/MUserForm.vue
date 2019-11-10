<template>
    <form class="m-user-form">
        <AInput
            :label="$t('user.first_name')"
            v-model="get().first_name"
        ></AInput>
    </form>
</template>

<script>
    import AInput from "../atoms/AInput";
    import {mapGetters} from "vuex";

    export default {
        name: "MUserForm",
        components: {AInput},
        computed: {
            ...mapGetters('users', ['get']),
        },

    }
</script>

<style scoped>

</style>
