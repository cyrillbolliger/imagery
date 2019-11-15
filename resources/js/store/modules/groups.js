import resource from "./resource";

const module = resource('groups');

/**
 * Inject the property tree_name when using the getAll getter
 *
 * @param {array} state
 * @returns {array}
 */
module.getters.getAll = function (state) {
    for (let group of state.data) {
        group.tree_name = treeName(group, state.data);
    }

    return state.data;
};

/**
 * Get the name prefixed with the ancestor names
 *
 * @param {object} group
 * @param {array} groups
 * @returns {string}
 */
const treeName = function (group, groups) {
    if (!group.parent_id) {
        return group.name;
    }

    const parent = groups.filter(obj => obj.id === group.parent_id);
    if (!parent.length) {
        return group.name;
    }

    return treeName(parent[0], groups) + ' > ' + group.name;
};


export default module;
