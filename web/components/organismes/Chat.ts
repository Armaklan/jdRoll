import { LitElement, html, css } from 'lit';
import { customElement, property } from 'lit/decorators.js'

export interface Message {
    id: Number;
    to: string;
    from: string;
    text: string;
    time: Date;
    private: boolean;
}

@customElement('jd-chat')
export class Chat extends LitElement {
    static styles = [
        css`
            :host {
                display: flex;
                flex-direction: column;
                grid-gap: 1rem;            }
        `
    ];

    @property({ type: Array }) messages: Message[] = [];

    render() {
        return html`${this.messages.map(this.renderMessage)}`;
    }

    renderMessage(message: Message) {
        return html`<jd-chat-message utilisateurName=${message.from} message=${message.text} .postDate=${message.time}></jd-chat-message>`
    }
}
