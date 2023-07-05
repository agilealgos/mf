import { copyOfTheState } from './copyOfTheState';
import Row from '../Row';
import { cloneDeep } from 'lodash';
export default class RowsReducers
{
    static getReducers()
    {
        return {
            'rows/add': RowsReducers.onNewRow,
            'rows/remove': RowsReducers.onRemoveRow,
            'rows/set': RowsReducers.onSetRows
        }
    }

    static onNewRow(state, action) 
    {
        return copyOfTheState(state, state => {
            const newRow = cloneDeep(Row.structure());

            if (action.payload.afterOptionalRowTemporaryId) {
                state.rows.splice(
                    state.rows.findIndex(row => row.temporaryID === action.payload.afterOptionalRowTemporaryId) + 1,
                    0,
                    newRow
                );
            } else {
                state.rows.push(newRow);
            }
        })
    }

    static onRemoveRow(state, action) 
    {
        return copyOfTheState(state, state => {
            state.rows = state.rows.filter(row => row.temporaryID !== action.payload.rowTemporaryId)
        })
    }

    static onSetRows(state, {payload: {rows}}) 
    {
        return copyOfTheState(state, state => {
            if (Array.isArray(rows)) {
                state.rows = rows;
            }
        })
    }
}