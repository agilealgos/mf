import React, {Component} from 'react';
import ContextsGroup from '../../ContextsGroup';
import ORColumn from '../ORColumn';

export default class OROffersColumn extends ORColumn
{
    static structure = {};

    static TYPE = CouponsPlus.components.columns.OROffers.type;

    static getMeta() 
    {
        return CouponsPlus.components.columns.OROffers.meta;
    }
 }