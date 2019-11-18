export default {
    methods: {
        prepareSelectData(elements, valueKey, textKey) {
            return elements.map(element => ({
                    value: element[valueKey],
                    text: element[textKey]
                })
            ).sort((a, b) => a.text.localeCompare(b.text));
        },
    }
}
