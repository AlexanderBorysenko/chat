<template>
	<AppWrapper>
		<input type="password" v-model="password" />
		<div class="gallery" v-if="password === correctPassword">
			<div
				v-for="image in props.images"
				:key="image"
				class="gallery-item"
			>
				<img :src="image" loading="lazy" alt="Gallery Image" />
			</div>
		</div>
	</AppWrapper>
</template>

<script setup lang="ts">
import AppWrapper from '@/Layouts/AppWrapper.vue';
import { ref } from 'vue';
const props = defineProps<{
	images: string[];
}>();
console.log(props.images);

const correctPassword = 'kotyky';
const password = ref('');
</script>

<style scoped lang="scss">
.gallery {
	display: grid;
	grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
	padding: 1rem;
	gap: 0.5rem;
}
.gallery-item {
	border-radius: 8px;
	overflow: hidden;
	box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
	img {
		width: 100%;
		height: 100%;
		object-fit: cover;
	}
}
</style>
