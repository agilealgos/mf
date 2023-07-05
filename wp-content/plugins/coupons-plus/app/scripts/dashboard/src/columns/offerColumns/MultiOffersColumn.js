import React, {Component} from 'react';
import ContextsGroup from '../../ContextsGroup';

export default class MultiOffersColumn extends Component
{
    static structure = {};

    static TYPE = CouponsPlus.components.columns.MultiOffers.type;

    static getMeta() 
    {
        return CouponsPlus.components.columns.MultiOffers.meta;
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
                    <div className="w-full text-gray-500 flex flex-row justify-between items-center space-x-2"> 
                        <div className="w-full h-px border-t-2 border-dashed border-gray-350"></div> 
                        <div className="w-full h-px border-t-2 border-dashed border-gray-350"></div>
                    </div>
                )}
            />
        );
    }
}