<template>
	<div class="user-card flex align-center p-8 pl-12">
		<div class="">
			<p class="uppercase fs-1-125">
				{{ chat.chat_name }}
			</p>
			<p class="fs-0-875">
				{{ chat.unread_messages_count }} Нових повідомлень
			</p>
		</div>
		<Link
			class="btn w-fit-content lh-0 ml-auto flex align-center"
			:href="route('chat.show', chat.id)"
		>
			<IoOutlinePaw class="fs-2 mr-8" />
			Жамк
		</Link>
	</div>
</template>

<script setup lang="ts">
import { TChat } from '@/types/TChat';
import { Link } from '@inertiajs/vue3';
import { IoOutlinePaw } from '@kalimahapps/vue-icons';
const props = defineProps<{
	chat: TChat;
}>();
</script>

<style scoped lang="scss">
.user-card {
	background: rgba($color: #fae6fa, $alpha: 0.3);
}
</style>
