import React, {Component} from 'react';
import SimpleColumn from '../SimpleColumn';
export default class SimpleOffersColumn extends SimpleColumn
{
    static structure = {};

    static TYPE = CouponsPlus.components.columns.SimpleOffer.type;

    static getMeta() 
    {
        return CouponsPlus.components.columns.SimpleOffer.meta;
    }
}