<template>
    <div class="o-data-table">
        <div class="mb-3 row justify-content-between">
            <button @click="$emit('newEntry')"
                    class="btn btn-outline-primary ml-3"
                    v-if="newEntryLabel"
            >{{newEntryLabel}}
            </button>
            <div v-if="showSearch"
                 class="o-data-table__search col"
            >
                <MSearch @search="filter($event)"></MSearch>
            </div>
        </div>
        <table class="o-data-table__table table table-hover">
            <thead>
            <tr>
                <th class="o-data-table__header-cell border-bottom-0"
                    v-for="{label, sortable, key} in headers"
                    scope="col"
                >
                    <AButtonSort
                        :direction="getSortDirection(key)"
                        @directionChanged="sort(key, $event)"
                        v-if="sortable"
                    >{{ label }}
                    </AButtonSort>
                    <span v-else>{{ label }}</span>
                </th>
            </tr>
            </thead>
            <tbody v-if="loading">
            <tr>
                <td :colspan="headers.length" class="border-top-0 p-0">
                    <ALoaderBar></ALoaderBar>
                </td>
            </tr>
            <tr class="o-data-table__loading">
                <td :colspan="headers.length"
                    class="border-top-0"
                >{{ $t('table.loading') }}
                </td>
            </tr>
            </tbody>
            <tbody v-if="!loading">
            <tr class="o-data-table__row" v-for="row in sortedRows">
                <td v-for="({key, transformation}, index) in headers">
                    <span v-if="transformation" v-html="transformation(row[key])"></span>
                    <span v-else>{{ row[key] }}</span>
                    <button @click="$emit('details', row[actionKey])"
                            class="o-data-table__item-action btn btn-link p-0 ml-2"
                            v-if="0 === index"
                    >{{detailsLabel}}
                    </button>
                </td>
            </tr>
            </tbody>
            <tfoot v-if="!loading">
            <tr>
                <td :colspan="headers.length"
                    class="o-data-table__footer-cell"
                >{{ totalRows }}
                </td>
            </tr>
            </tfoot>
        </table>
    </div>
</template>

<script>
    import ALoaderBar from "../atoms/ALoaderBar";
    import AButtonSort from "../atoms/AButtonSort";
    import MSearch from "../molecules/MSearch";

    const MIN_ITEMS_FOR_SEARCH = 6;

    export default {
        name: "ODataTable",
        components: {MSearch, AButtonSort, ALoaderBar},
        data() {
            return {
                sortedRows: [],
                filterTerm: '',
                filteredRows: [],
                direction: this.sortDirection,
                by: this.sortBy,
            }
        },
        props: {
            headers: {
                required: true,
                type: Array
            },
            rows: {
                required: true,
            },
            loading: {
                required: true,
                type: Boolean,
            },
            actionKey: {
                required: true,
                type: String
            },
            sortBy: {
                type: String,
                default: null
            },
            sortDirection: {
                type: String,
                default: 'asc',
                validator(value) {
                    return ['asc', 'desc'].includes(value);
                }
            },
            detailsLabel: {
                type: String,
                default() {
                    return this.$t('table.details');
                }
            },
            newEntryLabel: {
                type: String,
                default() {
                    return this.$t('table.add');
                }
            },
        },
        computed: {
            totalRows() {
                const total = this.rows.length;
                const displayed = this.filteredRows.length;
                let filtered = '';

                if (this.filterTerm) {
                    filtered = this.$t('table.filtered') + ' ';
                }

                return filtered + this.$t('table.total', {total, displayed});
            },
            showSearch() {
                return this.filteredRows.length >= MIN_ITEMS_FOR_SEARCH;
            },
            searchableKeys() {
                return this.headers
                    .filter(header => {
                        return !(header.hasOwnProperty('searchable') && false === header.searchable)
                    })
                    .map(header => header.key);
            }
        },
        methods: {
            sort(field, direction) {
                this.by = field;
                this.direction = direction;
                this.sortedRows = sort(this.filteredRows, field, direction);
            },
            getSortDirection(field) {
                return field === this.by ? this.direction : null;
            },
            filter(term) {
                this.filterTerm = term;
                this.filteredRows = filter(this.rows, term, this.searchableKeys);
            }
        },
        watch: {
            rows(value) {
                this.filteredRows = filter(value, this.term, this.searchableKeys);
            },
            filteredRows(value) {
                this.sortedRows = sort(value, this.by, this.direction);
            }
        },
        mounted() {
            if (this.rows.length && !this.sortedRows.length) {
                // this populates the table when navigating away and back again
                this.filteredRows = filter(this.rows, this.term, this.searchableKeys);
            }
        }
    }

    const filter = function (data, term, keys) {
        if (!term) {
            return data;
        }

        term = term.toLowerCase();

        return data.filter(obj => {
            let haystack = '';

            for (let key of keys) {
                haystack = haystack.concat(' ', obj[key]);
            }

            return haystack.toLowerCase().includes(term);
        });
    };

    const sort = function (data, key, direction) {
        if (!direction || !key) {
            return data;
        }

        data = data.concat(); // make a shallow copy

        if ('asc' === direction) {
            return data.sort((a, b) => a[key].localeCompare(b[key]));
        }

        return data.sort((a, b) => b[key].localeCompare(a[key]));
    }
</script>

<style lang="scss" scoped>
    .o-data-table {
        &__loading {
            text-align: center;
            color: $gray-500;
        }

        &__footer-cell {
            color: $gray-500;
            font-size: 0.8125em;
        }

        &__search {
            width: 100%;
            max-width: 350px;
        }

        &__table {
            position: relative;
        }

        &__header-cell {
            position: sticky;
            top: -1px;
            background: #F3F4F5;

            color: $gray-600;
            font-size: 0.875em;
            text-transform: uppercase;

            &::after {
                content: ' ';
                height: 5px;
                display: block;
                position: absolute;
                bottom: -5px;
                left: 0;
                right: 0;
                box-shadow: inset 0 5px 5px -5px $gray-500;
            }
        }

        &__item-action {
            color: $body-bg;
            font-size: 0.875em;
        }

        &__row:hover &__item-action,
        &__row:focus &__item-action {
            color: $primary;
            cursor: pointer;
        }
    }
</style>
