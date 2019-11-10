<template>
    <AFormGroup>
        <template #label>
            <label class="col-form-label" for="password">
                {{$t('user.password')}}
                <span v-if="required">*</span>
            </label>
        </template>
        <template #input>
            <div class="input-group">
                <input
                    :placeholder="$t('user.password_placeholder')"
                    :required="required"
                    :type="visible ? 'text' : 'password'"
                    :value="value"
                    @input="$emit('input', $event.target.value)"
                    class="form-control"
                    autocomplete="new-password"
                    id="password">
                <div class="input-group-append" v-if="value">
                    <button @click.prevent="visible = !visible" class="btn btn-outline-primary">
                        <i :class="visible ? 'mdi-visibility-off' : 'mdi-visibility'" class="mdi"></i>
                    </button>
                </div>
            </div>
            <small class="form-text text-muted">{{$t('user.password_empty_info')}}</small>
        </template>
    </AFormGroup>
</template>

<script>
    import AFormGroup from "../atoms/AFormGroup";

    export default {
        name: "AInput",
        components: {AFormGroup},

        data() {
            return {
                visible: false
            }
        },

        props: {
            required: {
                default: false,
                type: Boolean
            },
            value: {
                default: '',
                type: String
            }
        },
    }
</script>

<style scoped>

</style>
