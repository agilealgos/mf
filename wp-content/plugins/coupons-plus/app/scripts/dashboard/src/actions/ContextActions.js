const actions = {
    newConditionOrFilter(conditionOrFilterTypeName, contextTemporaryID, columnTemporaryId) 
    {
        return {
          type: 'context/conditionOrFilter/add',
          payload: {
              conditionOrFilterTypeName,
              contextTemporaryID,
              columnTemporaryId
          },
        };
    },

    removeConditionOrFilter(conditionOrFilterTemporaryId, contextTemporaryID, columnTemporaryId) 
    {
        return {
          type: 'context/conditionOrFilter/remove',
          payload: {
              conditionOrFilterTemporaryId,
              contextTemporaryID,
              columnTemporaryId
          },
        };
    },

    newOffer(columnTemporaryId, contextTemporaryID, OfferTypeName) 
    {
        return {
          type: 'context/offer/add',
          payload: {
              columnTemporaryId,
              contextTemporaryID,
              OfferTypeName
          },
        };
    },

    removeOffer(columnTemporaryId, contextTemporaryID, offerTemporaryID) 
    {
        return {
          type: 'context/offer/remove',
          payload: {
              columnTemporaryId,
              contextTemporaryID,
              offerTemporaryID
          },
        };
    },
};

export default actions;