import { LitElement, html, css } from 'lit';
import { customElement, property } from 'lit/decorators.js'
import { DateTime } from 'luxon';

@customElement('jd-chat-message')
export class ChatMessage extends LitElement {
    static styles = [
        css`
            :host {
                display: flex;
                grid-gap: 1rem;
                font-size: var(--font-size);
            }

            jd-pseudo {
                width: 12rem;
                min-width: 12rem;
                text-align: right;
            }

            div {
                flex: 1 1 100%;
            }
        `
    ];

    @property({ type: String }) utilisateurName: string = '';
    @property({ type: String }) message: string = '';
    @property({ type: Object }) postDate: Date = new Date();

    get time(): string {
        // Passage par la Date pour sécuriser suite à l'écriture dans le DOM
        const date = DateTime.fromJSDate(new Date(this.postDate));
        return date.toFormat('HH:mm:ss');
    }

    render() {
        return html`
            <jd-pseudo name="${this.utilisateurName}"></jd-pseudo>
            <div>${this.message}</div>
            <i>${this.time}</i>
        `;
    }
}

declare global {
    interface HTMLElementTagNameMap {
        "jd-chat-message": ChatMessage;
    }
}