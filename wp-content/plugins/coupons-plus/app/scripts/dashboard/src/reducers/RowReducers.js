import { copyOfTheStateFromTemporaryId } from './copyOfTheState';
import Column from '../Column';
import Context from '../Context';
import { merge } from 'lodash';
import ColumnsRegistrator from '../columns/ColumnsRegistrator';

export default class RowReducers
{
    static getReducers()
    {
        return {
            'column/add': RowReducers.onNewColumn,
            'after:column/add': RowReducers.onAfterColumnAdded,
            'column/remove': RowReducers.onRemoveColumn,
            'column/selector/open': RowReducers.onOpenNewColumnSelector,
            'column/selector/close': RowReducers.closeColumnSelector,
        }
    }

    static onNewColumn(state, action) 
    {
        return copyOfTheStateFromTemporaryId(action.payload.rowTemporaryId, state, rowToUpdate => {
            const columnData = merge({}, Column.structure(), {
                type: action.payload.columnType, 
                testableType: '',
                'contexts': [
                    merge({}, Context.structure())
                ]
            });

            if (action.payload.columnTemporaryId) {
                rowToUpdate.columns.splice(
                    rowToUpdate.columns.findIndex(columnData => columnData.temporaryID === action.payload.columnTemporaryId) + 1,
                    0,
                    columnData
                )
            } else {
                rowToUpdate.columns.push(columnData);
            }

        })
    }

    static onRemoveColumn(state, {payload: {rowTemporaryId, columnTemporaryId}}) 
    {
        return copyOfTheStateFromTemporaryId(rowTemporaryId, state, rowToUpdate => {
            rowToUpdate.columns = rowToUpdate.columns.filter(({temporaryID}) => temporaryID !== columnTemporaryId);
        })
    }

    static onOpenNewColumnSelector(state, {payload: columnTemporaryId}) 
    {
        return {
            ...state,
            newColumnSelector: {
                isOpen: true,
                columnTemporaryId: typeof columnTemporaryId === 'string'? columnTemporaryId : columnTemporaryId.columnTemporaryId
            }
        }
    }

    static closeColumnSelector(state, action) 
    {
        return {
            ...state,
            newColumnSelector: {
                isOpen: false,
                columnTemporaryId: ''
            }
        };
    }

    static onAfterColumnAdded(state, action) 
    {
        return {
            ...copyOfTheStateFromTemporaryId(action.payload.columnTemporaryId, state, columnToUpdate => {
                if (!action.payload.columnTemporaryId) {
                    return;
                }
                const ColumnClass = ColumnsRegistrator.getByType(columnToUpdate.type)

                // convert column to regular column if necessary
                if (ColumnClass.getMeta().isOffersColumn) {
                    const offersColumnType = ColumnsRegistrator.getByType(ColumnClass.getMeta().preferredColumnConversion)? ColumnClass.getMeta().preferredColumnConversion : CouponsPlus.components.columns.Simple.type; 
                    columnToUpdate = merge(
                        columnToUpdate, 
                        {
                            type: offersColumnType, 
                            defaultOffers: [], 
                            contexts: columnToUpdate.contexts.map(context => {
                                context.offers = [];
                                return context;
                            })
                        }
                    )
                }
            }),
            newColumnSelector: RowReducers.closeColumnSelector(state, action)
        };
    }
}