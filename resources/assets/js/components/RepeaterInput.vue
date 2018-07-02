<template>
    <div class="fancy-form-group inline">
        <label>Answer:</label>
        <input v-bind:name="name" v-model="value" placeholder="Válasz" class="form-control"  type="text" required>
    </div>
</template>
<script>
    export default {
        props: ['name', 'value'],
        data: function() {
            return {
                value: ''
            };
        }
    }
</script>