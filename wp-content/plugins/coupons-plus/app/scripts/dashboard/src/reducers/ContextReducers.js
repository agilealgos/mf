import { cloneDeep, merge } from 'lodash';
import RowsComponentsFinder from '../data/RowsComponentsFinder';
import ConditionOrFilter from '../conditionsOrFilters/ConditionOrFilter';
import Offer from '../conditionsOrFilters/Offer';
import { copyOfTheStateFromTemporaryId } from './copyOfTheState';
import {registeredConditionsAndfilters} from '../conditionsOrFilters/CardCreator';
import createCard from '../conditionsOrFilters/CardCreator';
const findContextFromColumn = (columnToUpdate, contextTemporaryID) => columnToUpdate.contexts
                                                                                    .find(({temporaryID}) => temporaryID === contextTemporaryID);
export default class ContextReducers
{
    static getReducers()
    {
        return {
            'context/conditionOrFilter/add': ContextReducers.onAddConditionOrFilter,
            'context/conditionOrFilter/remove': ContextReducers.onRemoveConditionOrFilter,

            'context/offer/add': ContextReducers.onAddOffer,
            'context/offer/remove': ContextReducers.onRemoveOffer,
        }
    }

    static onAddConditionOrFilter(state, action) 
    {
        return copyOfTheStateFromTemporaryId(action.payload.columnTemporaryId, state, columnToUpdate => {
            const conditionOrFilterData = merge({}, ConditionOrFilter.structure(), {type: action.payload.conditionOrFilterTypeName});
            const testableType = registeredConditionsAndfilters.find(
                ({testableType, ConditionOrFilterClass}) => ConditionOrFilterClass.TYPE === action.payload.conditionOrFilterTypeName
            ).testableType;

            const card = createCard(testableType, conditionOrFilterData);

            const contextToUpdate = findContextFromColumn(columnToUpdate, action.payload.contextTemporaryID);

            if (contextToUpdate.conditionsOrFilters.length === 0) {
                // this is the first condition or filter
                // we need to set the testable type on the parent column
                columnToUpdate.testableType = testableType;
            }

            contextToUpdate.conditionsOrFilters.push(card.data);
        })
    }

    static onRemoveConditionOrFilter(state, action) 
    {
        return copyOfTheStateFromTemporaryId(action.payload.columnTemporaryId, state, columnToUpdate => {
            const contextToUpdate = findContextFromColumn(columnToUpdate, action.payload.contextTemporaryID);

            contextToUpdate.conditionsOrFilters = contextToUpdate.conditionsOrFilters
                                                                 .filter(conditionOrFilter => conditionOrFilter.temporaryID !== action.payload.conditionOrFilterTemporaryId);
        })
    }

    static onAddOffer(state, {payload: {columnTemporaryId, contextTemporaryID, OfferTypeName}}) 
    {
        return copyOfTheStateFromTemporaryId(columnTemporaryId, state, columnToUpdate => {
            const offerData = merge({}, Offer.structure() , {type: OfferTypeName});

            const card = createCard('offers', offerData);

            const contextToUpdate = findContextFromColumn(columnToUpdate, contextTemporaryID);

            contextToUpdate.offers.push(card.data);

            /**
             * ColumnReducers will hook to this action after it's been executed
             * 
             * And will convert the column type and/or add the offer to the default offers for the column 
             * if necessary
             *
             * This is very important.
             */
        })
    }

    static onRemoveOffer(state, {payload: {columnTemporaryId, contextTemporaryID, offerTemporaryID}}) 
    {
        return copyOfTheStateFromTemporaryId(columnTemporaryId, state, columnToUpdate => {
            const contextToUpdate = findContextFromColumn(columnToUpdate, contextTemporaryID);

            if (contextToUpdate) {
                contextToUpdate.offers.splice(
                    contextToUpdate.offers.findIndex(offer => offer.temporaryID === offerTemporaryID),
                    1
                );
            }

            /**
             * ColumnReducers will hook to this action after it's been executed
             * 
             * And will convert the column type and/or remove the offer from the default offers for the column 
             * if necessary
             *
             * This is very important.
             */
        })
    }
}