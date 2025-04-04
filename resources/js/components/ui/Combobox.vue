<template>
    <div class="form-group position-relative" ref="combobox">
        <label>{{ label }}</label>
        <input
            :placeholder="placeholder"
            :class="inputClass"
            :id="inputId"
            :disabled="isDisabled"
            v-model="searchQuery"
            @input="filterOptions"
            @focus="showDropdown = true"
            @keydown.down.prevent="moveDown"
            @keydown.up.prevent="moveUp"
            @keydown.enter.prevent="selectHighlighted"
            @keydown.esc="showDropdown = false"
        />
        <ul v-if="showDropdown" class="dropdown-menu show w-100"
            style="max-height: 200px; overflow-y: auto;"
            @mouseenter="dropdownHovered = true"
            @mouseleave="dropdownHovered = false"
        >
            <li v-for="(option, index) in filteredOptions"
                :key="option.value"
                @mousedown.prevent="selectOption(option)"
                class="dropdown-item"
                :class="{ 'active': index === highlightedIndex }">
                {{ option.text }}
            </li>
            <li v-if="filteredOptions.length === 0" class="dropdown-item">Không có dữ liệu</li>
        </ul>
    </div>
</template>

<script>
export default {
    props: {
        label: { type: String, default: 'Chưa đặt tên' },
        placeholder: { type: String, default: '' },
        inputClass: { type: String, default: 'form-control' },
        inputId: { type: String, default: '' },
        modelValue: { type: [String, Number], default: '' },
        options: { type: Array, default: () => [] },
        isDisabled: {
            type: Boolean,
            default: false,
        }
    },
    data() {
        return {
            searchQuery: this.modelValue,
            showDropdown: false,
            dropdownHovered: false,
            highlightedIndex: -1,
        };
    },
    computed: {
        filteredOptions() {
            if (!this.searchQuery) return this.options;
            return this.options.filter(option =>
                option.text.toLowerCase().includes(this.searchQuery.toLowerCase())
            );
        },
    },
    watch: {
        modelValue(newVal) {
            const selectedOption = this.options.find(option => option.value === newVal);
            this.searchQuery = selectedOption ? selectedOption.text : newVal;

            if (!selectedOption) {
                this.$nextTick(() => {
                    const updatedOption = this.options.find(option => option.value === newVal);
                    if (updatedOption) {
                        this.searchQuery = updatedOption.text;
                    }
                });
            }
        },
        showDropdown(newValue) {
            if (newValue) {
                document.addEventListener("click", this.clickOutside);
            } else {
                document.removeEventListener("click", this.clickOutside);
            }
        }
    },
    methods: {
        filterOptions() {
            this.showDropdown = true;
            this.highlightedIndex = -1;
        },
        selectOption(option) {
            this.searchQuery = option.text;
            this.$emit('update:modelValue', option.value);
            this.showDropdown = false;
        },
        selectHighlighted() {
            if (this.highlightedIndex !== -1) {
                this.selectOption(this.filteredOptions[this.highlightedIndex]);
            }
        },
        moveDown() {
            if (this.highlightedIndex < this.filteredOptions.length - 1) {
                this.highlightedIndex++;
            }
        },
        moveUp() {
            if (this.highlightedIndex > 0) {
                this.highlightedIndex--;
            }
        },
        clickOutside(event) {
            if (this.$refs.combobox && !this.$refs.combobox.contains(event.target)) {
                this.showDropdown = false;
            }
        }
    }
};
</script>

<style scoped>
.dropdown-menu {
    position: absolute;
    top: 100%;
    left: 0;
    z-index: 1000;
}

.dropdown-item {
    cursor: pointer;
}

.dropdown-item:hover, .dropdown-item.active {
    background-color: #007bff;
    color: #fff;
}
</style>
