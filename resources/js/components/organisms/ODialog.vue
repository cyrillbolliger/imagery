<template>
    <div class="o-dialog container-fluid">
        <div class="o-dialog__container container">
            <header class="o-dialog__header row pt-3 pb-2 d-flex justify-content-between align-items-start">
                <h5 class="ml-3">{{ title }}</h5>
                <button @click="$emit('close')" class="o-dialog__close close mr-3">
                    <span aria-hidden="true">&times;</span>
                </button>
            </header>
            <section class="row">
                <div class="o-dialog__content col pt-3 pb-3 border-bottom">
                    <slot></slot>
                </div>
            </section>
            <footer class="row">
                <div class="o-dialog__footer col pt-3 pb-3">
                    <slot name="footer"></slot>
                </div>
            </footer>
        </div>
    </div>
</template>

<script>
    export default {
        name: "ODialog",
        data() {
            return {}
        },
        props: {
            title: {
                required: true,
                type: String
            }
        },
    }
</script>

<style lang="scss" scoped>
    .o-dialog {
        position: fixed;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.3);
        top: 0;
        left: 0;
        z-index: $zindex-modal;

        &__container {
            max-width: 768px;
            width: 100%;
            background: $body-bg;
            box-shadow: $box-shadow;

            @include media-breakpoint-up(md) {
                max-width: 720px;
                margin-top: 24px;
                margin-bottom: 24px;
            }
        }

        &__header {
            background: $secondary;
            color: white;
        }

        &__close {
            color: #fff;
            text-shadow: none;
            opacity: 1;
        }

        &__content {
        }
    }
</style>
