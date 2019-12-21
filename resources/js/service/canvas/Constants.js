export const Alignments = {
    left: -1,
    center: 0,
    right: 1,
};

export const BackgroundTypes = {
    gradient: 'gradient',
    transparent: 'transparent',
    image: 'custom'
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

export const ColorSchemes = {
    white: 'white',
    green: 'green',
    greengreen: 'green-green',
};

export const BarTypes = {
    headline: 'SanukFat',
    subline: 'SanukBold'
};

export const LogoTypes = {
    alternative: 'alternative',
    gruene: 'gruene',
    'gruene-verts': 'gruene-verts',
    verda: 'verda',
    verdi: 'verdi',
    verts: 'verts',
};

export const LogoSublineRatios = {
    [LogoTypes.alternative]: {
        topMargin: 0.1,
        left: 0.3,
        fontSize: 0.3
    },
    [LogoTypes.gruene]: {
        topMargin: 0.032,
        left: 0.33,
        fontSize: 0.14225
    },
    [LogoTypes["gruene-verts"]]: {
        topMargin: 0.0255, // todo
        left: 0.3375,
        fontSize: 0.1425
    },
    [LogoTypes.verda]: {
        topMargin: 0.0255,
        left: 0.3375,
        fontSize: 0.1425
    },
    [LogoTypes.verdi]: {
        topMargin: 0.0255, // todo
        left: 0.3375,
        fontSize: 0.1425
    },
    [LogoTypes.verts]: {
        topMargin: 0.0255, // todo
        left: 0.3375,
        fontSize: 0.1425
    },
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
