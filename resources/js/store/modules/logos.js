import resource from "./resource";

const baseEndpoint = 'logos';

export default function getLogoStore(type) {
    const module = resource(baseEndpoint);
    const loadAction = module.actions.load;

    module.actions.load = function ({commit, state}) {
        return loadAction({commit, state}, true, `${baseEndpoint}/${type}`);
    };

    return module;
}
