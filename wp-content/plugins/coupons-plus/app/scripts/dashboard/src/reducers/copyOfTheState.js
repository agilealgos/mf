import RowsComponentsFinder from '../data/RowsComponentsFinder';
import { cloneDeep } from 'lodash';

const copyOfTheStateFromTemporaryId = (temporaryID, state, updater) => {
    // we're cloning the main state, VERY important so that 
    // changes are made inmutably (I assume for the view to update correctly)
    const copyOfTheSate = cloneDeep(state);
    const rowsComponentsFinder = new RowsComponentsFinder(copyOfTheSate.rows);
    const objectToUpdate = rowsComponentsFinder.find(temporaryID);

    updater(objectToUpdate);

    return copyOfTheSate;
}

const copyOfTheState = (state, updater) => {
    // we're cloning the main state, VERY important so that 
    // changes are made inmutably (I assume for the view to update correctly)
    const copyOfTheSate = cloneDeep(state);

    updater(copyOfTheSate);

    return copyOfTheSate;
}

export {
    copyOfTheState,
    copyOfTheStateFromTemporaryId
};