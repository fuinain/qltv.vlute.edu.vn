<!-- resources/js/components/Breadcrumb.vue -->
<template>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li
                v-for="(crumb, index) in breadcrumbItems"
                :key="index"
                :class="['breadcrumb-item', { active: index === breadcrumbItems.length - 1 }]"
            >
                <router-link v-if="crumb.path && index !== breadcrumbItems.length - 1" :to="crumb.path">
                    {{ crumb.name }}
                </router-link>
                <span v-else>{{ crumb.name }}</span>
            </li>
        </ol>
    </div>
</template>

<script>
import {useRoute} from 'vue-router';
import {computed} from 'vue';

export default {
    name: 'Breadcrumb',
    setup() {
        const route = useRoute();

        const breadcrumbItems = computed(() => {
            if (route.meta && Array.isArray(route.meta.breadcrumb)) {
                return route.meta.breadcrumb.map(item => {
                    return {
                        name: item.name,
                        path: typeof item.path === 'function' ? item.path(route) : item.path
                    };
                });
            }

            // fallback nếu không có breadcrumb thủ công
            return route.matched
                .filter(r => r.meta?.title)
                .map(r => ({
                    name: r.meta.title,
                    path: r.path
                }));
        });


        return {
            breadcrumbItems
        };
    }
};
</script>
