import React, {Component} from 'react';
import { Tab } from '@headlessui/react';
import CouponPanel from './CouponPanel';
import PresetsPanel from './PresetsPanel';
import { connect } from "react-redux";
import { merge } from 'lodash';
import PanelActions from './actions/PanelActions';
import { addProductLabels, addProductsWithIds } from './helpers/ProductsManager';

import "react-datepicker/dist/react-datepicker.css";

const {setActivePanel} = PanelActions;

class Dashboard extends Component
{
    static defaultState = {
        "rows": merge([], CouponsPlus.options.rows),
        newColumnSelector: {
            isOpen: false,
            columnTemporaryId: ''
        },
        selectedPanel: CouponPanel.label
    };

    static mapStateToProps(state, props) {
        return merge({
            selectedPanel: state.selectedPanel?? CouponPanel.label,
        })
    };

    panels = [
        CouponPanel,
        PresetsPanel
    ];

    constructor() 
    {
        super();
        $('body').attr('id', 'coupons-plus-admin');

        addProductLabels(CouponsPlus.products.default.labels);
        addProductsWithIds(CouponsPlus.products.default.idsWithVariations);
        this.setGlobals()
    }

    setGlobals() 
    {
        /**
         * Because countries and states are such a big data, we'll just map 'em once and add it in a global variable.
         */
         const mapTerritory = territoryType => {
            let countriesOrStates = territoryType === 'country'? CouponsPlus.places.countries : CouponsPlus.places.states;
            return Object.keys(countriesOrStates)
                         .map(code => ({value: code, label: countriesOrStates[code]}))    
         }

         CouponsPlus.mappedTerritories = {
            country: mapTerritory('country'),
            state: mapTerritory('state')
         }
    }

    render() 
    {
        return (
            <div className="cp-dashboard text-gray-400 w-full h-full flex flex-col">
                <div className="text-2x text-gray-600 flex flex-row">
                    {this.panels.map(Panel => {
                        return (
                            <button 
                                key={Panel.label} 
                                className="inline-flex flex-row items-center justify-center space-x-2 h-12 px-5 capitalize hover:cursor-pointer"
                                onClick={() => this.props.setActivePanel(Panel.label)}
                            >
                                <span>{Panel.getIcon()}</span><span className="flex h-5">{Panel.label}</span>
                            </button>
                        )
                    })}
                </div>
                <div>
                    <div className="mt-4">
                        {this.panels.filter(Panel => Panel.label == this.props.selectedPanel).map(Panel => <Panel key={Panel.label}/>)}
                    </div>
                </div>
            </div>
        )
    }
}

export default connect(Dashboard.mapStateToProps, {setActivePanel})(Dashboard);