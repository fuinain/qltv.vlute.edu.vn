<template>
    <div class="row">
        <div class="col-12">
            <div class="table-responsive">
                <div class="card-header pt-0 d-flex justify-content-end pr-0">
                    <input v-model="searchQuery" type="text" class="form-control form-control-sm w-auto"
                           style="min-width: 200px"
                           placeholder="Tìm kiếm..."/>
                </div>
                <!-- Table -->
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th v-for="column in columns" :key="column.key"
                            @click="column.sortable !== false && sortBy(column.key)"
                            :style="{ cursor: column.sortable === false ? 'default' : 'pointer' }">
                            {{ column.label }}
                            <span v-if="sortColumn === column.key">
                  <i v-if="sortAsc" class="fas fa-sort-up"></i>
                  <i v-else class="fas fa-sort-down"></i>
                </span>
                        </th>

                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(row, rowIndex) in filteredAndSortedData" :key="row.id || rowIndex">
                        <td v-for="column in columns" :key="column.key">
                            <slot :name="`column-${column.key}`" :row="row" :row-index="rowIndex">
                                <template v-if="column.key === 'index'">
                                    {{ calculateSTT(rowIndex) }}
                                </template>
                                <template v-else-if="row[column.key] !== undefined">
                                    {{ formatValue(row[column.key], column.format) }}
                                </template>
                                <template v-else>
                                    <em class="text-muted">Không có dữ liệu</em>
                                </template>
                            </slot>

                        </td>
                    </tr>
                    </tbody>
                </table>
                <!-- Pagination -->
                <nav v-if="pagination" class="mt-3">
                    <ul class="pagination justify-content-center">
                        <li class="page-item" :class="{ disabled: !pagination.prev_page_url }">
                            <button class="page-link" @click="goToPage(pagination.current_page - 1)">Trước</button>
                        </li>
                        <li class="page-item disabled">
                            <span class="page-link">Trang {{ pagination.current_page }}</span>
                        </li>
                        <li class="page-item" :class="{ disabled: !pagination.next_page_url }">
                            <button class="page-link" @click="goToPage(pagination.current_page + 1)">Tiếp</button>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>

    </div>
</template>

<script>
import dayjs from "dayjs";

export default {
    name: "Table",
    props: {
        columns: {
            type: Array,
            required: true,
        },
        data: {
            type: Array,
            required: true,
        },
        pagination: {
            type: Object,
            required: false,
        },
        fetchData: {
            type: Function,
            required: false,
        },
    },
    data() {
        return {
            searchQuery: "",
            sortColumn: null,
            sortAsc: true,
        };
    },
    computed: {
        filteredData() {
            if (!this.searchQuery) return this.data;

            const keyword = this.searchQuery.toLowerCase();

            return this.data.filter((row) => {
                return this.columns.some((column) => {
                    const value = row[column.key];
                    return value && value.toString().toLowerCase().includes(keyword);
                });
            });
        },
        filteredAndSortedData() {
            let result = [...this.filteredData];

            if (this.sortColumn) {
                result.sort((a, b) => {
                    const valA = a[this.sortColumn];
                    const valB = b[this.sortColumn];

                    if (valA == null) return 1;
                    if (valB == null) return -1;
                    if (valA === valB) return 0;

                    // So sánh theo kiểu chuỗi
                    return this.sortAsc
                        ? valA.toString().localeCompare(valB.toString(), undefined, {numeric: true})
                        : valB.toString().localeCompare(valA.toString(), undefined, {numeric: true});
                });
            }

            return result;
        },
    },
    methods: {
        formatValue(value, format) {
            if (format === "datetime") {
                return value ? dayjs(value).format("DD/MM/YYYY HH:mm:ss") : "–";
            } else if (format === "normalDateTime") {
                return value ? dayjs(value).format("DD/MM/YYYY") : "";
            }
            return value;
        },
        sortBy(columnKey) {
            if (this.sortColumn === columnKey) {
                this.sortAsc = !this.sortAsc;
            } else {
                this.sortColumn = columnKey;
                this.sortAsc = true;
            }
        },
        goToPage(page) {
            if (this.fetchData && page > 0 && page <= this.pagination.last_page) {
                this.fetchData(page);
            }
        },
        calculateSTT(rowIndex) {
            if (this.pagination && this.pagination.from) {
                return this.pagination.from + rowIndex;
            }
            return rowIndex + 1;
        },
    },
};
</script>

<style>
.table td,
.table th {
    padding: .4rem !important;
    vertical-align: middle;
    border-top: 1px solid #dee2e6;
    font-size: 15px;
}

.table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

@media (max-width: 768px) {
    .table {
        display: block;
        width: 100%;
        overflow-x: auto;
        white-space: nowrap;
    }

    .table th,
    .table td {
        padding: 10px;
        font-size: 15px;
    }

    .pagination {
        justify-content: center;
    }
}
</style>
