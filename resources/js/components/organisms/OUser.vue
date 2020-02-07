<template>
    <div class="o-user">
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <button
                    :class="tabEditClasses"
                    @click="currentTab = 'edit'"
                >{{$t('user.nav_edit')}}
                </button>
            </li>
            <li class="nav-item">
                <button
                    :class="tabStatsClasses"
                    @click="currentTab = 'stats'"
                >{{$t('user.nav_stats')}}
                </button>
            </li>
        </ul>
        <MUserForm
            :user="user"
            class="mt-3"
            v-show="'edit' === currentTab"
            @saved="$emit('close', $event)"
            @removed="$emit('close', $event)"
        />
        <MUserStats
            :user="user"
            class="mt-3"
            v-show="'stats' === currentTab"
        />
    </div>
</template>

<script>
    import MUserForm from "../molecules/MUserForm";
    import MUserStats from "../molecules/MUserStats";


    export default {
        name: "OUser",
        components: {MUserStats, MUserForm},

        data() {
            return {
                currentTab: 'edit'
            }
        },

        computed: {
            tabEditClasses() {
                return this.tabClasses('edit');
            },
            tabStatsClasses() {
                return this.tabClasses('stats');
            }
        },

        props: {
            user: {
                required: true,
                type: Object
            }
        },

        methods: {
            tabClasses(tab) {
                return 'o-user__tab nav-link' + (this.currentTab === tab ? ' active' : '');
            },
        },
    }
</script>

<style lang="scss" scoped>
    .o-user {
        &__tab {
            background: transparent;
        }
    }
</style>
