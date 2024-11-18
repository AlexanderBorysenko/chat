<template>
	<div class="app-wrapper">
		<div class="inner flex flex-column">
			<div class="header ui-panel sticky flex align-center gap-1">
				<button
					@click="logout"
					class="logout btn-danger w-fit-content lh-0"
				>
					<BxLogOut class="fs-2" />
				</button>
				<button
					v-if="chat"
					@click="erase"
					class="logout btn-danger w-fit-content"
				>
					Очистити
				</button>
				<Link
					class="btn- w-fit-content lh-0"
					:href="route('gallery.index')"
				>
					<IoImages class="fs-2" />
				</Link>
				<Link
					class="btn- w-fit-content lh-0"
					:href="route('music.index')"
				>
					<IoMusicalNotes class="fs-2" />
				</Link>
				<Link
					class="btn- w-fit-content lh-0"
					:href="route('chat.index')"
				>
					<BxChat class="fs-2" />
				</Link>
			</div>
			<slot />
		</div>
	</div>
</template>

<script setup lang="ts">
import { TChat } from '@/types/TChat';
import { Link, router } from '@inertiajs/vue3';
import {
	BxLogOut,
	BxChat,
	IoMusicalNotes,
	IoImages
} from '@kalimahapps/vue-icons';
import { onUnmounted } from 'vue';
import { onMounted } from 'vue';
import { ref } from 'vue';

const prop = defineProps<{
	chat?: TChat;
}>();

const logout = () => {
	const html = document.querySelector('html');
	if (html) html.innerHTML = '';

	router.post(route('logout'));
};
const erase = () => {
	console.log(route('chat.erase', prop.chat?.id));
	const html = document.querySelector('html');
	if (html) html.innerHTML = '';

	router.post(route('chat.erase', prop.chat?.id));
};

const keydownListener = (e: KeyboardEvent) => {
	if ((e.ctrlKey && e.key === 'q') || (e.metaKey && e.key === 'q')) {
		logout();
	}
};
onMounted(() => {
	window.addEventListener('keydown', keydownListener);
});
onUnmounted(() => {
	window.removeEventListener('keydown', keydownListener);
});

const viewportHeight = ref('100vh');
const resizeListener = () => {
	// if is mobile return fale
	viewportHeight.value = `${window.innerHeight}px`;
};
onMounted(() => {
	resizeListener();
	window.addEventListener('resize', resizeListener);
	window.addEventListener('orientationchange', resizeListener);
});
onUnmounted(() => {
	window.removeEventListener('resize', resizeListener);
	window.removeEventListener('orientationchange', resizeListener);
});
</script>

<style scoped lang="scss">
.app-wrapper {
	width: 100%;
	height: 100vh;
	height: v-bind(viewportHeight);
	padding: 4px;
	background: url(../../asset-images/background-4.png) center center fixed;
	background-size: cover;
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
	border: 1px solid #fff;
	background: rgba(255, 255, 255, 0.7);
	backdrop-filter: blur(10px);
	border-radius: 8px;
	flex-wrap: nowrap;
	// box-shadow: 0 0 10px rgba($color: #ccccff, $alpha: 0.3);
}
.logout {
	margin-right: auto;
	svg {
		line-height: 0;
	}
}
</style>
