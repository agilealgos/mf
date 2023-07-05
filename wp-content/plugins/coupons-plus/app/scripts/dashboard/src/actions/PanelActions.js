const actions = {
    setActivePanel(panelName) 
    {
        return {
          type: 'panel/active/set',
          payload: {
            panelName
          },
        };
    },
};

export default actions;