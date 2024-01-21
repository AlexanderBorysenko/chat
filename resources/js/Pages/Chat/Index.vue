<template>
	<AppWrapper>
		<div class="p-8">
			<ChatCard
				v-for="chat in chats"
				:key="chat.id"
				:chat="chat"
				class="mb-8"
			/>
		</div>
	</AppWrapper>
</template>

<script setup lang="ts">
import ChatCard from '@/Components/ChatCard.vue';
import UserDirectMessagesCard from '@/Components/UserDirectMessagesCard.vue';
import AppWrapper from '@/Layouts/AppWrapper.vue';
import { TChat } from '@/types/TChat';
const props = defineProps<{
	chats: TChat[];
}>();
</script>

<style scoped></style>
