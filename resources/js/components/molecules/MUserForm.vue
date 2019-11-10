<template>
    <form class="m-user-form">
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
    </form>
</template>

<script>
    import AInput from "../atoms/AInput";
    import APasswordSet from "../atoms/APasswordSet";
    import ACheckbox from "../atoms/ACheckbox";
    import AFormGroup from "../atoms/AFormGroup";

    export default {
        name: "MUserForm",
        components: {AFormGroup, ACheckbox, AInput, APasswordSet},

        props: {
            user: {
                required: true,
            }
        },

        computed: {
            amISuperAdmin() {
                return true;
            }
        },

        created() {
            console.log(this.user);
        }

    }
</script>

<style scoped>

</style>
