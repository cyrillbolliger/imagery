import * as Window from "../service/Window";
import debounce from 'lodash/debounce';

export default {
    data() {
        return {
            viewWidth: Window.viewWidth(),
            viewHeight: Window.viewHeight(),
            isSm: Window.isSm(),
            isMd: Window.isMd(),
            isLg: Window.isLg(),
            isXl: Window.isXl(),
        }
    },
    methods: {
        onResize: debounce(function () {
            this.viewWidth = Window.viewWidth();
            this.viewHeight = Window.viewHeight();
            this.isSm = Window.isSm();
            this.isMd = Window.isMd();
            this.isLg = Window.isLg();
            this.isXl = Window.isXl();
        }, 100),
    },
    created: function () {
        window.addEventListener('resize', this.onResize)
    },
    beforeDestroy: function () {
        window.removeEventListener('resize', this.onResize)
    }
}
