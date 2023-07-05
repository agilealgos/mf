const actions = {
    newContext(columnTemporaryId) 
    {
        return {
            type: 'context/add',
            payload: {
                columnTemporaryId
            },
        };
    },

    removeContext(columnTemporaryId, contextTemporaryID) 
    {
        return {
            type: 'context/remove',
            payload: {
                columnTemporaryId,
                contextTemporaryID
            },
        };
    },

    newOffer(columnTemporaryId, OfferTypeName) 
    {
        return {
            type: 'column/offer/add',
            payload: {
                columnTemporaryId,
                OfferTypeName
            },
        };
    },

    removeOffer(columnTemporaryId, offerTemporaryID) 
    {
        return {
            type: 'column/offer/remove',
            payload: {
                columnTemporaryId,
                offerTemporaryID
            },
        };
    },
};

export default actions;