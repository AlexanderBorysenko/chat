<template>
	<AppWrapper>
		<input type="password" v-model="password" />
		<div class="gallery" v-if="password === correctPassword">
			<div v-for="image in images" :key="image" class="gallery-item">
				<img :src="image" loading="lazy" alt="Gallery Image" />
			</div>
		</div>
	</AppWrapper>
</template>

<script setup lang="ts">
import AppWrapper from '@/Layouts/AppWrapper.vue';
import { computed, ref } from 'vue';
const props = defineProps<{
	images: string[];
}>();
const images = computed(() => props.images.reverse());
const correctPassword = 'kotyky';
const password = ref('');
</script>

<style scoped lang="scss">
.gallery {
	display: grid;
	grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
	padding: 1rem;
	gap: 0.5rem;
	flex: 1;
	overflow: auto;
}
.gallery-item {
	border-radius: 8px;
	overflow: hidden;
	box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
	min-height: 25rem;
	img {
		width: 100%;
		height: 100%;
		object-fit: cover;
	}
}
</style>
