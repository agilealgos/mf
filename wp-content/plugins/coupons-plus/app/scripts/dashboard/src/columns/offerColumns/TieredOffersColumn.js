import React, {Component} from 'react';
import ContextsGroup from '../../ContextsGroup';

export default class TieredOffersColumn extends Component
{
    static structure = {};

    static TYPE = CouponsPlus.components.columns.TieredOffers.type;

    static getMeta() 
    {
        return CouponsPlus.components.columns.TieredOffers.meta;
    }
 
    render() 
    {
        return (
            <ContextsGroup 
                columnNumber={this.props.columnNumber}
                hasMoreColumnsToTheRight={this.props.hasMoreColumnsToTheRight}
                isTheFirstColumn={this.props.isTheFirstColumn}
                contextComponents={this.props.getContextComponents({
                    disableMarginRight: true
                })}
                columnData={this.props.columnData}
                individualContextSelector={true}
                afterGroup={() => (
                    <div className="w-full text-gray-500 flex flex-row justify-between items-center"> 
                        <div className="w-full h-px border-t-2 border-dashed border-gray-500"></div> 
                        <span className="flex text-gray-150 leading-5 px-2 rounded-3 bg-gray-500">{__('OR', CouponsPlus.textDomain)}</span> 
                        <div className="w-full h-px border-t-2 border-dashed border-gray-500"></div>
                    </div>
                )}
            />
        );
    }
}