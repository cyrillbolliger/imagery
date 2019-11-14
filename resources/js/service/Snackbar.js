const SUCCESS = 'success';
const ERROR = 'error';

const TIMEOUT_SUCCESS = 3000;

class Snackbar {
    /**
     * Constructor.
     *
     * @param {String} message
     * @param {SUCCESS|ERROR} type
     * @param {String|null} actionLabel optional
     */
    constructor(
        message,
        type,
        actionLabel = null,
    ) {
        this.message = message;
        this.type = type;
        this.actionLabel = actionLabel;
    }

    /**
     * Let the store handle this.
     *
     * @param id
     */
    setId(id) {
        this.id = id;
    }

    /**
     * Returns a promise. If the Snackbar type is SUCCESS it will
     * resolve automatically after TIMEOUT_SUCCESS milliseconds.
     */
    launch() {
        if (SUCCESS === this.type) {
            // resolve after timeout success millis
            return new Promise(resolve => {
                this.resolve = resolve;
                setTimeout(() => resolve(this), TIMEOUT_SUCCESS);
            });
        } else {
            // do not resolve automatically
            return new Promise(resolve => {
                this.resolve = resolve;
            });
        }
    }

    /**
     * Resolves the promise from launch. Fire this in button click.
     */
    doAction() {
        this.resolve(this);
    }
}

export {
    Snackbar,
    SUCCESS,
    ERROR
}
