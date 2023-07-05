import React, {Component} from 'react';
import ContextsGroup from '../ContextsGroup';
import ColumnsSeparator from '../ColumnsSeparator';
import RightColumnOptions from '../RightColumnOptions';
import OffersSet from '../OffersSet';

export default class ANDColumn extends Component
{
    static structure = {};

    static TYPE = CouponsPlus.components.columns.AND.type;

    static getMeta() 
    {
        return CouponsPlus.components.columns.AND.meta;
    }
 
    render() 
    {
        return (
            <ColumnsSeparator
                columnNumber={this.props.columnNumber}
                singleColumn={this.props.hasMoreColumnsToTheRight}
                isTheFirstColumn={this.props.isTheFirstColumn}
                alignCenter={!this.props.columnData.defaultOffers.length}
                leftColumn={() => (
                    <ContextsGroup 
                        isTheFirstColumn={this.props.isTheFirstColumn}
                        hasMoreColumnsToTheRight={this.props.hasMoreColumnsToTheRight}
                        contextComponents={this.props.getContextComponents({
                            disableMarginRight: true
                        })} 
                        disableColumnsSeparatorMarginRight={true}
                        disableNumeration={true}
                        columnData={this.props.columnData}
                        afterGroup={() => (
                            <div className="w-full text-gray-400 flex flex-row justify-between items-center"> 
                                <div className="w-full h-px border-t-2 border-dashed border-gray-500"></div> 
                                <span className="flex text-gray-150 leading-5 px-2 rounded-3 bg-gray-500">{__('AND', CouponsPlus.textDomain)}</span> 
                                <div className="w-full h-px border-t-2 border-dashed border-gray-400"></div>
                            </div>
                        )}
                    />
                )}
                rightColumn={() => {
                    return this.props.columnData.defaultOffers.length ? (
                        <OffersSet offersSetRelation="column"
                                   offersData={this.props.columnData.defaultOffers} 
                                   columnId={this.props.columnData.temporaryID} />
                    ) : (
                        <RightColumnOptions 
                            offersSetRelation="column"
                            columnTemporaryId={this.props.columnData.temporaryID}
                            offersData={this.props.columnData.defaultOffers}
                        />
                    )
                }}
            />
        );
    }
}