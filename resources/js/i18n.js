/**
 * Copied from https://www.codeandweb.com/babeledit/tutorials/how-to-translate-your-vue-app-with-vue-i18n
 * with slight adaptions
 */
import Vue from 'vue';
import VueI18n from 'vue-i18n';

Vue.use(VueI18n);

function loadLocaleMessages() {
    const locales = require.context('./locales', true, /[A-Za-z0-9-_,\s]+\.json$/i);
    const messages = {};
    locales.keys().forEach(key => {
        const matched = key.match(/([A-Za-z0-9-_]+)\./i);
        if (matched && matched.length > 1) {
            const locale = matched[1];
            messages[locale] = locales(key);
        }
    });
    return messages;
}

function detectLocal() {
    if (window.crowdin_lang) {
        return window.crowdin_lang;
    }

    if (window.user) {
        return window.user.lang;
    }

    return navigator.language;
}

export default new VueI18n({
    locale: detectLocal(),
    fallbackLocale: 'en',
    messages: loadLocaleMessages()
})
