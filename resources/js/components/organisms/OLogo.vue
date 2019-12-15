<template>
    <div class="o-logo">
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <button
                    :class="tabEditClasses"
                    @click="currentTab = 'edit'"
                >{{$t('logo.navEdit')}}
                </button>
            </li>
            <li class="nav-item">
                <button
                    :class="tabStatsClasses"
                    @click="currentTab = 'details'"
                >{{$t('logo.navDetails')}}
                </button>
            </li>
        </ul>
        <MLogoForm
            :logo="logo"
            @removed="$emit('close', $event)"
            @saved="$emit('close', $event)"
            class="mt-3"
            v-show="'edit' === currentTab"
        ></MLogoForm>
        <!--        <MLogoDetails-->
        <!--            :logo="logo"-->
        <!--            class="mt-3"-->
        <!--            v-show="'details' === currentTab"-->
        <!--        ></MLogoDetails>-->
    </div>
</template>

<script>
    import MLogoForm from "../molecules/MLogoForm";
    // import MLogoDetails from "../molecules/MLogoDetails";


    export default {
        name: "OLogo",
        components: {MLogoForm},

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
            logo: {
                required: true,
                type: Object
            }
        },

        methods: {
            tabClasses(tab) {
                return 'o-logo__tab nav-link' + (this.currentTab === tab ? ' active' : '');
            },
        },
    }
</script>

<style lang="scss" scoped>
    .o-logo {
        &__tab {
            background: transparent;
        }
    }
</style>
