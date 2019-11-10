import Api from '../service/api';

const resource = '/users';

export default {
    getAll() {
        return Api().get(resource);
    },

    get(userId) {
        return Api().get(`${resource}/${userId}`);
    },

    create(payload) {
        return Api().post(resource, payload);
    },

    save(userId, payload) {
        return Api().put(`${resource}/${userId}`, payload);
    },

    delete(userId) {
        return Api().delete(`${resource}/${userId}`);
    }
}
