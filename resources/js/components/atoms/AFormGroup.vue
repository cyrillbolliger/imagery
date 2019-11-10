<template>
    <div class="form-group row">
        <div class="col-sm-3">
            <slot name="label"></slot>
        </div>
        <div class="col-sm-9">
            <slot name="input"></slot>
        </div>
    </div>
</template>

<script>
    export default {
        name: "AFormGroup"
    }
</script>

<style scoped>

</style>
