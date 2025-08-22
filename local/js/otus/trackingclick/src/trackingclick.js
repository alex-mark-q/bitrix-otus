import { Type } from 'main.core';
import {ClickBuffer} from './helper/buffer.js';

export class TrackingClick {
    constructor() {
        this.buffer = new ClickBuffer();
    }

    setName(name) {
        if (Type.isString(name)) {
            this.name = name;
        }
    }

    getName() {
        return this.name;
    }

    init() {
        document.addEventListener('click', this.handleClick.bind(this), true);
        window.addEventListener('online', () => this.buffer.flush());
    }

    handleClick(event) {
        const target = event.target;
        const isLink = target.closest('a');

        this.buffer.addClick({
            element: {
                tag: target.tagName,
                id: target.id,
                class: target.className,
                href: isLink ? isLink.href : null,
                text: target.textContent?.trim()
            },
            position: {
                x: event.clientX,
                y: event.clientY
            },
            meta: {
                isTrusted: event.isTrusted,
                ctrlKey: event.ctrlKey,
                shiftKey: event.shiftKey
            }
        });
		// console.log({
        //     element: {
        //         tag: target.tagName,
        //         id: target.id,
        //         class: target.className,
        //         href: isLink ? isLink.href : null,
        //         text: target.textContent?.trim()
        //     },
        //     position: {
        //         x: event.clientX,
        //         y: event.clientY
        //     },
        //     meta: {
        //         isTrusted: event.isTrusted,
        //         ctrlKey: event.ctrlKey,
        //         shiftKey: event.shiftKey
        //     }
        // });
    }
}
