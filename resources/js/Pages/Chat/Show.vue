<template>
	<AppWrapper :chat="props.chat">
		<div class="messages-wrapper col p-8" ref="messagesWrapper">
			<TransitionGroup name="fade">
				<Message
					v-for="message in messages"
					:key="message.id"
					:message="message"
					:isMine="message.sender_id === auth.user.id"
				/>
			</TransitionGroup>
		</div>
		<MessageSender :chat="props.chat" @messageSent="scrollToView" />
	</AppWrapper>
</template>

<script setup lang="ts">
import { TChat } from '@/types/TChat';
import Message from '@/Components/Message.vue';
import { usePage } from '@inertiajs/vue3';
import AppWrapper from '@/Layouts/AppWrapper.vue';
import MessageSender from '@/Components/MessageSender.vue';
import { onUnmounted } from 'vue';
import { onMounted } from 'vue';
import { ref } from 'vue';
import { TMessage } from '@/types/TMessage';
import axios from 'axios';
import { TUser } from '@/types/TUser';

const props = defineProps<{
	chat: TChat;
}>();

const messages = ref(props.chat.messages);

const auth = usePage().props.auth;

const readMessages = async () => {
	await axios.post(route('chat.ajaxReadMessages', props.chat.id));
};

const messagesWrapper = ref<HTMLDivElement | null>(null);
const scrollToView = () => {
	setTimeout(() => {
		if (messagesWrapper.value) {
			messagesWrapper.value.scrollTop =
				messagesWrapper.value.scrollHeight;
		}
	}, 100);
};

const channel = ref<ReturnType<typeof window.Echo.private> | null>(null);

const subscribeToChannel = () => {
	console.log('Підписуюсь на канал');
	channel.value = window.Echo.private(`chat.${props.chat.id}`);
	channel.value
		?.listen('NewDirectMessage', (e: { message: TMessage }) => {
			messages.value?.push(e.message);
			console.log('Нове повідомлення', e.message);
			readMessages();
		})
		.listen(
			'ReadUserMessages',
			(e: { chat: TChat; readingUser: TUser }) => {
				console.log('Повідомлення прочитано', e.chat, e.readingUser);
				messages.value?.forEach(message => {
					if (e.readingUser.id !== auth.user.id) {
						message.read = true;
					}
				});
			}
		);
};

onMounted(() => {
	readMessages();
	subscribeToChannel();
	scrollToView();

	// handle channel disconnect
	channel.value?.error(() => {
		console.log('Сокет заглючив, перезавантажуюсь...');
		try {
			subscribeToChannel();
		} catch (e) {
			console.log('Не вдалося перезавантажити сокет');
			setTimeout(() => {
				subscribeToChannel();
			}, 2000);
		}
	});
});

onUnmounted(() => {
	channel.value
		?.stopListening('NewDirectMessage')
		.stopListening('ReadUserMessages');

	channel.value = null;
});
</script>

<style scoped lang="scss">
.messages-wrapper {
	overflow: auto;
	position: relative;
}
</style>
