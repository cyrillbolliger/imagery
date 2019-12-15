import {isXl} from "../service/Window";

export default {
    data() {
        return {
            isXl: this.setIsXl(),
        }
    },
    methods: {
        setIsXl: _.debounce(function () {
            this.isXl = isXl();
        }, 100),
    },
    created: function () {
        this.setIsXl();
        window.addEventListener('resize', this.setIsXl)
    },
    beforeDestroy: function () {
        window.removeEventListener('resize', this.setIsXl)
    }
}
