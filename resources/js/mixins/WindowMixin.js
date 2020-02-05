import {isXl} from "../service/Window";
import debounce from 'lodash/debounce';

export default {
    data() {
        return {
            isXl: this.setIsXl(),
        }
    },
    methods: {
        setIsXl: debounce(function () {
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
