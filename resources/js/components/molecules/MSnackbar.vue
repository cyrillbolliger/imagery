<template>
    <transition name="fade">
        <div class="m-snackbar mt-1">
            <div class="p-2">
                <i class="mdi mdi-error-outline pr-2" v-if="isError"></i>
                <span class="m-snackbar__message">{{snackbar.message}}</span>
            </div>
            <div class="m-snackbar__actions">
                <button
                    @click="dismiss"
                    class="m-snackbar__action m-snackbar__action--secondary btn btn-link pl-2"
                    type="button"
                >{{$t('snackbar.dismiss')}}
                </button>
                <button
                    @click="doAction"
                    class="m-snackbar__action btn btn-link pl-2"
                    type="button"
                    v-if="hasAction"
                >{{snackbar.actionLabel}}
                </button>
            </div>
        </div>
    </transition>
</template>

<script>
    import * as Snackbar from "../../service/Snackbar";

    export default {
        name: "MSnackbar",
        computed: {
            isError() {
                return this.snackbar.type === Snackbar.ERROR;
            },
            hasAction() {
                return this.snackbar.actionLabel !== null;
            }
        },
        props: {
            snackbar: {
                required: true,
                validator: obj => obj instanceof Snackbar.Snackbar
            }
        },
        methods: {
            doAction() {
                this.snackbar.doAction();
            },
            dismiss() {
                this.$store.dispatch('snackbar/dismiss', this.snackbar);
            }
        }
    }
</script>

<style lang="scss" scoped>
    .m-snackbar {
        display: flex;
        align-items: center;
        background: $gray-800;
        color: white;
        width: 100%;
        max-width: 800px;
        justify-content: space-between;
        box-shadow: $box-shadow;

        &__actions {
            display: flex;
            flex-direction: column-reverse;

            @include media-breakpoint-up(sm) {
                flex-direction: row;
            }
        }

        &__action {
            font-weight: bold;
            color: lighten($primary, 20);

            &:hover, &:focus {
                color: lighten($primary, 40);
            }

            &--secondary {
                color: $gray-500;
            }
        }
    }
</style>
