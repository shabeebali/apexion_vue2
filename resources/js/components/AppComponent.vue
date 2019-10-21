<template>
	<router-view></router-view>
	
</template>

<script>
    export default {
        mounted() {
            console.log('Component mounted.')
            Echo.channel('category').listen('CategoryCreated', (e) => {
		    });
        }
    }
</script>
