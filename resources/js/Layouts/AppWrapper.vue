<template>
	<div class="app-wrapper">
		<div class="inner flex flex-column">
			<div
				class="header ui-panel sticky flex align-center justify-space-between gap-1"
			>
				<button
					@click="logout"
					class="logout btn-danger w-fit-content lh-0"
				>
					<BxLogOut class="fs-2" />
				</button>
				<button @click="erase" class="logout btn-danger w-fit-content">
					Очистити
				</button>
				<Link
					class="btn- w-fit-content lh-0"
					:href="route('directMessages.index')"
				>
					<BxChat class="fs-2" />
				</Link>
			</div>
			<slot />
		</div>
	</div>
</template>

<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import { BxLogOut, BxChat } from '@kalimahapps/vue-icons';
import { onMounted } from 'vue';
import { ref } from 'vue';

const logout = () => {
	const html = document.querySelector('html');
	if (html) html.innerHTML = '';

	router.post(route('logout'));
};
const erase = () => {
	const html = document.querySelector('html');
	if (html) html.innerHTML = '';

	router.post(route('directMessages.erase'));
};

const viewportHeight = ref('100vh');
onMounted(() => {
	viewportHeight.value = `${window.innerHeight}px`;
});
</script>

<style scoped lang="scss">
.app-wrapper {
	width: 100%;
	height: 100vh;
	height: v-bind(viewportHeight);
	padding: 4px;
}
.header {
	padding: 8px;
	position: sticky;
	top: 0;
}
.inner {
	max-width: 744px;
	height: 100%;
	margin-left: auto;
	margin-right: auto;
	border-radius: 8px;
	background: rgba($color: #fff2fdb8, $alpha: 0.72);
	border: 1px solid #fff;
	backdrop-filter: blur(2px);
	border-radius: 8px;
	// box-shadow: 0 0 10px rgba($color: #ccccff, $alpha: 0.3);
}
.logout {
	svg {
		line-height: 0;
	}
}
</style>
