<template>
    <div>
        <div :key="node.id" class="m-group-tree-node" v-for="(node, index) in nodes">
            <AGroupSelect
                :manage="node.manage || manageAll"
                :manage-disabled="manageAll"
                :name="node.name"
                :use="node.use || node.manage || useAll || manageAll"
                :use-disabled="useAll || manageAll || node.manage"
                @manage="manage(index, $event)"
                @use="use(index, $event)"
            ></AGroupSelect>
            <MGroupTreeNode
                :manage-all="node.manage || manageAll"
                :use-all="node.manage || useAll || manageAll"
                @input="children(index, $event)"
                class="pl-3"
                v-if="node.children"
                v-model="node.children"
            ></MGroupTreeNode>
        </div>
    </div>
</template>

<script>
    import AGroupSelect from "../atoms/AGroupSelect";

    export default {
        name: "MGroupTreeNode",
        components: {AGroupSelect},
        data() {
            return {
                nodes: this.value,
            }
        },

        watch: {
            useAll(value) {
                if (value) {
                    this.nodes.forEach((value, index) => this.use(index, false));
                }
            },
            manageAll(value) {
                if (value) {
                    this.nodes.forEach((value, index) => this.manage(index, false));
                }
            }
        },

        props: {
            value: {
                type: Array,
                required: true
            },
            useAll: {
                type: Boolean,
                required: true
            },
            manageAll: {
                type: Boolean,
                required: true
            }
        },

        methods: {
            use(index, value) {
                this.set('use', index, value);
            },
            manage(index, value) {
                this.set('manage', index, value);
                if (value) {
                    this.set('use', index, false);
                }
            },
            children(index, value) {
                this.set('children', index, value);
            },
            set(property, index, value) {
                this.$set(this.nodes[index], property, value);
                this.$emit('input', this.nodes);
            }
        },
    }
</script>

<style scoped>

</style>
