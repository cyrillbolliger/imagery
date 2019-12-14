<template>
    <div>
        <label class="mb-0 d-block" v-if="title">{{title}}</label>
        <div class="btn-group btn-group-toggle">
            <label
                :class="{'active': current === option.value}"
                class="btn btn-secondary btn-sm"
                v-for="option in options">
                <input
                    :value="option.value"
                    @input="$emit('input', option.value)"
                    name="background"
                    type="radio"
                    v-model="current"
                >{{option.text}}
            </label>
        </div>
    </div>
</template>
<script>
    export default {
        name: 'AButtonGroup',
        data() {
            return {
                current: this.value,
            }
        },
        props: {
            options: {
                required: true,
                type: Array
            },
            value: {
                default: null,
            },
            title: {
                type: String,
            },
        },
        watch: {
            value(value) {
                this.current = value;
            }
        }
    }
</script>
