import getSymbolFromCurrency from 'currency-symbol-map'
import React, {Component} from 'react';

export default class Currency extends Component
{
    render() 
    {
        return (getSymbolFromCurrency(CouponsPlus.woocommerce.currency))
    }
}