<template>
    <div class="help-block animation-slideUp">
        <div v-for="error in errors">
            {{ error }}
        </div>
    </div>
</template>

<script>
    export default {
        props: ['errors'],
    }
</script>