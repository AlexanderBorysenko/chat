<template>
	<AppWrapper>
		<div class="gallery">
			<input
				type="password"
				:value="password"
				@input="(e:Event) => password = (e.target as HTMLInputElement).value"
			/>
			<div
				v-if="password === correctPassword"
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

const correctPassword = 'kotyky';
const password = ref('');
</script>

<style scoped lang="scss">
.gallery {
	display: grid;
	grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
	gap: 1rem;
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
