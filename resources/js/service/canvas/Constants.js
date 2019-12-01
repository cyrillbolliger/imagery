export const Alignments = {
    left: 0,
    right: 1
};

export const BackgroundTypes = {
    gradient: 0,
    transparent: 1,
    image: 2
};

export const BarSchemes = {
    white: {
        background: '#ffffff',
        text: '#84b414',
    },
    green: {
        background: '#84b414',
        text: '#ffffff',
    },
    magenta: {
        background: '#e10078',
        text: '#ffffff',
    }
};

export const BarTypes = {
    headline: 'SanukFat',
    subline: 'SanukBold'
};

/**
 * Specifies the oversize of the bar in relation to the canvas width.
 *
 * 0.2 means that we'll add 20% of the canvas width to the bar.
 *
 * @type {number}
 */
export const BarSizeFactor = 0.2;

/**
 * If anything is rotated use this angle.
 *
 * @type {number}
 */
export const RotationAngle = -0.0872664626; // 5 degrees ccw in radians cw
