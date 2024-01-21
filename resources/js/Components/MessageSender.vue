<template>
	<div class="message-type-area flex p-8">
		<input
			type="file"
			class="btn hidden"
			ref="mediaFileInput"
			:disabled="mediaFileForm.processing"
			@change="onMediaFileInputChange"
		/>
		<button
			class="btn lh-0 mr-8"
			@click="mediaFileInput?.click()"
			:disabled="mediaFileForm.processing"
		>
			<BxCamera class="fs-2" />
		</button>
		<textarea
			type="text"
			class="form-control col mr-8"
			rows="2"
			ref="messageTextarea"
			v-model="messageForm.content"
			:disabled="messageForm.processing"
			@input="updateTyping"
		/>
		<button
			class="btn w-fit-content lh-0 ml-auto flex align-center"
			@click="submit"
			:disabled="messageForm.processing"
		>
			<IoOutlinePaw class="fs-2" />
		</button>
		<Transition name="fade">
			<span class="is-typing-message" v-if="isNyamNyamTyping"
				>Жамкає Лапкою...</span
			>
		</Transition>
	</div>
</template>

<script setup lang="ts">
import { TChat } from '@/types/TChat';
import { useForm, usePage } from '@inertiajs/vue3';
import { BxCamera } from '@kalimahapps/vue-icons';
import { IoOutlinePaw } from '@kalimahapps/vue-icons';
import { watch } from 'vue';
import { onUnmounted } from 'vue';
import { onMounted } from 'vue';
import { ref } from 'vue';

const messageForm = useForm({
	content: ''
});

const props = defineProps<{
	chat: TChat;
}>();

const auth = usePage().props.auth;

const messageTextarea = ref<HTMLTextAreaElement | null>(null);

const submit = () => {
	if (messageForm.content.length === 0) return;
	messageForm.post(route('message.store', props.chat.id), {
		onSuccess: () => {
			messageForm.reset();
		},
		onFinish: () => {
			messageTextarea.value?.focus();
			emit('messageSent');
		},
		preserveState: true
	});
};
onMounted(() => {
	messageTextarea.value?.focus();
});

// MEDIA FILE LOGIC
const mediaFileInput = ref<HTMLInputElement | null>(null);
const mediaFileForm = useForm<{
	file: File | null;
}>({
	file: null
});
const onMediaFileInputChange = (e: Event) => {
	const target = e.target as HTMLInputElement;
	if (target.files) {
		mediaFileForm.file = target.files[0];
	}
	submitMediaFile();
};

const submitMediaFile = () => {
	if (!mediaFileForm.file) return;
	mediaFileForm.post(route('message.storeMediaFile', props.chat.id), {
		onSuccess: () => {
			mediaFileForm.reset();
			emit('messageSent');
		},
		onError: data => {
			alert(data.file);
		}
	});
};

const onPaste = (e: ClipboardEvent) => {
	const items = e.clipboardData?.items;
	if (!items) return;
	for (let i = 0; i < items.length; i++) {
		if (items[i].type.indexOf('image') === -1) continue;
		const file = items[i].getAsFile();
		if (!file) continue;
		mediaFileForm.file = file;
		submitMediaFile();
	}
};

// TYPING LOGIC
const channel = ref<ReturnType<typeof window.Echo.private> | null>(null);
const isNyamNyamTyping = ref(false);
onMounted(() => {
	messageTextarea.value?.addEventListener('paste', onPaste);
	channel.value = window.Echo.private(`chat.${props.chat.id}`);
	channel.value
		.listenForWhisper(
			`chat.${props.chat.id}.startTyping`,
			(e: { typingUserId: number }) => {
				isNyamNyamTyping.value = e.typingUserId !== auth.user.id;
			}
		)
		.listenForWhisper(
			`chat.${props.chat.id}.stopTyping`,
			(e: { typingUserId: number }) => {
				isNyamNyamTyping.value = !(e.typingUserId !== auth.user.id);
			}
		);
});
onUnmounted(() => {
	messageTextarea.value?.removeEventListener('paste', onPaste);

	// handle channel disconnect
	channel.value
		?.stopListening(`chat.${props.chat.id}.startTyping`)
		.stopListening(`chat.${props.chat.id}.stopTyping`);
	channel.value = null;
});

const isTyping = ref(false);
const typingTimeout = ref<ReturnType<typeof setTimeout> | null>(null);
const updateTyping = () => {
	if (typingTimeout.value) {
		clearTimeout(typingTimeout.value);
	}
	isTyping.value = true;
	typingTimeout.value = setTimeout(() => {
		isTyping.value = false;
	}, 1500);
};

watch(isTyping, isTyping => {
	if (isTyping) {
		//@ts-ignore
		channel.value.whisper(`chat.${props.chat.id}.startTyping`, {
			typingUserId: auth.user.id
		});
	} else {
		//@ts-ignore
		channel.value.whisper(`chat.${props.chat.id}.stopTyping`, {
			typingUserId: auth.user.id
		});
	}
});

const submitIfSingleEnterPressed = (e: KeyboardEvent) => {
	if (e.key === 'Enter' && !e.shiftKey) {
		submit();
	}
};
onMounted(() => {
	window.addEventListener('keydown', submitIfSingleEnterPressed);
});
onUnmounted(() => {
	window.removeEventListener('keydown', submitIfSingleEnterPressed);
});

// emits
// define messageSent event
const emit = defineEmits({
	messageSent: () => true
});
</script>

<style scoped lang="scss">
.message-type-area {
	position: relative;
}
.is-typing-message {
	position: absolute;
	bottom: 100%;
	left: 8px;
	background: var(--primary-color);
	padding: 4px 8px;
	border-radius: 4px;
	color: #fff;
}
// transition
.fade-enter-active,
.fade-leave-active {
	transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
	opacity: 0;
}
</style>
