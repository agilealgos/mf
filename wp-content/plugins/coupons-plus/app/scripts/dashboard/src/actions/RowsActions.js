const actions = {
    newRow(afterOptionalRowTemporaryId) 
    {
        return {
          type: 'rows/add',
          payload: {
            afterOptionalRowTemporaryId
          },
        };
    },

    removeRow(rowTemporaryId) 
    {
        return {
          type: 'rows/remove',
          payload: {
            rowTemporaryId
          },
        };
    },

    removeConditionOrFilter(conditionOrFilterTemporaryId, contextTemporaryID) 
    {
        return {
          type: 'rows/remove',
          payload: {
              conditionOrFilterTemporaryId,
              contextTemporaryID
          },
        };
    },

    setRows(rows: array, source: string) 
    {
        return {
          type: 'rows/set',
          payload: {
            rows,
            source
          },
        };
    },
};

export default actions;