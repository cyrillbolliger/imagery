import {breakpoints} from "../constants";

export function isSm() {
    return viewWidth() >= breakpoints.sm;
}

export function isMd() {
    return viewWidth() >= breakpoints.md;
}

export function isLg() {
    return viewWidth() >= breakpoints.lg;
}

export function isXl() {
    return viewWidth() >= breakpoints.xl;
}

export function viewHeight() {
    return document.documentElement.clientHeight;
}

export function viewWidth() {
    return document.documentElement.clientWidth;
}
