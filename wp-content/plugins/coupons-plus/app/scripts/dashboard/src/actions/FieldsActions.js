const actions = {
    newSelectValue(value, temporaryID, propertyName) 
    {
        return {
          type: 'fields/select/update',
          payload: {
              value, 
              temporaryID, 
              propertyName
          },
        };
    },
    removeRepeaterItem(index, temporaryID, propertyName) 
    {
        return {
          type: 'fields/repeater/item/remove',
          payload: {
              index, 
              temporaryID, 
              propertyName
          },
        };
    }
};

export default actions;