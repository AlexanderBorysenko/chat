export type TMessage = {
	id: number;
	sender_id: number;
	receiver_id: number;
	content: string;
	read: boolean;
	created_at: string;
	type: 'text' | 'media';
};
