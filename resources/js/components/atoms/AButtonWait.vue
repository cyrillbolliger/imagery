<template>
    <div class="d-flex align-items-center">
        <button
            :class="buttonClass"
            :disabled="disabled || working"
            @click.prevent="$emit('buttonClicked', $event)"
        >{{buttonText}}
        </button>
        <span
            class="d-block ml-3"
            role="status"
            v-if="working"
        >{{workingText}}</span>
        <div
            class="spinner-border spinner-border-sm text-primary ml-3"
            role="status"
            v-if="working"
        ></div>
    </div>
</template>

<script>
    export default {
        name: "AButtonWait",
        props: {
            disabled: {
                type: Boolean,
                default: false
            },
            buttonText: {
                type: String,
                required: true
            },
            workingText: {
                type: String,
                required: true
            },
            working: {
                type: Boolean,
                required: true,
            },
            buttonClass: {
                type: String,
                default: 'btn btn-primary'
            }
        }
    }
</script>

<style scoped>

</style>
