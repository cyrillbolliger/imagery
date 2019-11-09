<template>
    <div class="col">
        <MHeader>{{$t('users.index.title')}}</MHeader>
        <div v-if="list.loading">loading</div>
        <div v-if="list.lastError">error</div>
        <ul v-if="list.data">
            <li v-for="user in list.data">{{user.first_name}}</li>
        </ul>
    </div>
</template>

<script>
    import MHeader from "../molecules/MHeader";
    import {mapGetters} from "vuex";

    export default {
        name: "UserIndex",
        components: {MHeader},
        computed: {
            ...mapGetters('users', ['list']),
        }
    }
</script>

<style scoped>

</style>
