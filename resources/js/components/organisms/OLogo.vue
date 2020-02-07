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
        </ul>
        <MLogoForm
            :logo="logo"
            @removed="$emit('close', $event)"
            @saved="$emit('close', $event)"
            class="mt-3"
            v-show="'edit' === currentTab"
        />
    </div>
</template>

<script>
    import MLogoForm from "../molecules/MLogoForm";

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
