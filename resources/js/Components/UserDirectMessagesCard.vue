<template>
	<div class="user-card flex align-center p-8 pl-12">
		<div class="">
			<p class="uppercase fs-1-125">
				{{ user.name }}
			</p>
			<p class="fs-0-875">
				{{ user.current_user_unread_messages_count }} Нових повідомлень
			</p>
		</div>
		<Link
			class="btn w-fit-content lh-0 ml-auto flex align-center"
			:href="route('directMessages.show', user.id)"
		>
			<IoOutlinePaw class="fs-2 mr-8" />
			Жамк
		</Link>
	</div>
</template>

<script setup lang="ts">
import { TUser } from '@/types/TUser';
import { Link } from '@inertiajs/vue3';
import { IoOutlinePaw } from '@kalimahapps/vue-icons';
const props = defineProps<{
	user: TUser;
}>();
</script>

<style scoped lang="scss">
.user-card {
	background: rgba($color: #fae6fa, $alpha: 0.3);
}
</style>
