import { TMessage } from './TMessage';
import { TUser } from './TUser';

export type TChat = {
	id: number;
	chat_name: string;
	unread_messages_count: number;
	users: TUser[];
	messages?: TMessage[];
};
