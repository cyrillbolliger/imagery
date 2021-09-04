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
    'alternative-risch': 'alternative-risch',
    gruene: 'gruene',
    'gruene-vert-e-s': 'gruene-vert-e-s',
    'gruene-verts': 'gruene-verts',
    verda: 'verda',
    verdi: 'verdi',
    'vert-e-s': 'vert-e-s',
    verts: 'verts',
};

export const LogoSublineRatios = {
    [LogoTypes.alternative]: {
        topMargin: 0.1,
        left: 0.3,
        fontSize: 0.3
    },
    [LogoTypes['alternative-risch']]: {
        topMargin: 0.1,
        left: 0.3,
        fontSize: 0.3
    },
    [LogoTypes.gruene]: {
        topMargin: 0.032,
        left: 0.33,
        fontSize: 0.14225
    },
    [LogoTypes["gruene-vert-e-s"]]: {
        topMargin: 0.02,
        left: 0.26,
        fontSize: 0.094
    },
    [LogoTypes["gruene-verts"]]: {
        topMargin: 0.02175,
        left: 0.296,
        fontSize: 0.09375
    },
    [LogoTypes.verda]: {
        topMargin: 0.0255,
        left: 0.3375,
        fontSize: 0.1425
    },
    [LogoTypes.verdi]: {
        topMargin: 0.03,
        left: 0.32,
        fontSize: 0.141
    },
    [LogoTypes['vert-e-s']]: {
        topMargin: 0.0315,
        left: 0.2215,
        fontSize: 0.1425
    },
    [LogoTypes.verts]: {
        topMargin: 0.031,
        left: 0.25375,
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
