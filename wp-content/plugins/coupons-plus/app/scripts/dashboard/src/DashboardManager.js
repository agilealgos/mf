import { merge } from 'lodash';

export class DashboardManager
{
    state = {
        view: 'coupons+' // or 'classic'
    };

    constructor(store) 
    {
        // needs be added before any props are set
        $('#wpbody-content').append('<div id="coupons-plus"></div>');

        this.originalHeadingHTML = $('.woocommerce-layout__header-heading').html();
        this.store = store;
        this.couponsPlusElement = $('#coupons-plus');

        $(window).on('load', this.onLoad.bind(this));

        $('form#post').append(`
                                <input type="hidden" id="coupons-plus-rows" name="couponsplus_rows" value="jkgf"/>
                                <input type="hidden" id="coupons-plus-coupon-auto-apply-is-enabled" name="couponsplus_coupon_auto_apply_is_enabled" value="${CouponsPlus.options.coupon_auto_apply_is_enabled? 'yes' : 'no'}"/>
                                <input type="hidden" id="coupons-plus-dashboard-nonce" name="couponsplus_dashboard_nonce" value="${CouponsPlus.security.nonces.dashboard}"/>
                            `);
    }

    onLoad() 
    {
        this.registerEvents();
        this.switchToCouponsPlus();       
    }

    changeState(data) 
    {
        this.state = merge(this.state, data);

        this.onUpdate();
    }

    registerEvents() 
    {
        $('#coupons-plus-admin').on('click', 'button.cp-view-switch-to-classic', this.changeState.bind(this, {
            view: 'classic'
        })).on('click', 'button.cp-view-switch-to-coupons-plus', this.changeState.bind(this, {
            view: 'coupons+'
        })).on('click', 'button.cp-save', this.onSave.bind(this))
           .on('change', 'input#cp-coupon-code-mirror', this.updateCodeInputValue.bind(this, {fieldToUpdate: 'classic'}))
           .on('click', 'button.cp-export', this.outputExportDev.bind(this))
    }

    onSave() 
    {
        this.updateRowsInputField();
        $('input#publish').trigger('click');
    }

    updateRowsInputField() 
    {
        $('input#coupons-plus-rows').val(JSON.stringify(this.store.getState()))
    }

    onUpdate() 
    {
        switch (this.state.view) {
            case 'classic':
                this.switchToClassic();
            break;

            case 'coupons+':
                this.switchToCouponsPlus();
            break;
        }
    }

    switchToCouponsPlus() 
    {
        this.couponsPlusElement.css('display', 'block');

        $('.woocommerce-layout__header-heading').html(`
            <button class="cp-view-switch-to-classic flex flex-row items-center space-x-2 px-5 ml-[calc(var(--large-gap)*-1)] h-full border-r-px border-gray-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>
                    ${__('Back to Classic', CouponsPlus.textDomain)}
                </span>
            </button>
            <div class="flex flex-row relative ml-5 items-center space-x-1 bg-gray-200 rounded-3 h-10 px-1">
                <span class="flex flex-row items-center uppercase text-smaller-1 bg-gray-100 text-gray-450 h-7 px-1 rounded-4">${__('Code', CouponsPlus.textDomain)}</span>
                <input type="text" id="cp-coupon-code-mirror" class="border-none shadow-none bg-transparent"/>
            </div>
            <button class="cp-save flex flex-row space-x-2 leading-5 items-center ml-6 h-9 px-3 rounded-3 bg-gray-700 ring-gray-400 text-gray-100 capitalize font-light ring ring-[4px]">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01" />
                </svg>
                <span>${ __('save', CouponsPlus.textDomain) }</span>
            </button>
        `) 

        if (process.env.NODE_ENV === "development") {
            /**
             * Only available in the dev builds, 
             * but it is very likey to be added to production
             * builds when we introduce import & export functionality
             */
            $('.woocommerce-layout__header-heading').append(`
                <button class="cp-export">Export</button>
            `)
        }

        this.updateCodeInputValue({fieldToUpdate: 'coupons+'});
    }

    updateCodeInputValue({fieldToUpdate}) 
    {
        switch(fieldToUpdate) {
            case 'coupons+':
                $('#cp-coupon-code-mirror').val($('input[name="post_title"]').val());
            break;
            case 'classic':
                let mirrorCodevalue;

                if (mirrorCodevalue = $('#cp-coupon-code-mirror').val()) {
                    $('input[name="post_title"]').val(mirrorCodevalue)
                }
            break;
        }
    }

    switchToClassic() 
    {
        // needs be called before we replace the html with the classic header
        this.updateCodeInputValue({fieldToUpdate: 'classic'});
        this.updateRowsInputField();

        this.couponsPlusElement.css('display', 'none');        

        $('.woocommerce-layout__header-heading').html(`
            ${this.originalHeadingHTML}
            <button class="cp-view-switch-to-coupons-plus flex flex-col leading-5 items-start justify-center ml-6 h-10 px-4 rounded-4 bg-blue-normal text-gray-100">
                        <span class="font-light text-smaller-1 leading-[10px]">${__('Edit with', CouponsPlus.textDomain)}</span>
                        <span class="leading-4">Coupons+</span>
                </button>
        `)

    }

    outputExportDev() 
    {
        console.log('Exported:');
        console.log(JSON.stringify(this.store.getState().rows));
    }
}