import { LitElement, html, css } from 'lit';
import { customElement, property } from 'lit/decorators.js';

@customElement('jd-pseudo')
export class Pseudo extends LitElement {
  static styles = css`
    a {
      text-decoration: none;
      font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
      font-size: var(--font-size);
    }

    .user {
      color: var(--user-color);
    }

    .admin {
      color: var(--admin-color);
    }

    .prestige {
      color: var(--prestige-color);
    }
  `;
  
  @property({type: String})
  name: string = '';

  @property({ type: Number })
  statut: number = 0;

  get statutClass(): string {
    const classStatut = ['user', 'prestige', 'admin'];
    return classStatut[this.statut];
  }

  render() {
    return html`
      <a
        href="profile/${this.name}"
        class="${this.statutClass}"
      >
        ${this.name}
      </a>
    `;
  }
}

declare global {
  interface HTMLElementTagNameMap {
    "jd-pseudo": Pseudo;
  }
}