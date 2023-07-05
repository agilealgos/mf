import { copyOfTheState } from './copyOfTheState';
import CouponPanel from '../CouponPanel';

export default class PanelReducers
{
    static getReducers()
    {
        return {
            'panel/active/set': PanelReducers.onActivePanelSet,
            'after:rows/set': PanelReducers.afterRowsHaveBeenSet
        }
    }

    static onActivePanelSet(state, {payload: {panelName}}) 
    {
        return copyOfTheState(state, state => {
            state.selectedPanel = panelName
        })
    }

    static afterRowsHaveBeenSet(state, {payload: {source}}) 
    {
        window.scrollTo(0, 0);
        
        return copyOfTheState(state, state => {
            if (source === 'from-preset') {
                state.selectedPanel = CouponPanel.label
            }
        })
    }
}