<template>
    <div class="table-responsive" v-if="rows && rows.length">
        <table class="table table-bordered table-sm">
            <thead class="thead-light">
            <tr>
                <th style="min-width:140px">Trường cha</th>
                <th style="width:55px">Nhãn</th>
                <th style="width:55px">CT1</th>
                <th style="width:55px">CT2</th>
                <th style="width:70px">Con</th>
                <th>Nội dung</th>
                <th style="width:100px">Trường con</th>
            </tr>
            </thead>

            <tbody>
            <!-- ====== LẶP CÁC TRƯỜNG CHA ====== -->
            <template v-for="(parent, pIdx) in rows" :key="parent.id_bien_muc_truong_cha || pIdx">
                <!-- ---------- HÀNG CHA ---------- -->
                <tr class="table-primary">
                    <td class="align-middle">
                        <!-- xoá cha -->
                        <button
                            class="btn btn-xs btn-outline-danger mr-1"
                            @click="$emit('remove-parent', parent)">
                            <i class="fas fa-minus"></i>
                        </button>
                    </td>

                    <td>
                        <input v-model="parent.ma_truong"
                               class="form-control form-control-sm"
                               @change="$emit('update-parent', parent)"/>
                    </td>
                    <td>
                        <input v-model="parent.ct1"
                               class="form-control form-control-sm"
                               @change="$emit('update-parent', parent)"/>
                    </td>
                    <td>
                        <input v-model="parent.ct2"
                               class="form-control form-control-sm"
                               @change="$emit('update-parent', parent)"/>
                    </td>
                    <td></td>
                    <td></td>
                    <td class="text-center">
                        <button
                            class="btn btn-xs btn-outline-success"
                            @click="$emit('add-child', parent)">
                            <i class="fas fa-plus"></i>
                        </button>
                    </td>
                </tr>

                <!-- ---------- placeholder: chưa có CON ---------- -->
                <tr v-if="!parent.children || !parent.children.length">
                    <td colspan="4"></td>
                    <td colspan="2" class="text-muted">(Chưa có trường con)</td>
                    <td></td>
                </tr>

                <!-- ---------- LẶP CÁC TRƯỜNG CON ---------- -->
                <tr v-for="child in parent.children"
                    :key="child.id_bien_muc_truong_con">
                    <td colspan="4"></td>

                    <td>
                        <input v-model="child.ma_truong_con"
                               class="form-control form-control-sm"
                               @change="$emit('update-child', parent, child)"/>
                    </td>

                    <td>
                        <input v-model="child.noi_dung"
                               class="form-control form-control-sm"
                               @change="$emit('update-child', parent, child)"/>
                    </td>

                    <td class="text-center">
                        <button
                            class="btn btn-xs btn-outline-danger"
                            @click="$emit('remove-child', parent, child)">
                            <i class="fas fa-minus"></i>
                        </button>
                    </td>
                </tr>
            </template>
            </tbody>
        </table>
    </div>
</template>

<script>
export default {
    name : "TableMarc",
    props: { rows: { type:Array, required:true } },
    emits: [
        "add-parent-after", "remove-parent",
        "add-child", "remove-child",
        "update-parent", "update-child",
    ],
};
</script>

<style scoped>
input.form-control-sm{
    width:100%;
    border:1px solid transparent;
    border-radius:0;
    padding:.25rem .4rem;
    background:transparent;
    box-shadow:none;
}
input.form-control-sm:hover  { border-color:#cb3d3d; }
input.form-control-sm:focus  { border-color:#007bff; background:#fff; outline:0; }
</style>
