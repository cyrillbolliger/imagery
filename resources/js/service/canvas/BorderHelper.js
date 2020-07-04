const borderWidthFactor = 0.0175;
const radiusFactor = 1;

export default {
    width(canvasWidth, canvasHeight) {
        const area = canvasWidth * canvasHeight;
        return Math.sqrt(area) * borderWidthFactor;
    },

    radius(borderWidth) {
        return borderWidth * radiusFactor;
    },
}
