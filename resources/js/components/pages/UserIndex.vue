<template>
    <div class="col">
        <MHeader>{{$t('users.index.title')}}</MHeader>
        <ODataTable
            :error="null !== list.lastError"
            :headers="headers"
            :loading="list.loading"
            :rows="list.data"
        ></ODataTable>
    </div>
</template>

<script>
    import MHeader from "../molecules/MHeader";
    import {mapGetters} from "vuex";
    import ODataTable from "../organisms/ODataTable";

    export default {
        name: "UserIndex",
        components: {ODataTable, MHeader},
        data() {
            return {
                headers: [
                    {label: this.$t('user.first_name'), key: 'first_name', sortable: true},
                    {label: this.$t('user.last_name'), key: 'last_name', sortable: true},
                    {label: this.$t('user.email'), key: 'email'},
                ],
            }
        },
        computed: {
            ...mapGetters('users', ['list']),
        }
    }
</script>

<style scoped>

</style>
