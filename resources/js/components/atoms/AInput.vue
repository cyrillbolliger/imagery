<template>
    <div class="form-group row">
        <label :for="id" class="col-sm-2 col-form-label">
            {{$t('user.first_name')}}
            <span v-if="required">*</span>
        </label>
        <div class="col-sm-10">
            <input
                :id="id"
                :required="required"
                :type="type"
                :value="value"
                @input="$emit('input', $event.target.value)"
                class="form-control">
        </div>
    </div>
</template>

<script>
    export default {
        name: "AInput",
        props: {
            label: {
                required: true,
                type: String
            },
            type: {
                default: 'text',
                type: String
            },
            required: {
                default: false,
                type: Boolean
            },
            value: {
                default: '',
                type: String
            }
        },
        computed: {
            id() {
                // keep only 0-9, a-z, A-Z without any umlauts
                return this.label.replace(/[^0-9\x41-\x5A\x61-\x7A]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/-+/g, '-');
            }
        }
    }
</script>

<style scoped>

</style>
