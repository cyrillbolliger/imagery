<template>
    <div class="m-group-tree">
        <MGroupTreeNode
            :manage-all="false"
            :use-all="false"
            @input="emitRolesChanged($event)"
            v-model="tree">
        </MGroupTreeNode>
    </div>
</template>

<script>
    import MGroupTreeNode from "./MGroupTreeNode";


    export default {
        name: "MGroupTree",
        components: {MGroupTreeNode},
        data() {
            return {
                tree: this.getTree()
            }
        },

        props: {
            roles: {
                type: Array,
                required: true
            },
            groups: {
                type: Array,
                required: true
            },
            user: {
                type: Object,
                required: true
            }
        },

        methods: {
            getTree() {
                // inspired by https://stackoverflow.com/a/18018037
                const list = this.sortGroups(this.addRolesToGroups(this.groups));
                let map = {}, node, root, i;

                for (i = 0; i < list.length; i += 1) {
                    map[list[i].id] = i; // initialize the map
                    list[i].children = []; // initialize the children
                }

                for (i = 0; i < list.length; i += 1) {
                    node = list[i];
                    if (node.parent_id !== null) {
                        list[map[node.parent_id]].children.push(node);
                    } else {
                        root = node;
                    }
                }

                return [root];
            },

            getRoleByGroupId(groupId) {
                return this.roles.filter(role => role.group_id === groupId);
            },

            sortGroups(groups) {
                return groups.sort((a, b) => a.tree_name.localeCompare(b.tree_name));
            },

            addRolesToGroups(groups) {
                const roleGroupIds = this.roles.map(role => role.group_id);

                // add roles
                let use, manage;
                for (let group of groups) {
                    use = false;
                    manage = false;

                    if (roleGroupIds.includes(group.id)) {
                        const role = this.getRoleByGroupId(group.id)[0];
                        use = !role.admin;
                        manage = role.admin;
                    }

                    this.$set(group, 'use', use);
                    this.$set(group, 'manage', manage);
                }

                return groups;
            },

            emitRolesChanged(nodes) {
                let roles = [];

                for (let node of nodes) {
                    roles.push(...this.getRolesRecursive(node));
                }

                this.$emit('change', roles);
            },

            getRolesRecursive(node) {
                let roles = [];

                for (let child of node.children) {
                    roles.push(...this.getRolesRecursive(child));
                }

                if (node.use) {
                    roles.push(this.getRoleByGroupIdOrCreateNew(node.id, false));
                }

                if (node.manage) {
                    roles.push(this.getRoleByGroupIdOrCreateNew(node.id, true));
                }

                return roles;
            },

            getRoleByGroupIdOrCreateNew(groupId, admin) {
                const existing = this.roles.filter(role =>
                    role.user_id === this.user.id && role.group_id === groupId
                );

                if (existing.length) {
                    existing[0].admin = admin;
                    return existing[0];
                }

                return {
                    user_id: this.user.id,
                    group_id: groupId,
                    admin: admin
                };
            }
        }
    }
</script>

<style scoped>
    .m-group-tree {
        font-size: 0.9125em;
    }
</style>
