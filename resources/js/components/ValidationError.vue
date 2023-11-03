<script setup>
const props = defineProps({
    validationErrors: {
        required: false,
    },
})
</script>

<template>
    <VAlert v-if="props.validationErrors" :color="'error'" :icon="(this.isUpdated == 200) ? $success : $error">
        <ul class="alert alert-danger" v-for="(value) in props.validationErrors">
            <li v-for="(value2) in value">{{ value2 }}</li>
        </ul>
    </VAlert>
</template>
