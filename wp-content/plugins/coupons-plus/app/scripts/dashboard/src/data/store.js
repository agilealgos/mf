import { createStore } from 'redux';
import RowsReducers from '../reducers/RowsReducers';
import RowReducers from '../reducers/RowReducers';
import ColumnReducers from '../reducers/ColumnReducers';
import ContextReducers from '../reducers/ContextReducers';
import FieldsReducers from '../reducers/FieldsReducers';
import PanelReducers from '../reducers/PanelReducers';
import _ from 'lodash';
import Dashboard from '../Dashboard';
import RowsMapper from './RowsMapper';

const rowsMapper = new RowsMapper(Dashboard.defaultState.rows);

const defaultState = {
    rows: rowsMapper.map(),
    newColumnSelector: {
        isOpen: false,
        columnTemporaryId: ''
    }
};

const reducer = (state = defaultState, action) => {
    const registeredReducers = [
        RowsReducers.getReducers(),
        RowReducers.getReducers(),
        ColumnReducers.getReducers(),
        ContextReducers.getReducers(),
        FieldsReducers.getReducers(),
        PanelReducers.getReducers(),
    ];
    const actionTypeReducersMap = {};

    for (let reducerGroup of registeredReducers) {
        for (let actionType in reducerGroup) {
            if (typeof actionTypeReducersMap[actionType] === 'undefined') {
                actionTypeReducersMap[actionType] = [];
            }
            
            actionTypeReducersMap[actionType].push(reducerGroup[actionType]);            
        }
    }

    let newState = _.cloneDeep(state);

    const executeReducers = (reducers, newState, action) => {
        for (let reducer of reducers) {
            newState = reducer(newState, action);
        }

        return newState;
    };

    if (!action.type.startsWith('@@')) {
        newState = executeReducers(actionTypeReducersMap[action.type], newState, action)

        const afterActionReducers = actionTypeReducersMap[`after:${action.type}`];

        if (afterActionReducers?.length) {
            newState = executeReducers(afterActionReducers, newState, action);
        }

        const afterStateChange = actionTypeReducersMap['__AFTER_STATE_CHANGE__'];

        if (afterStateChange?.length) {
            newState = executeReducers(afterStateChange, newState, action);
        }
        /*
        for (let reducer of actionTypeReducersMap[action.type]) {
            newState = reducer(newState, action);

            if (actionTypeReducersMap[action.type].length) {

            }
        }*/
    }
    return newState;
}

export default createStore(reducer, window.__REDUX_DEVTOOLS_EXTENSION__ && window.__REDUX_DEVTOOLS_EXTENSION__({trace:true}));