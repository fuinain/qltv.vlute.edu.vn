<template>
    <div class="row">
        <div class="col-12">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th v-for="header in headers"
                            :key="header.key"
                            :style="header.css"
                            @click="sortTable(header.key)">
                            {{ header.label }}
                            <span v-if="sortKey === header.key">
                              <i v-if="sortOrder === 'asc'" class="fas fa-sort-amount-up"></i>
                              <i v-if="sortOrder === 'desc'" class="fas fa-sort-amount-up-alt"></i>
                            </span>
                        </th>
                        <th v-if="$slots.actions" :style="cssActions"></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="item in sortedData" :key="item.id">
                        <td v-for="header in headers" :key="header.key">
                            <slot v-if="$slots[header.key]" :name="header.key" :item="item"></slot>
                            <span v-else v-html="item[header.key]"></span>
                        </td>

                        <td v-if="$slots.actions">
                            <slot name="actions" :item="item"></slot>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <hr>
        </div>

        <div class="col-6">
            Có tổng cộng <b>{{ pagination.total }}</b> dòng dữ liệu.
        </div>

        <div class="col-6">
            <ul v-if="pagination.last_page > 1" class="pagination pagination-sm m-0 float-right">
                <li class="page-item" :class="{ disabled: !pagination.prev_page_url }">
                    <a class="page-link" href="#" @click.prevent="fetchPage(pagination.prev_page_url)">Trước</a>
                </li>

                <li v-if="pagination.current_page > 3" class="page-item">
                    <a class="page-link" href="#" @click.prevent="fetchPage(getPageUrl(1))">1</a>
                </li>
                <li v-if="pagination.current_page > 4" class="page-item disabled">
                    <span class="page-link">...</span>
                </li>

                <li v-for="page in visiblePages" :key="page" class="page-item" :class="{ active: pagination.current_page === page }">
                    <a class="page-link" href="#" @click.prevent="fetchPage(getPageUrl(page))">{{ page }}</a>
                </li>

                <li v-if="pagination.current_page < pagination.last_page - 3" class="page-item disabled">
                    <span class="page-link">...</span>
                </li>
                <li v-if="pagination.current_page < pagination.last_page - 2" class="page-item">
                    <a class="page-link" href="#" @click.prevent="fetchPage(getPageUrl(pagination.last_page))">{{ pagination.last_page }}</a>
                </li>

                <li class="page-item" :class="{ disabled: !pagination.next_page_url }">
                    <a class="page-link" href="#" @click.prevent="fetchPage(pagination.next_page_url)">Sau</a>
                </li>
            </ul>
        </div>

    </div>
</template>

<script>
export default {
    props: {
        data: {
            type: Array,
            required: true
        },
        headers: {
            type: Array,
            required: true
        },
        pagination: {
            type: Object,
            required: true
        },
        fetchPage: {
            type: Function,
            required: true
        },
        cssActions: {
            type: String,
            default: "width: 100px"
        },
        currentPage: {
            type: Number,
            required: true
        },
    },
    data() {
        return {
            sortKey: '',
            sortOrder: 'asc',
        };
    },
    computed: {
        sortedData() {
            let sortedData = [...this.data];

            if (this.sortKey) {
                sortedData.sort((a, b) => {
                    let result = 0;
                    const valA = a[this.sortKey] || '';
                    const valB = b[this.sortKey] || '';
                    if (valA < valB) result = -1;
                    if (valA > valB) result = 1;
                    return this.sortOrder === 'asc' ? result : -result;
                });
            }

            return sortedData;
        },

        visiblePages() {
            const { current_page, last_page } = this.pagination;
            let start = Math.max(current_page - 2, 1);
            let end = Math.min(current_page + 2, last_page);

            return Array.from({ length: (end - start + 1) }, (_, i) => start + i);
        }
    },
    methods: {
        sortTable(key) {
            if (this.sortKey === key) {
                this.sortOrder = this.sortOrder === 'asc' ? 'desc' : 'asc';
            } else {
                this.sortKey = key;
                this.sortOrder = 'asc';
            }
        },
        getPageUrl(page) {
            return `${this.pagination.path}?page=${page}`;
        },
    }
};
</script>

<style>
.icon-edit {
    font-size: 11px;
    background: #0c84ff;
    color: white;
    padding: 5px 3px 5px 5px;
    border-radius: 50%;
    margin-right: 5px;
    width: 25px;
    height: 25px;
    line-height: 16px;
    text-align: center;
}

.icon-delete {
    font-size: 11px;
    background: red;
    color: white;
    padding: 5px;
    border-radius: 50%;
    margin-right: 5px;
    width: 25px;
    height: 25px;
    line-height: 16px;
    text-align: center;
}

.table td, .table th {
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

    .table th, .table td {
        padding: 10px;
        font-size: 15px;
    }

    .pagination {
        justify-content: center;
    }
}
</style>
