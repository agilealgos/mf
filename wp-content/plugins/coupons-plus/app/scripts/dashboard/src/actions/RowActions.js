const actions = {
    newColumn(rowTemporaryId, columnTemporaryId, columnType) 
    {
        return {
            type: 'column/add',
            payload: {
                rowTemporaryId, 
                columnTemporaryId,
                columnType
            },
        };
    },
    removeColumn(rowTemporaryId, columnTemporaryId) 
    {
        return {
            type: 'column/remove',
            payload: {
                rowTemporaryId,
                columnTemporaryId
            },
        };
    },
    openNewColumnSelector(columnTemporaryId) 
    {
        return {
            type: 'column/selector/open',
            payload: {
                columnTemporaryId
            },
        };
    },
    closeNewColumnSelector(columnTemporaryId) 
    {
        return {
            type: 'column/selector/close',
            payload: {},
        };
    },
};

export default actions;