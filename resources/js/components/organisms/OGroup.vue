<template>
    <div class="o-group">
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <button
                    :class="tabEditClasses"
                    @click="currentTab = 'edit'"
                >{{$t('group.nav_edit')}}
                </button>
            </li>
            <li class="nav-item">
                <button
                    :class="tabStatsClasses"
                    @click="currentTab = 'stats'"
                >{{$t('group.nav_stats')}}
                </button>
            </li>
        </ul>
        <MGroupForm
            :group="group"
            @removed="$emit('close', $event)"
            @saved="$emit('close', $event)"
            class="mt-3"
            v-show="'edit' === currentTab"
        ></MGroupForm>
    </div>
</template>

<script>
    import MGroupForm from "../molecules/MGroupForm";


    export default {
        name: "OGroup",
        components: {MGroupForm},

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
            group: {
                required: true,
                type: Object
            }
        },

        methods: {
            tabClasses(tab) {
                return 'o-group__tab nav-link' + (this.currentTab === tab ? ' active' : '');
            },
        },
    }
</script>

<style lang="scss" scoped>
    .o-group {
        &__tab {
            background: transparent;
        }
    }
</style>
