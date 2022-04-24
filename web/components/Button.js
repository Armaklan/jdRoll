import { LitElement, html, css } from "lit";

export class Button extends LitElement {
  static styles = css`
    .storybook-button {
      font-family: "Nunito Sans", "Helvetica Neue", Helvetica, Arial, sans-serif;
      font-weight: 700;
      border: 0;
      border-radius: 3em;
      cursor: pointer;
      display: inline-block;
      line-height: 1;
    }
    .storybook-button--primary {
      color: white;
      background-color: #1ea7fd;
    }
    .storybook-button--medium {
      font-size: 14px;
      padding: 11px 20px;
    }
  `;

  static properties = {
    label: {}
  };

  render() {
    return html`
      <button
        type="button"
        class="storybook-button storybook-button--medium storybook-button--primary"
      >
        ${this.label}
      </button>
    `;
  }
}

customElements.define('jd-button', Button);