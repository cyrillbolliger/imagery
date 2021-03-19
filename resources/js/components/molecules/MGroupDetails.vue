<template>
    <div class="m-group-details">
        <h4>{{$t('group.logos')}}</h4>
        <p>{{$t('group.logos_helptext')}}</p>
        <ul v-if="!logosLoading">
            <li v-for="logo of logos">{{logo.name}}</li>
            <li class="text-info" v-if="!logos.length">{{$t('group.no_logos')}}</li>
        </ul>
        <ALoader v-else/>

        <h4>{{$t('group.users')}}</h4>
        <p>{{$t('group.users_helptext')}}</p>
        <ul v-if="!usersLoading">
            <li v-for="user of users">{{`${user.first_name} ${user.last_name}`}}</li>
            <li class="text-info" v-if="!users.length">{{$t('group.no_users')}}</li>
        </ul>
        <ALoader v-else/>
    </div>
</template>

<script>
    import Api from "../../service/Api";
    import ALoader from "../atoms/ALoader";
    import UnauthorizedHandlerMixin from "../../mixins/UnauthorizedHandlerMixin";

    export default {
        name: "MGroupDetails",
        mixins: [UnauthorizedHandlerMixin],
        components: {ALoader},
        data() {
            return {
                logos: [],
                users: [],
                logosLoading: true,
                usersLoading: true
            }
        },

        props: {
            group: {
                required: true,
                type: Object
            }
        },

        created() {
            this.logosLoad();
            this.usersLoad();
        },

        methods: {
            logosLoad() {
                this.logosLoading = true;

                return Api().get(`groups/${this.group.id}/logos`)
                    .then(response => response.data)
                    .then(logos => this.logos = logos)
                    .finally(() => this.logosLoading = false)
                    .catch(error => this.handleUnauthorized(error))
                    .catch(reason => {
                        this.snackErrorRetry(reason, this.$t('group.logos_loading_failed'))
                            .then(() => this.logosLoad());
                    });
            },

            usersLoad() {
                this.usersLoading = true;

                return Api().get(`groups/${this.group.id}/users`)
                    .then(response => response.data)
                    .then(users => this.users = users)
                    .finally(() => this.usersLoading = false)
                    .catch(error => this.handleUnauthorized(error))
                    .catch(reason => {
                        this.snackErrorRetry(reason, this.$t('group.users_loading_failed'))
                            .then(() => this.usersLoad());
                    });
            },
        }
    }
</script>

<style scoped>

</style>
