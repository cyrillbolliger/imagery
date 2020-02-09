<template>
    <AFormGroup>
        <template #label>
            <label :for="id" class="col-form-label">
                {{label}}
                <span v-if="required">*</span>
            </label>
        </template>
        <template #input>
            <!-- set random name to disable chrome's wrong autofill guess -->
            <ModelSelect
                :id="id"
                :name="'a'+Math.random().toString(36).substring(8)"
                :options="options"
                :required="required"
                :value="value"
                :class="validClass"
                :disabled="disabled"
                @input="onInput"
                class="a-select__select form-control"/>
        </template>
        <template #helptext v-if="helptext.length || (validation && $v.value.$error)">
            {{helptext}}
            <div v-if="validation && $v.value.$error">
                <div v-if="validation.messages">
                    <div v-for="(message, key) in validation.messages">
                        <span class="text-danger" v-if="false === $v.value[key]">{{message}}</span>
                    </div>
                </div>
                <div v-else>
                    <span class="text-danger">{{validation.message}}</span>
                </div>
            </div>
        </template>
    </AFormGroup>
</template>

<script>
    import SlugifyMixin from "../../mixins/SlugifyMixin";
    import AFormGroup from "../atoms/AFormGroup";
    import {ModelSelect} from 'vue-search-select'

    export default {
        name: "ASelect",
        components: {AFormGroup, ModelSelect},
        mixins: [SlugifyMixin],
        computed: {
            id() {
                return this.slugify(this.label)
            },
            validClass() {
                if (!this.validation) {
                    return;
                }

                if (this.$v.value.$error) {
                    return 'is-invalid';
                }

                if (this.$v.value.$dirty && !this.$v.value.$error) {
                    return 'is-valid';
                }

                return '';
            }
        },
        props: {
            label: {
                required: true,
                type: String
            },
            options: {
                required: true,
            },
            required: {
                default: false,
                type: Boolean
            },
            value: {
                required: true,
            },
            helptext: {
                type: String,
                default: ''
            },
            disabled: {
                type: Boolean,
                default: false
            },
            validation: {
                default: null,
                type: Object
            }
        },
        validations() {
            if (this.validation) {
                return {
                    value: this.validation.rules
                }
            }
        },
        methods: {
            onInput(event) {
                this.$emit('input', event);

                if (this.validation) {
                    this.$v.value.$touch();
                }
            }
        },
    }
</script>

<style lang="scss" scoped>
    .a-select {
        &__select {
            &.is-valid {
                border-color: $success;
            }

            &.is-invalid {
                border-color: $danger;
            }
        }
    }
</style>
