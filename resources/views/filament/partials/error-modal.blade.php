<style>
    [x-cloak] { display: none !important; }
</style>

<div
    x-data="{
        open: false,
        title: 'Data belum valid',
        messages: [],
        show(event) {
            this.title = event.detail.title || 'Data belum valid';
            const errors = event.detail.errors;
            this.messages = Array.isArray(errors) ? errors : (errors ? [errors] : []);
            this.open = true;
        },
    }"
    x-on:beneficiary-validation-error.window="show($event)"
    x-on:keydown.escape.window="open = false"
    x-show="open"
    x-cloak
    role="dialog"
    aria-modal="true"
    aria-labelledby="beneficiary-error-title"
    style="position: fixed; inset: 0; z-index: 60;"
>
    <div
        x-on:click="open = false"
        style="position: absolute; inset: 0; background: rgba(17, 24, 39, .6); backdrop-filter: blur(2px);"
    ></div>

    <div
        style="position: relative; margin: 8vh auto 0; width: min(92vw, 540px); max-height: 84vh; display: flex; flex-direction: column; overflow: hidden; border-radius: 14px; background: #fff; box-shadow: 0 24px 60px rgba(0, 0, 0, .3);"
    >
        <div style="display: flex; align-items: flex-start; gap: 12px; padding: 16px 18px; border-bottom: 1px solid #fecaca; background: #fef2f2;">
            <div style="display: flex; align-items: center; justify-content: center; flex: 0 0 auto; width: 34px; height: 34px; border-radius: 9999px; background: #fee2e2; color: #b91c1c; font-size: 20px; font-weight: 800;">
                !
            </div>
            <div style="flex: 1 1 auto; min-width: 0;">
                <div id="beneficiary-error-title" x-text="title" style="font-size: 15px; font-weight: 700; color: #991b1b;"></div>
                <div style="margin-top: 2px; font-size: 12px; line-height: 1.45; color: #b91c1c;">
                    Periksa kembali data berikut, lalu simpan ulang.
                </div>
            </div>
            <button
                type="button"
                x-on:click="open = false"
                aria-label="Tutup"
                style="flex: 0 0 auto; width: 30px; height: 30px; border: 0; border-radius: 8px; background: transparent; color: #991b1b; font-size: 22px; line-height: 1; cursor: pointer;"
            >&times;</button>
        </div>

        <div style="padding: 14px 18px; overflow-y: auto; background: #fff;">
            <ul style="margin: 0; padding-left: 20px; color: #374151; font-size: 13px; line-height: 1.65;">
                <template x-for="(message, index) in messages" :key="index">
                    <li x-text="message"></li>
                </template>
            </ul>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 8px; padding: 12px 18px; border-top: 1px solid #e5e7eb; background: #f9fafb;">
            <button
                type="button"
                x-on:click="open = false"
                style="padding: 8px 18px; border: 0; border-radius: 8px; background: #059669; color: #fff; font-size: 13px; font-weight: 600; cursor: pointer;"
            >
                Mengerti
            </button>
        </div>
    </div>
</div>
