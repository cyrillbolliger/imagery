<template>
    <form autocomplete="off" class="m-user-form">
        <AInput
            :label="$t('user.first_name')"
            :required="true"
            v-model="user.first_name"
        ></AInput>
        <AInput
            :label="$t('user.last_name')"
            :required="true"
            v-model="user.last_name"
        ></AInput>
        <AInput
            :label="$t('user.email')"
            :required="true"
            type="email"
            v-model="user.email"
        ></AInput>
        <APasswordSet
            v-model="user.password"
        ></APasswordSet>
        <AFormGroup>
            <template #label>
                {{ $t('user.super_admin') }}
            </template>
            <template #input>
                <ACheckbox
                    :label="$t('user.super_admin_desc')"
                    v-if="amISuperAdmin"
                    v-model="user.super_admin"
                ></ACheckbox>
            </template>
        </AFormGroup>
        <ASelect
            :label="$t('user.language')"
            :options="options"
            :required="true"
            v-model="user.lang"
        ></ASelect>
        <ASelect
            :label="$t('user.managed_by')"
            :options="groupsSelect"
            :required="true"
            v-model="user.managed_by"
        ></ASelect>
        <!-- user roles -->
        <!-- admin roles -->
        <!-- default logo (if role; only role logos selectable) -->
    </form>
</template>

<script>
    import AInput from "../atoms/AInput";
    import APasswordSet from "../atoms/APasswordSet";
    import ACheckbox from "../atoms/ACheckbox";
    import AFormGroup from "../atoms/AFormGroup";
    import ASelect from "../atoms/ASelect";
    import ResourceLoad from "../../mixins/ResourceLoad";
    import {mapGetters} from "vuex";

    export default {
        name: "MUserForm",
        components: {ASelect, AFormGroup, ACheckbox, AInput, APasswordSet},

        data() {
            return {
                options: [
                    {value: 'de', text: this.$t('languages.de')},
                    {value: 'fr', text: this.$t('languages.fr')},
                    {value: 'en', text: this.$t('languages.en')},
                ],
            }
        },

        props: {
            user: {
                required: true,
            }
        },

        computed: {
            ...mapGetters({
                groups: 'groups/getAll',
                logos: 'logos/getAll'
            }),
            amISuperAdmin() {
                return true;
            },
            groupsSelect() {
                return this.groups.map(group => ({
                        value: group.id,
                        text: group.name
                    })
                ).sort((a, b) => a.text.localeCompare(b.text));
            },
        },

        created() {
            this.resourceLoad('groups');
            this.resourceLoad('logos');
        },

        mixins: [ResourceLoad],

    }
</script>

<style scoped>

</style>
