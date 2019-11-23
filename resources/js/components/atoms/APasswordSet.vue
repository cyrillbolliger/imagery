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
                    :placeholder="required ? '' : $t('user.password_placeholder')"
                    :required="required"
                    :type="visible ? 'text' : 'password'"
                    :value="value"
                    @input="onInput"
                    class="form-control"
                    :class="validClass"
                    autocomplete="new-password"
                    id="password">
                <div class="input-group-append" v-if="value">
                    <button
                        :class="buttonValidClass"
                        @click.prevent="visible = !visible"
                        class="btn">
                        <i :class="visible ? 'mdi-visibility-off' : 'mdi-visibility'" class="mdi"></i>
                    </button>
                </div>
            </div>
            <small class="form-text text-muted" v-if="!value && !required">{{$t('user.password_empty_info')}}</small>
            <small class="form-text text-danger"
                   v-if="!$v.value.entropy">{{$t('user.password_insecure')}}</small>
            <small class="form-text text-danger"
                   v-if="!$v.value.required && $v.value.$error">{{$t('validation.required')}}</small>
        </template>
    </AFormGroup>
</template>

<script>
    import AFormGroup from "../atoms/AFormGroup";

    export default {
        name: "APasswordSet",
        components: {AFormGroup},

        data() {
            return {
                visible: false
            }
        },

        computed: {
            valid() {
                return this.$v.value.$dirty && !this.$v.value.$error;
            },
            validClass() {
                if (this.$v.value.$error) {
                    return 'is-invalid';
                } else if (this.valid) {
                    return 'is-valid';
                } else {
                    return '';
                }
            },
            buttonValidClass() {
                if (this.$v.value.$error) {
                    return 'btn-outline-danger';
                } else if (this.valid) {
                    return 'btn-outline-success';
                } else {
                    return 'btn-outline-primary';
                }
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

        validations: {
            value: {
                entropy(value) {
                    if ('' === value) {
                        return true;
                    }

                    if (!value || 'string' !== typeof value) {
                        return false;
                    }

                    let base = value.match(/\d/) ? 10 : 0;
                    base += value.match(/[a-z]/) ? 26 : 0;
                    base += value.match(/[A-Z]/) ? 26 : 0;
                    base += value.match(/[^a-zA-Z0-9]/) ? 10 : 0; // people tend to always use the same

                    const exp = value.length;
                    const entropy = base ** exp;

                    return entropy >= 2 ** 48;
                },

                required(value) {
                    return this.required ? value.length : true
                }
            },
        },

        methods: {
            onInput(event) {
                this.$emit('input', event.target.value);
                this.$v.value.$touch();
            }
        },
    }
</script>

<style scoped>

</style>
