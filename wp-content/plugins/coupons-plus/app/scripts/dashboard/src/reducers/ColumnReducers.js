import { copyOfTheStateFromTemporaryId } from './copyOfTheState';
import Context from '../Context';
import Offer from '../conditionsOrFilters/Offer';
import { merge } from 'lodash';
import ColumnsRegistrator from '../columns/ColumnsRegistrator';
import createCard from '../conditionsOrFilters/CardCreator';

const addDefaultContext = columnToUpdate => {
    const contextData = merge({}, Context.structure());

    columnToUpdate.contexts.push(contextData);
}
export default class ColumnReducers
{
    static getReducers()
    {
        return {
            'context/add': ColumnReducers.onNewContext,
            'context/remove': ColumnReducers.onRemoveContext,

            'column/offer/add': ColumnReducers.onNewDefaultOffer,
                'after:column/offer/add': ColumnReducers.onDefaultOfferAdderToColumn,
            'column/offer/remove': ColumnReducers.onRemoveDefaultOffer,

            'after:context/offer/add': ColumnReducers.onOfferAddedToContext,
            'after:context/offer/remove': ColumnReducers.onOfferRemovedFromContext,
            '__AFTER_STATE_CHANGE__': ColumnReducers.onStateChange,
        }
    }

    static onNewContext(state, action) 
    {
        return copyOfTheStateFromTemporaryId(action.payload.columnTemporaryId, state, columnToUpdate => {
            addDefaultContext(columnToUpdate);
        })
    }

    static onRemoveContext(state, {payload: {columnTemporaryId, contextTemporaryID}}) 
    {
        return copyOfTheStateFromTemporaryId(columnTemporaryId, state, columnToUpdate => {
            //const contextData = merge({}, Context.structure());

            columnToUpdate.contexts = columnToUpdate.contexts.filter(contextData => contextData.temporaryID !== contextTemporaryID);

            if (!columnToUpdate.contexts.length) {
                addDefaultContext(columnToUpdate)
            }
        })
    }

    static onNewDefaultOffer(state, {payload: {columnTemporaryId, OfferTypeName}}) 
    {
        return copyOfTheStateFromTemporaryId(columnTemporaryId, state, columnToUpdate => {
            const offerData = merge({}, Offer.structure(), {type: OfferTypeName});

            const card = createCard('offers', offerData);

            columnToUpdate.defaultOffers.push(card.data);

            /**
             * HOOK NOTICE
             * 
             * ColumnReducers (self) will hook to this action after it's been executed
             *
             * And will convert the column type if need be.
             */
        })
    }

    static onDefaultOfferAdderToColumn(state, {payload: {columnTemporaryId, OfferTypeName}}) 
    {
        return copyOfTheStateFromTemporaryId(columnTemporaryId, state, columnToUpdate => {
            const columnMeta = CouponsPlus.components.columns[columnToUpdate.type].meta;

            // it's not an offers column and we just added and offer to it!
            if (!columnMeta.isOffersColumn) {
                columnToUpdate.type = columnMeta.preferredColumnConversion || CouponsPlus.components.defaultColumnTypes.offers;
            }
        })
    }

    static onRemoveDefaultOffer(state, {payload: {columnTemporaryId, offerTemporaryID}}) 
    {
        return copyOfTheStateFromTemporaryId(columnTemporaryId, state, columnToUpdate => {
            columnToUpdate.defaultOffers = columnToUpdate.defaultOffers.filter(offerData => offerData.temporaryID !== offerTemporaryID);
        })
    }

    static onStateChange(state, {type, payload: {columnTemporaryId, contextTemporaryID}}) 
    {
        const resetColumnTestableTypeWhenNoConditionsOrFilters = (columnTemporaryId, contextTemporaryID) => copyOfTheStateFromTemporaryId(columnTemporaryId, state, columnToUpdate => {

                    const allConditionsOrFiltersForTheEntireColumnAcrossAllContexts = columnToUpdate.contexts.map(({conditionsOrFilters}) => conditionsOrFilters.length).reduce((previous, current) => previous + current);
                    if (allConditionsOrFiltersForTheEntireColumnAcrossAllContexts === 0) {
                        // we need to reset the column's testable type if we have no more
                        // conditions or filters after removal
                        columnToUpdate.testableType = ''
                    }            
                });

        switch (type) {
            case 'context/conditionOrFilter/remove':
            case 'context/remove':
                return resetColumnTestableTypeWhenNoConditionsOrFilters(columnTemporaryId, contextTemporaryID)
            break;
        }

        return state;
    }

    static onOfferAddedToContext(state, {payload: {columnTemporaryId, contextTemporaryID, OfferTypeName}}) 
    {
        return copyOfTheStateFromTemporaryId(columnTemporaryId, state, columnToUpdate => {
            const ColumnClass = ColumnsRegistrator.getByType(columnToUpdate.type)

            // convert column to offers column if necessary
            if (!ColumnClass.getMeta().isOffersColumn) {
                const offersColumnType = ColumnsRegistrator.getByType(ColumnClass.getMeta().preferredColumnConversion)? ColumnClass.getMeta().preferredColumnConversion : CouponsPlus.components.columns.SimpleOffer.type; 
                columnToUpdate = merge(columnToUpdate, {type: ColumnClass.getMeta().preferredColumnConversion})
            }


            if (ColumnsRegistrator.getByType(columnToUpdate.type).getMeta().useOneOffersSetForAllContexts) {
                const offerData = merge({}, Offer.structure() , {type: OfferTypeName});

                columnToUpdate.defaultOffers.push(offerData)
                columnToUpdate.contexts = columnToUpdate.contexts.map(context => ({
                    ...context,
                    offers: []
                }))
            }
        })
    } 

    static onOfferRemovedFromContext(state, {payload: {columnTemporaryId, contextTemporaryID, offerTemporaryID}}) 
    {
        return copyOfTheStateFromTemporaryId(columnTemporaryId, state, columnToUpdate => {
            const ColumnClass = ColumnsRegistrator.getByType(columnToUpdate.type)

            // remove the offer from the default offers if one offersset for all contexts
            if (ColumnsRegistrator.getByType(columnToUpdate.type).getMeta().useOneOffersSetForAllContexts) {
                columnToUpdate.defaultOffers = columnToUpdate.defaultOffers.filter(offer => offer.temporaryID !== offerTemporaryID);
            }
        })
    }   
}