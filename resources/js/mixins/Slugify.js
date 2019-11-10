export default {
    methods: {
        slugify(string) {
            // keep only 0-9, a-z, A-Z without any umlauts
            return string.replace(/[^0-9\x41-\x5A\x61-\x7A]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-');
        }
    }
}

