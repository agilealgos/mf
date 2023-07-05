import React, {Component} from 'react';
import ContextsGroup from '../../ContextsGroup';
import ANDColumn from '../ANDColumn';

export default class ANDOffersColumn extends ANDColumn
{
    static structure = {};

    static TYPE = CouponsPlus.components.columns.ANDOffers.type;

    static getMeta() 
    {
        return CouponsPlus.components.columns.ANDOffers.meta;
    }
 }