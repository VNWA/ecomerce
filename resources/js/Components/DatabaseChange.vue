<template>
    <div>
        <div v-if="is_show" class="space-y-2">
            <select id="db_connection" v-model="selectedConnection" @change="changeDatabase"
                class="block w-[200px] px-4 py-2 text-sm border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none transition duration-200">
                <option value="en_db">English Database </option>
                <option value="es_db">Spanish Database</option>
            </select>
        </div>
    </div>
</template>

<script setup>
import { useForm, usePage } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
const page = usePage();
const is_show = ref(false)
const selectedConnection = ref('');

onMounted(() => {
    selectedConnection.value = page.props.current_db
    is_show.value = true
})
const changeDatabase = () => {
    const form = useForm({ db_connection: selectedConnection.value });
    form.post(route('Config.ChangeDatabase'), {
        preserveScroll: true,
        onSuccess: () => {
            console.log('Database connection changed!');
        },
    });
};

</script>
