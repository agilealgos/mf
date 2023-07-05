import React, {Component} from 'react';
import { Switch } from '@headlessui/react'

export default class AutoApplyOptions extends Component
{
    elements = {
        autoApplyInput: $('#coupons-plus-coupon-auto-apply-is-enabled')
    }

    autoApplyIsChecked() 
    {
        return this.elements.autoApplyInput.val() === 'yes'
    }

    render() 
    {
        return (
            <div className="flex flex-row items-center rounded-3 bg-gray-100 p-6 space-x-4">
                <div className="flex flex-col">
                    <h1>{__('Apply coupon automatically? (BETA)', CouponsPlus.textDomain)}</h1>
                    <p>{__('Applies the coupon automatically to the cart when the requirements are met.', CouponsPlus.textDomain)}</p>
                </div>
                <Switch
                    checked={this.autoApplyIsChecked.bind(this)}
                    onChange={() => {
                        this.elements.autoApplyInput.val(this.autoApplyIsChecked()? 'no' : 'yes')
                        this.forceUpdate()
                    }}
                    className={`${this.autoApplyIsChecked() ? 'bg-blue-normal' : 'bg-gray-400'}
                        relative inline-flex flex-shrink-0 h-6 w-10 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus-visible:ring-2  focus-visible:ring-white focus-visible:ring-opacity-75`}
                    >
                    <span
                        aria-hidden="true"
                        className={`${this.autoApplyIsChecked() ? 'translate-x-4' : 'translate-x-0'}
                        pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow-lg transform ring-0 transition ease-in-out duration-200`}
                    />
                </Switch>
            </div>
        )
    }
}